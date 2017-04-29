<?php

namespace Tomloprod\IonicApi\Api;

/**
 * Class Notifications
 *
 * Stores ionic push api methods related to notifications collection.
 * More info: https://docs.ionic.io/api/endpoints/push.html
 *
 * @package Tomloprod\IonicApi\Api
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class Notifications extends Request {

    public $requestData = [];
    private $ionicProfile;
    private static $endPoints = [
        'list' => '/push/notifications', // GET
        'create' => '/push/notifications', // POST
        'retrieve' => '/push/notifications/:notification_id', // GET
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
     * @param boolean $decodeJSON - Indicates whether the JSON response will be converted to a PHP variable before return.
     * @return array
     */
    public function paginatedList($parameters, $decodeJSON = false) {
        $response =  $this->sendRequest(
            self::METHOD_GET, 
            self::$endPoints['list'] . '?' . http_build_query($parameters), 
            $this->requestData
        );
        $this->resetRequestData();
        return ($decodeJSON) ? json_decode($response) : $response;
    }

    /**
     * Get a Notification.
     *
     * @param string $notificationId - Notification id
     * @return array
     */
    public function retrieve($notificationId) {
        $response = $this->sendRequest(
            self::METHOD_GET,
            str_replace(':notification_id', $notificationId, self::$endPoints['retrieve']),
            $this->requestData
        );
        $this->resetRequestData();
        return $response;
    }

    // TODO: replace

    /**
     * Deletes a notification.
     *
     * @param $notificationId
     * @return array
     */
    public function delete($notificationId) {
        return $this->sendRequest(
            self::METHOD_DELETE,
            str_replace(':notification_id', $notificationId, self::$endPoints['delete'])
        );
    }

    /**
     * List messages of the indicated notification.
     *
     * @param string $notificationId - Notification id
     * @param array $parameters
     * @return array
     */
    public function listMessages($notificationId, $parameters) {
        $endPoint = str_replace(':notification_id', $notificationId, self::$endPoints['listMessages']);
        $response =  $this->sendRequest(
            self::METHOD_GET, 
            $endPoint . '?' . http_build_query($parameters), 
            $this->requestData
        );
        $this->resetRequestData();
        return $response;
    }

    /**
     * Send push notification for the indicated device tokens.
     *
     * @param array $deviceTokens
     * @return array
     */
    public function sendNotification($deviceTokens) {
        $this->requestData['tokens'] = $deviceTokens;
        $this->requestData['send_to_all'] = false;
        return $this->create();
    }

    /**
     * Send push notificatoin for all registered devices.
     *
     * @return array
     */
    public function sendNotificationToAll() {
        $this->requestData['send_to_all'] = true;
        return $this->create();
    }

    /**
     * Create a Push Notification.
     *
     * Used by "sendNotification" and "sendNotificationToAll".
     *
     * @private
     * @return array
     */
    private function create() {
        $response = $this->sendRequest(
            self::METHOD_POST, 
            self::$endPoints['create'], 
            $this->requestData
        );
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
