<?php
	require_once 'MwWeeklyReport.php';
	require_once 'MwOAuthOwnerOnlyClient.php';
	require_once 'MwAgent.php';

	// XXConfig.phpを探す
	$base = 'config/OAuthOwnerOnlyConfig.php';
	$config = null;
	foreach (['..', '.'] as $dir) {
		$configFile = __DIR__ . '/' . $dir . '/' . $base;
		if (file_exists($configFile)) {
			$config = require_once $configFile;
			break;
		}
	}
	if ($config == null) {
		print($base . " not found.");
		exit(1);
	}

	/*
		現在の週番号を計算し、週報のURLを求めてRedirectする。
		ページが無い場合は作成する。
		GET変数diffが設定されている時は、週番号にdiffを加える。
	*/
	try {
		$client = new MwOAuthOwnerOnlyClient($config);
		$agent = new MwAgent($client);

		$diff = isset($_GET['diff'])?$_GET['diff']:0;
		$title = MwWeeklyReport::getTitle($diff);
		//print($result . "\n");
		if ($diff <= 0 && $agent->existsPage($title) == false) {
			//今週(または過去)のページが無いなら、あらかじめ作成する
			$content = MwWeeklyReport::getBlankPage($diff);
			$agent->createNewPage($title, $content);
		}
		header('Location: ' . $agent->getUrl($config['url'], $title));
	} catch(Exception $e) {
		print($e->getMessage() . "\n");
	}
