<?php

namespace Tomloprod\IonicApi\Api;

/**
 * Class Request
 *
 * @package Tomloprod\IonicApi\Api
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class Request {

    // Available HTTP methods
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_PATCH = 'PATCH';
    const METHOD_DELETE = 'DELETE';

    // API URL
    private static $ionicBaseURL = 'https://api.ionic.io';

    // cURL
    public $timeout = 0;
    public $connectTimeout = 0;
    public $sslVerifyPeer = 0;

    // Required for Authorization
    private $ionicProfile;
    private $ionicAPIToken;

    /**
     * Request constructor.
     *
     * @param string $ionicProfile
     * @param string $ionicAPIToken
     */
    public function __construct($ionicProfile, $ionicAPIToken) {
        $this->ionicProfile = $ionicProfile;
        $this->ionicAPIToken = $ionicAPIToken;
    }
	
    /**
     * Decode response into a PHP variable
     *
     * @param boolean $decodeJSON - Indicates whether the JSON response will be converted to a PHP variable before return.
     * @return object|null $response - An object when $response has data and null when there is no data.
     */
	public function decodeResponse($response) {
		$response = json_decode($response);
		// If is an object with data property and data has elements.
		if(is_object($response) && property_exists($response, "data") && count($response->data) > 0) {
			return $response;
		}else{
			return null;
		}
	}

    /**
     * Send requests to the Ionic Push API.
     *
     * @param string $method
     * @param string $endPoint
     * @param string $data
     * @return array
     */
    public function sendRequest($method, $endPoint, $data = "") {
        $jsonData = json_encode($data);

        $endPoint = self::$ionicBaseURL . $endPoint;

        $curlHandler = curl_init();

        $authorization = sprintf('Bearer %s', $this->ionicAPIToken);

        $headers = [
            'Authorization:' . $authorization,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ];

        curl_setopt($curlHandler, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($curlHandler, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
        curl_setopt($curlHandler, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curlHandler, CURLOPT_HEADER, false);

        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($curlHandler, CURLOPT_POST, true);
                break;
            case self::METHOD_GET:
            case self::METHOD_DELETE:
            case self::METHOD_PATCH:
                curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }
	
		if (!empty($jsonData)) {
			curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $jsonData);
		}

        curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandler, CURLOPT_URL, $endPoint);

        $response = curl_exec($curlHandler);
        curl_close($curlHandler);

        return $response;
    }

}
