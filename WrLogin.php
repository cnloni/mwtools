<?php
	require_once 'MwWeeklyReport.php';
	require_once 'MwAgent.php';
	require_once 'MwCurlClient.php';

	// ClientConfig.phpを探す
	$base = '/config/ClientConfig.php';
	$config = null;
	foreach (['..', '.'] as $dir) {
		$configFile = __DIR__ . '/' . $dir . $base;
		if (file_exists($configFile)) {
			$config = require_once $configFile;
			break;
		}
	}
	if ($config == null) {
		print("ClientConfig.php not found.");
		exit(1);
	}

	/*
		現在の週番号を計算し、週報のURLを求めてRedirectする。
		ページが無い場合は作成する。
		GET変数diffが設定されている時は、週番号にdiffを加える。
		Request2Clientを使用する。
	*/
	try {
		$client = new MwCurlClient($config);
		$agent = new MwAgent($client);
		$agent->clientLogin(
			$config['username'],
			$config['password'],
			$config['url']
		);

		$diff = isset($_GET['diff'])?$_GET['diff']:0;
		$title = MwWeeklyReport::getTitle($diff);
		//$title = '週報:てすと';
		if ($diff <= 0 && $agent->existsPage($title) == false) {
			//今週(または過去)のページが無いなら、あらかじめ作成する
			$text = MwWeeklyReport::getBlankPage($diff);
			$agent->createNewPage($title, $text);
		}
		header('Location: ' . $agent->getUrl($config['url'], $title));
	} catch(Exception $e) {
		print($e->getMessage() . "\n");
		exit(1);
	}
