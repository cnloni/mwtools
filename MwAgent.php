<?php
class MwAgent {
	protected $client;

	public function __construct($client) {
		$this->client = $client;
	}
	/*
		clientloginで用いるトークンを取得する
	*/
	protected function getLoginToken() {
		$result = $this->client->get(array(
			'action' => 'query',
			'meta' => 'tokens',
			'type' => 'login'
		));
		return $result['query']['tokens']['logintoken'];
	}
	/*
		editで用いるトークンを取得する
	*/
	protected function getCsrfToken() {
		$result = $this->client->get(array(
			'action' => 'query',
			'meta' => 'tokens'
		));
		return $result['query']['tokens']['csrftoken'];
	}
	public function clientLogin($user, $password, $rootUrl) {
		$token = $this->getLoginToken();
		$result = $this->client->post(array(
			'action' => 'clientlogin',
			'loginreturnurl' => $rootUrl,
			'username' => $user,
			'password' => $password,
			'logintoken' => $token
		));
		$status = $result['clientlogin']['status'];
		if ($status !== 'PASS') {
			throw new Exception("'clientlogin' failed. STATUS = " . $status);
		};
	}
	public function existsPage($title, $namespaceId=0) {
		$params = array(
			'action' => 'query',
			'titles' => $title
		);
		if ($namespaceId > 0) {
			$params['prop'] = $namespaceId;
		}
		$result = $this->client->get($params);
		$pages = $result['query']['pages'];
		$id = array_keys($pages)[0];
		return $id >= 0;
	}
	public function createNewPage($title, $text) {
		$token = $this->getCsrfToken();
		$result = $this->client->post(array(
			'action' => 'edit',
			'token' => $token,
			'title' => $title,
			'text' => $text
		));
		$status = $result['edit']['result'];
		if ($status != 'Success') {
			// 作成に失敗した
			throw new Exception("Failed to create new page.");
		}
	}
	public function getUrl($url, $title) {
		$toUrl = $url . '/' . urlencode($title);
		return $toUrl;
	}
}
