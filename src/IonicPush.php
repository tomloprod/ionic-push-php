<?php

namespace Tomloprod\IonicPush;

/**
 * Ionic Push Library v 1.0.5
 *
 * @category  Library
 * @author    Tomás L.R (@tomloprod)
 */
class IonicPush {

    // Constants
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_DELETE = 'DELETE';

    // API URLS
    private static $ionicBaseURL = 'https://api.ionic.io';
    private static $endPoints = [
        'sendNotification' => '/push/notifications', // POST
        'getNotification' => '/push/notifications,:notification_id', // GET
        'getDeviceInfo' => '/push/tokens/:token_id', // GET
        'listTokens' => '/push/tokens', // GET
        'listNotifications' => '/push/notifications', // GET
        'deleteDevice' => '/push/tokens/:token_id', // DELETE
    ];

    // Required for authentication
    private $ionicProfile;
    private $ionicAPIToken;

    // Config parameters
    private $pushData = [];

    // cURL
    public $timeout = 0;
    public $connectTimeout = 0;
    public $sslVerifyPeer = 0;

    /**
     * IonicPush constructor.
     *
     * @param $ionicProfile
     * @param $ionicAPIToken
     */
    public function __construct($ionicProfile, $ionicAPIToken) {
        $this->ionicProfile = $ionicProfile;
        $this->ionicAPIToken = $ionicAPIToken;
    }

    /**
     * Set notification config.
     *
     * @param array $notificationData
     */
    public function setConfig($notificationData) {
        if (!is_array($notificationData)) {
            $notificationData = [$notificationData];
        }
        if (count($notificationData) > 0) {
            $newData = [];
            $newData['profile'] = $this->ionicProfile;
            $newData['notification'] = $notificationData;
            $this->pushData = array_merge($this->pushData, $newData);
        }
    }

    /**
     * Set custom data.
     *
     * @param array $payloadData
     */
    public function setPayload($payloadData) {
        if (!is_array($payloadData)) {
            $payloadData = [$payloadData];
        }
        if (count($payloadData) > 0) {
            $this->pushData['notification']['payload'] = $payloadData;
        }
    }

    /**
     * Set scheduled time for the notification.
     *
     * @param string $date - Time to start delivery of the notification Y-m-d H:i:s format
     */
    public function setScheduled($dateTime) {
        // Convert dateTime to RFC3339
        $this->pushData['scheduled'] = date("c", strtotime($dateTime));
    }

    /**
     * Determines if the message should be delivered as a silent notification.
     *
     * @param bool $enableSilentNotification
     */
    public function setSilentNotification($enableSilentNotification = false) {
        if ($enableSilentNotification) {
            $this->pushData['notification']['android']['content_available'] = 1;
            $this->pushData['notification']['ios']['content_available'] = 1;
        } else {
            unset($this->pushData['notification']['android']['content_available']);
            unset($this->pushData['notification']['ios']['content_available']);
        }
    }

    /**
     * Paginated listing of Push Notifications.
     *
     * @param array $parameters
     * @return array
     */
    public function listNotifications($parameters) {
        $getParameters = http_build_query($parameters);
        return $this->sendRequest(self::METHOD_GET, self::$endPoints['listNotifications'] . '?' . $getParameters, $this->pushData);
    }

    /**
     * Get information about a specific notification.
     *
     * @param string $notificationId - Notification id
     * @return array
     */
    public function getNotification($notificationId) {
        return $this->prepareNotificationRequest(self::METHOD_GET, $notificationId, self::$endPoints['getNotification']);
    }

    /**
     * Paginated listing of Tokens.
     *
     * @param array $parameters
     * @return array
     */
    public function listTokens($parameters) {
        $getParameters = http_build_query($parameters);
        return $this->sendRequest(self::METHOD_GET, self::$endPoints['listTokens'] . '?' . $getParameters, $this->pushData);
    }

    /**
     * Get information about a device of a specific device token.
     *
     * @param string $deviceToken - Device token
     * @return array
     */
    public function getDeviceInfo($deviceToken) {
        return $this->prepareDeviceRequest(self::METHOD_GET, $deviceToken, self::$endPoints['getDeviceInfo']);
    }

    /**
     * Delete a device related to the device token.
     *
     * @param string $deviceToken - Device token
     * @return array
     */
    public function deleteDevice($deviceToken) {
        return $this->prepareDeviceRequest(self::METHOD_DELETE, $deviceToken, self::$endPoints['deleteDevice']);
    }

    /**
     * Send push for the indicated device tokens.
     *
     * @param array $deviceTokens
     * @return array
     */
    public function sendPush($deviceTokens) {
        $this->pushData['tokens'] = $deviceTokens;
		$this->pushData['send_to_all'] = false;
        return $this->push();
    }

    /**
     * Send push for all registered devices.
     *
     * @return array
     */
    public function sendPushAll() {
        $this->pushData['send_to_all'] = true;
        return $this->push();
    }

    /**
     * Used by "sendPush" and "sendPushAll".
     *
     * @private
     * @return array
     */
    private function push() {
        $response = $this->sendRequest(self::METHOD_POST, self::$endPoints['sendNotification'], $this->pushData);
        return $response;
    }

    /**
     * Used by "getDeviceInfo" and "deleteDevice".
     *
     * @private
     * @param string $method
     * @param string $deviceToken
     * @param string $endPoint
     * @return array
     */
    private function prepareDeviceRequest($method, $deviceToken, $endPoint) {
        return $this->sendRequest($method, str_replace(':token_id', md5($deviceToken), $endPoint), $this->pushData);
    }

    /**
     * Used by "getNotification".
     *
     * @private
     * @param $method
     * @param $notificationId
     * @param $endPoint
     * @return array
     */
    private function prepareNotificationRequest($method, $notificationId, $endPoint) {
        return $this->sendRequest($method, str_replace(':notification_id', md5($notificationId), $endPoint), $this->pushData);
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
    private function sendRequest($method, $endPoint, $data) {
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
