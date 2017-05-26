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
class Request
{
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
    public function __construct($ionicProfile, $ionicAPIToken)
    {
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
    public function sendRequest($method, $endPoint, $data = '')
    {
        $data = json_encode($data);

        $headers = [
            'Authorization: Bearer ' . $this->ionicAPIToken,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data),
        ];

        $curlHandler = curl_init();

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

        if (!empty($data)) {
            curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $data);
        }

        curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        curl_setopt($curlHandler, CURLOPT_URL, self::$ionicBaseURL . $endPoint);

        $response = curl_exec($curlHandler);

        $statusCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);

        curl_close($curlHandler);

        $response = json_decode($response);

        if (!$this->isSuccessResponse($statusCode)) {
            $this->throwRequestException($statusCode, $response);
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
     * Is response valid?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isSuccessResponse($statusCode)
    {
        return $statusCode >= 200 && $statusCode < 300;
    }

    /**
     * Is response invalid?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isInvalidResponse($statusCode)
    {
        return $statusCode < 100 || $statusCode >= 600;
    }

    /**
     * Is there a client error?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isClientErrorResponse($statusCode)
    {
        return $statusCode >= 400 && $statusCode < 500;
    }

    /**
     * Was there a server side error?
     *
     * @private
     * @param number $statusCode
     * @return bool
     */
    private function isServerErrorResponse($statusCode)
    {
        return $statusCode >= 500 && $statusCode < 600;
    }

    /**
     * Throw the RequestException error with error detail
     *
     * @private
     * @param number $statusCode
     * @param mixed $response
     *
     * @throws RequestException
     *
     * @return void
     */
    private function throwRequestException($statusCode, $response)
    {
        if ($response && isset($response->error)) {
            $type = $response->error->type;
            $message = $this->getResponseErrorMessage($response->error);
            $link = $response->error->link;
        } elseif ($this->isServerErrorResponse($statusCode)) {
            $type = 'Server Error';
            $message = RequestException::$statusTexts[$statusCode];
            $link = '';
        } else {
            $type = 'Client Error';
            $message = RequestException::$statusTexts[$statusCode];
            $link = '';
        }

        throw new RequestException($type, $message, $link, $statusCode);
    }

    /**
     * Generate a full message from response error request
     *
     * @private
     * @param object $error
     *
     * @return string
     */
    private function getResponseErrorMessage($error)
    {
        $message = $error->message;

        if (isset($error->details) && is_array($error->details)) {
            foreach ($error->details as $detail) {
                $message .= ' ['.$detail->error_type.'] '.implode(' ', $detail->errors);
            }
        }

        return $message;
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
