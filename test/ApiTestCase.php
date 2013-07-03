<?php
/**
 * ApiTestCase class file.
 * 
 * @author Jin Hu <bixuehujin@gmail.com>
 * @since 2013-07-03
 */

use Guzzle\Http\Client;
use Guzzle\Http\Message\Response;

Yii::setPathOfAlias('Guzzle', __DIR__ . '/../vendors/guzzle/src/Guzzle');
Yii::setPathOfAlias('Symfony', __DIR__ . '/../vendors/event-dispatcher/Symfony');

class ApiTestCase extends CDbTestCase {
	
	public $baseUrl;
	/**
	 * @var Guzzle\Http\Client
	 */
	protected $client;
	
	public $requestOptions = array(
		'exceptions' => false
	);
	
	public function setUp() {
		parent::setUp();
		$this->client = new Client($this->baseUrl);
	}
	
	/**
	 * Construct the request url using the uri param and baseUrl.
	 * 
	 * @param mixed $uri
	 * @return string
	 */
	protected function getRequestUrl($uri) {
		if (is_array($uri) && count($uri) > 1) {
			$uri2 = array_shift($uri);
			$uri = $uri2 . '?' . http_build_query($uri, '', '&');
		}
		return $this->baseUrl . '/' . $uri;
	}
	
	/**
	 * Perform a GET request.
	 * 
	 * @param string|array $uri
	 * @param array $headers
	 * @return Response
	 */
	public function get($uri, $headers = null) {
		return $this->client->get($this->getRequestUrl($uri), $headers, $this->requestOptions)->send();
	}
	
	/**
	 * Perform a POST request.
	 * 
	 * @param mixed $uri
	 * @param array $postFields
	 * @param string $headers
	 * @return Response
	 */
	public function post($uri, $postFields = array(), $headers = null) {
		return $this->client->post($this->getRequestUrl($uri), $headers, $postFields, $this->requestOptions)->send();
	}
	
	/**
	 * Perform a PUT request.
	 * 
	 * @param mixed $uri
	 * @param mixed $body
	 * @param array $headers
	 * @return Response
	 */
	public function put($uri, $body = null, $headers = null) {
		return $this->client->put($this->getRequestUrl($uri), $headers, $body, $this->requestOptions)->send();
	}
	
	/**
	 * Perform a PUT request.
	 *
	 * @param mixed $uri
	 * @param mixed $body
	 * @param array $headers
	 * @return Response
	 */
	public function delete($uri, $body = null, $headers = null) {
		return $this->client->delete($this->getRequestUrl($uri), $headers, $body, $this->requestOptions)->send();
	}
}
