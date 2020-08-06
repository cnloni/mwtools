<?php
class MwCurlClient {
	private $requestParameters;
	private $apiUrl;

	public function __construct($config) {
		$this->apiUrl = $config['api_url'];
		$this->cookieFile = tempnam(sys_get_temp_dir(), 'CKF-');
	}
	public function __destruct() {
		unlink($this->cookieFile);
	}
	public function get($params) {
		$queryString = http_build_query($params);
		$url = $this->apiUrl . '?' . $queryString;

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);
		$response = curl_exec($curl);
		$result = json_decode($response, TRUE);
		if (array_key_exists('error', $result)) {
			throw new Exception($result['error']['info']);
		}
		return $result;
	}
	public function post($params) {
		$queryString = http_build_query($params);

		$curl = curl_init($this->apiUrl);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($curl, CURLOPT_POSTFIELDS, $queryString);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_COOKIEJAR, $this->cookieFile);
		curl_setopt($curl, CURLOPT_COOKIEFILE, $this->cookieFile);

		$response = curl_exec($curl);
		$result = json_decode($response, TRUE);
		if (array_key_exists('error', $result)) {
			throw new Exception($result['error']['info']);
		}
		return $result;
	}
}
