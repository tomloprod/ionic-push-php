<?php

namespace Tomloprod\IonicPush;

/**
 * Class IonicApiRequest
 *
 * @package Tomloprod\IonicPush
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class IonicApiRequest {

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
     * Send requests to the Ionic Push API.
     *
     * @private
     * @param string $method
     * @param string $endPoint
     * @param string $data
     * @return array
     */
    public function sendRequest($method, $endPoint, $data = "") {
        $jsonData = json_encode($data);

        $endPoint = self::$ionicBaseURL . $endPoint;

        $ci = curl_init();

        $authorization = sprintf('Bearer %s', $this->ionicAPIToken);

        $headers = [
            'Authorization:' . $authorization,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        ];

        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifyPeer);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case self::METHOD_POST:
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($jsonData)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData);
                }
                break;
            case self::METHOD_GET:
            case self::METHOD_DELETE:
                curl_setopt($ci, CURLOPT_CUSTOMREQUEST, $method);
                if (!empty($jsonData)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData);
                }
                break;
        }

        curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE);
        curl_setopt($ci, CURLOPT_URL, $endPoint);

        $content = curl_exec($ci);
        curl_close($ci);

        return $content;
    }

}
