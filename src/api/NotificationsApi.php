<?php

namespace Tomloprod\IonicPush\Api;

/**
 * Class NotificationsApi
 *
 * Stores ionic push api methods related to notifications collection.
 * More info: https://docs.ionic.io/api/endpoints/push.html
 *
 * @package Tomloprod\IonicPush\Api
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class NotificationsApi extends IonicApiRequest {

    public $requestData = [];
    private $ionicProfile;
    private static $endPoints = [
        'list' => '/push/notifications', // GET
        'create' => '/push/notifications', // POST
        'retrieve' => '/push/notifications,:notification_id', // GET
        'replace' => '/push/notifications/:notification_id', // PUT
        'delete' => '/push/notifications/:notification_id', // DELETE
        'listMessages' => '/push/notifications/:notification_id/messages', // GET
    ];

    /**
     * Notifications constructor.
     *
     * @param string $ionicProfile
     * @param string $ionicAPIToken
     */
    public function __construct($ionicProfile, $ionicAPIToken)
    {
        parent::__construct($ionicProfile, $ionicAPIToken);
        $this->ionicProfile = $ionicProfile;
        $this->requestData = ['profile' => $this->ionicProfile];
    }

    /**
     * Set notification config.
     *
     * @param array $notificationData
     * @param array $payloadData - Custom extra data
     * @param bool $silentNotification - Determines if the message should be delivered as a silent notification.
     * @param string $dateTime - Time to start delivery of the notification Y-m-d H:i:s format
     */
    public function setConfig($notificationData, $payloadData = [], $silentNotification = false, $dateTime = '') {
        if (!is_array($notificationData)) {
            $notificationData = [$notificationData];
        }
        if (count($notificationData) > 0) {
            $this->requestData = array_merge($this->requestData, ['notification' => $notificationData]);
        }

        // payload
        if (!is_array($payloadData)) {
            $payloadData = [$payloadData];
        }
        if (count($payloadData) > 0) {
            $this->requestData['notification']['payload'] = $payloadData;
        }

        // silent
        if ($silentNotification) {
            $this->requestData['notification']['android']['content_available'] = 1;
            $this->requestData['notification']['ios']['content_available'] = 1;
        } else {
            unset($this->requestData['notification']['android']['content_available']);
            unset($this->requestData['notification']['ios']['content_available']);
        }

        // scheduled
        if($this->isDatetime($dateTime)) {
            // Convert dateTime to RFC3339
            $this->requestData['scheduled'] = date("c", strtotime($dateTime));
        }
    }

    /**
     * Paginated listing of Push Notifications.
     *
     * @param array $parameters
     * @return array
     */
    public function paginatedList($parameters) {
        $getParameters = http_build_query($parameters);
        $response =  $this->sendRequest(self::METHOD_GET, self::$endPoints['list'] . '?' . $getParameters, $this->requestData);
        $this->resetRequestData();
        return $response;
    }

    /**
     * Get information about a specific notification.
     *
     * @param string $notificationId - Notification id
     * @return array
     */
    public function retrieve($notificationId) {
        $response = $this->sendRequest(
            self::METHOD_GET,
            str_replace(':notification_id', md5($notificationId), self::$endPoints['retrieve']),
            $this->requestData
        );
        $this->resetRequestData();
        return $response;
    }

    // TODO: replace

    // TODO: delete

    // TODO: list messages

    /**
     * Send push for the indicated device tokens.
     *
     * @param array $deviceTokens
     * @return array
     */
    public function sendPush($deviceTokens) {
        $this->requestData['tokens'] = $deviceTokens;
        $this->requestData['send_to_all'] = false;
        return $this->create();
    }

    /**
     * Send push for all registered devices.
     *
     * @return array
     */
    public function sendPushToAll() {
        $this->requestData['send_to_all'] = true;
        return $this->create();
    }

    /**
     * Used by "sendPush" and "sendPushAll".
     *
     * @private
     * @return array
     */
    private function create() {
        $response = $this->sendRequest(self::METHOD_POST, self::$endPoints['create'], $this->requestData);
        $this->resetRequestData();
        return $response;
    }

    /**
     * Reinitialize requestData.
     *
     * @private
     */
    private function resetRequestData() {
        $this->requestData = ['profile' => $this->ionicProfile];
    }

    /**
     * Validates a datetime (format YYYY-MM-DD HH:MM:SS)
     *
     * @param string $datetime
     * @return bool
     */
    private function isDatetime($datetime)
    {
        if (preg_match('/^(\d{4})-(\d\d?)-(\d\d?) (\d\d?):(\d\d?):(\d\d?)$/', $datetime, $matches)) {
            return checkdate($matches[2], $matches[3], $matches[1]) && $matches[4] / 24 < 1 && $matches[5] / 60 < 1 && $matches[6] / 60 < 1;
        }

        return false;
    }

}
