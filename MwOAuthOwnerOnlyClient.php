<?php
require_once __DIR__ . '/vendor/autoload.php';
use MediaWiki\OAuthClient\Consumer;
use MediaWiki\OAuthClient\Token;
use MediaWiki\OAuthClient\Request;
use MediaWiki\OAuthClient\SignatureMethod\HmacSha1;

class MwOAuthOwnerOnlyClient {
	protected $consumer;
	protected $accessToken;
	protected $apiUrl;

	public function __construct($config) {
		$this->consumer = new Consumer( $config['consumer_key'], $config['consumer_secret'] );
		$this->accessToken = new Token( $config['access_key'], $config['access_secret']);
		$this->apiUrl = $config['api_url'];
	}

	public function get($params) {
		$queryString = http_build_query($params);
		$url = $this->apiUrl . '?' . $queryString;

		$curl = curl_init();
		$request = Request::fromConsumerAndToken(
			$this->consumer, $this->accessToken, 'GET', $url, $params );
		$request->signRequest( new HmacSha1(), $this->consumer, $this->accessToken );
		$authorizationHeader = $request->toHeader();
		curl_setopt_array($curl, [
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_URL => $url,
		    CURLOPT_CUSTOMREQUEST => 'GET',
		    CURLOPT_HTTPHEADER => [$authorizationHeader],
		]);
		$response = curl_exec($curl);
		$result = json_decode($response, true);
		if (array_key_exists('error', $result)) {
			throw new Exception($result['error']['info']);
		}
		return $result;
	}

	function post($params) {
		$queryString = http_build_query($params);

		$curl = curl_init();
		$request = Request::fromConsumerAndToken(
			$this->consumer, $this->accessToken, 'POST', $this->apiUrl, $params );
		$request->signRequest( new HmacSha1(), $this->consumer, $this->accessToken );
		$authorizationHeader = $request->toHeader();
		curl_setopt_array($curl, [
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_URL => $this->apiUrl,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => $queryString,
		    CURLOPT_HTTPHEADER => [$authorizationHeader],
		]);
		$response = curl_exec($curl);
		$result = json_decode($response, true);
		if (array_key_exists('error', $result)) {
			throw new Exception($result['error']['info']);
		}
		return $result;
	}
}
