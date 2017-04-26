<?php

namespace Tomloprod\IonicPush;

/**
 * Ionic Push Library v 1.0.2
 * 
 * 
 * @category  Library
 * @author    Tomás L.R (@tomloprod)
 */
class IonicPush {

    //////////// Required for authentication
    private $ionicProfile;
    private $ionicAPIToken;
    //////////// API URLS
    private $ionicBaseURL = 'https://api.ionic.io/';
    private $sendNotification = 'push/notifications'; // POST
    private $getDeviceInfo = 'push/tokens/:token_id'; // GET
    private $listTokens = 'push/tokens'; // GET
    private $listNotifications = 'push/notifications'; // GET
    private $deleteDevice = 'push/tokens/:token_id'; // DELETE
    //////////// Config parameters.
    private $pushData = [];
    //////////// cURL
    public $timeout = 0;
    public $connectTimeout = 0;
    public $sslVerifypeer = 0;

    public function __construct($ionicProfile, $ionicAPIToken) {
        $this->ionicProfile = $ionicProfile;
        $this->ionicAPIToken = $ionicAPIToken;
    }

    /**
     * 
     * @param {String} $date - Time to start delivery of the notification Y-m-d H:i:s format
     */
    public function setScheduled($date) {
        //////////// Convert date to RFC3339 
        $dateTime = date("c", strtotime($date));
        $this->pushData["scheduled"] = $dateTime;
    }

    /**
     * Determines if the message should be delivered as a silent notification.
     * @param {Boolean} $enableSilentNotification
     */
    public function setSilentNotification($enableSilentNotification) {
        if ($enableSilentNotification) {
            $this->pushData["notification"]["android"]["content_available"] = 1;
            $this->pushData["notification"]["ios"]["content_available"] = 1;
        } else {
            unset($this->pushData["notification"]["android"]["content_available"]);
            unset($this->pushData["notification"]["ios"]["content_available"]);
        }
    }

    /**
     * Custom data.
     * @param {array} $payloadData
     */
    public function setPayload($payloadData) {
        if (!is_array($payloadData)) {
            $payloadData = array($payloadData);
        }
        if (count($payloadData) > 0) {
            $this->pushData['notification']['payload'] = $payloadData;
        }
    }

    /**
     * Notification config.
     * @param {array} $notificationData
     */
    public function setConfig($notificationData) {
        if (!is_array($notificationData)) {
            $notificationData = array($notificationData);
        }
        if (count($notificationData) > 0) {
            $newData = [];
            $newData["profile"] = $this->ionicProfile;
            $newData["notification"] = $notificationData;
            $this->pushData = array_merge($this->pushData, $newData);
        }
    }

    /**
     * Paginated listing of Tokens
     * @param {array} $parameters
     * @return {array}
     */
    public function listTokens($parameters) {
        $getParameters = http_build_query($parameters);
        return $this->sendRequest("GET", $this->ionicBaseURL . $this->listTokens . "?" . $getParameters, $this->pushData);
    }

    /**
     * Paginated listing of Push Notifications
     * @param {array} $parameters
     * @return {array}
     */
    public function listNotifications($parameters) {
        $getParameters = http_build_query($parameters);
        return $this->sendRequest("GET", $this->ionicBaseURL . $this->listNotifications . "?" . $getParameters, $this->pushData);
    }

    /**
     * Get information about a device of a specific device token
     * @param {String} $deviceToken - Device token
     * @return {array}
     */
    public function getDeviceInfo($deviceToken) {
        return $this->prepareRequest("GET", $deviceToken, $this->ionicBaseURL . $this->getDeviceInfo);
    }

    /**
     * Delete a device related to the device token. 
     * @param {String} $deviceToken - Device token
     * @return {array}
     */
    public function deleteDevice($deviceToken) {
        return $this->prepareRequest("DELETE", $deviceToken, $this->ionicBaseURL . $this->deleteDevice);
    }

    /**
     * Send push for the indicated device tokens
     * @param {array} $deviceTokens
     * @return {array}
     */
    public function sendPush($deviceTokens) {
        $this->pushData["tokens"] = $deviceTokens;
        return $this->push();
    }

    /** 
     * Send push for all registered devices.
     * @return {array}
     */
    public function sendPushAll() {
        $this->pushData["send_to_all"] = true;
        return $this->push();
    }

    /** PRIVATE METHOD.
     * Used by "sendPush" and "sendPushAll"
     * @return {array}
     */
    private function push() {
        $response = $this->sendRequest("POST", $this->ionicBaseURL . $this->sendNotification, $this->pushData);
        return $response;
    }

    /** PRIVATE METHOD.
     * Used by "getDeviceInfo" and "deleteDevice"
     * @param {String} $method
     * @param {String} $deviceToken
     * @param {String} $endPoint
     * @return {array}
     */
    private function prepareRequest($method, $deviceToken, $endPoint) {
        return $this->sendRequest($method, str_replace(":token_id", md5($deviceToken), $endPoint), $this->pushData);
    }

    /** PRIVATE METHOD.
     * Send requests to the Ionic Push API
     * @param {String} $method
     * @param {String} $endPoint
     * @param {String} $data
     * @return {array}
     */
    private function sendRequest($method, $endPoint, $data) {
        $jsonData = json_encode($data);

        $ci = curl_init();

        $authorization = sprintf("Bearer %s", $this->ionicAPIToken);

        $headers = array(
            'Authorization:' . $authorization,
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData)
        );

        curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, $this->connectTimeout);
        curl_setopt($ci, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, $this->sslVerifypeer);
        curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ci, CURLOPT_HEADER, FALSE);

        switch ($method) {
            case "POST":
                curl_setopt($ci, CURLOPT_POST, TRUE);
                if (!empty($jsonData)) {
                    curl_setopt($ci, CURLOPT_POSTFIELDS, $jsonData);
                }
                break;
            case "DELETE":
            case "GET":
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
