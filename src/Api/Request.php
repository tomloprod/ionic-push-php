<?php

namespace Tomloprod\IonicApi\Api;

use Tomloprod\IonicApi\Exception\RequestException;

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
     * @throws RequestException
     * @return object
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
        if($this->isInvalidResponse($httpStatusCode)) {
            throw new RequestException("Invalid Response", "The response from ionic is invalid", "", $httpStatusCode);
        } else if($this->isClientErrorResponse($httpStatusCode) || $this->isServerErrorResponse($httpStatusCode)) {
            if(empty($response) || empty($response->error)) {
                throw new RequestException($this->isServerErrorResponse($httpStatusCode) ? "Server Error" : "Client Error", RequestException::$statusTexts[$httpStatusCode], "", $httpStatusCode);
            } else {
                throw new RequestException($response->error->type, $response->error->message, $response->error->link, $httpStatusCode);
            }
        }

        // Return response.
        return $response;
    }

    /**
     * Is response valid?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isValidResponse($statusCode) {
        return !$this->isInvalidResponse($statusCode);
    }*/

    /**
     * Is response invalid?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isInvalidResponse($statusCode) {
        return $statusCode < 100 || $statusCode >= 600;
    }

    /**
     * Is there a client error?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isClientErrorResponse($statusCode) {
        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Was there a server side error?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isServerErrorResponse($statusCode) {
        return $statusCode >= 500 && $statusCode < 600;
    }

    /**
     * Is response informative?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isInformationalResponse($statusCode) {
        return $statusCode >= 100 && $statusCode < 200;
    }*/

    /**
     * Is response successful?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isSuccessfulResponse($statusCode) {
        return $statusCode >= 200 && $statusCode < 300;
    }*/

    /**
     * Is the response a redirect?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isRedirectionResponse($statusCode) {
        return $statusCode >= 300 && $statusCode < 400;
    }*/

    /**
     * Is the response a Auth error?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isAuthErrorResponse($statusCode) {
        return 401 === $statusCode;
    }*/

    /**
     * Is the response forbidden?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isForbiddenResponse($statusCode) {
        return 403 === $statusCode;
    }*/

    /**
     * Is the response a not found error?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isNotFoundResponse($statusCode) {
        return 404 === $statusCode;
    }*/

    /**
     * Is the response empty?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    /*private function isEmptyResponse($statusCode) {
        return in_array($statusCode, array(204, 304));
    }*/

}
