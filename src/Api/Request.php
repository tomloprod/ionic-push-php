<?php

namespace Tomloprod\IonicApi\Api;

use Tomloprod\IonicApi\Exception\AuthException,
    Tomloprod\IonicApi\Exception\NotFoundException,
    Tomloprod\IonicApi\Exception\BadRequestException;

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
    public $timeout = 5; // Set timeout to 0 is inadvisable in a production environment
    public $connectTimeout = 5; // Set timeout to 0 is inadvisable in a production environment
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
     * Send requests to the Ionic Push API.
     *
     * INFO: https://docs.ionic.io/api/http.html#response-structure
     *
     * @param string $method
     * @param string $endPoint
     * @param string $data
     * @return ApiResponse
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

        switch($method) {
            case self::METHOD_POST:
                curl_setopt($curlHandler, CURLOPT_POST, true);
                break;
            case self::METHOD_GET:
            case self::METHOD_DELETE:
            case self::METHOD_PATCH:
                curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $method);
                break;
        }

        if(!empty($jsonData)) {
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $jsonData);
        }

        curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandler, CURLOPT_URL, $endPoint);

        $response = curl_exec($curlHandler);

        $httpStatusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        curl_close($curlHandler);

        $response = json_decode($response);

        // Exceptions
        switch ($httpStatusCode) {
          case 400:
              throw new BadRequestException($response->error->type, $response->error->message, $httpStatusCode);
          break;
          case 401:
              throw new AuthException($response->error->type, $response->error->message, $httpStatusCode);
          break;
          case 404:
              throw new NotFoundException($response->error->type, $response->error->message, $httpStatusCode);
          break;
        }

        // If there is no exception, return response.
        return $response;
    }

}
