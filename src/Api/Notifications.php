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
     * @param boolean $silentNotification - Determines if the message should be delivered as a silent notification.
     * @param string $scheduledDateTime - Time to start delivery of the notification Y-m-d H:i:s format
     * @param string $sound - Filename of audio file to play when a notification is received.
     */
    public function setConfig($notificationData, $payloadData = [], $silentNotification = false, $scheduledDateTime = '', $sound = 'default') {
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
        if($this->isDatetime($scheduledDateTime)) {
            // Convert dateTime to RFC3339
            $this->requestData['scheduled'] = date("c", strtotime($scheduledDateTime));
        }
	    
        // sound
        $this->requestData['notification']['android']['sound'] = $sound;
    	$this->requestData['notification']['ios']['sound'] = $sound;
    }

    /**
     * Paginated listing of Push Notifications.
     *
     * @param array $parameters
     * @return ApiResponse
     */
    public function paginatedList($parameters = []) {
        $response =  $this->sendRequest(
            self::METHOD_GET, 
            self::$endPoints['list'] . '?' . http_build_query($parameters), 
            $this->requestData
        );
        $this->resetRequestData();
	    return $response;
    }

    /**
     * Get a Notification.
     *
     * @param string $notificationId - Notification id
     * @return ApiResponse
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
     * @return ApiResponse
     */
    public function delete($notificationId) {
        return $this->sendRequest(
            self::METHOD_DELETE,
            str_replace(':notification_id', $notificationId, self::$endPoints['delete'])
        );
    }

    /**
     * Deletes all notifications
     *
     * @return array - array of ApiResponse
     */
    public function deleteAll() {
        $responses = array();
        $notifications = self::paginatedList();
        if($notifications['success']) {
           foreach($notifications['response']['data'] as $notification) {
               $responses[] = self::delete($notification->uuid);
           } 
        } else {
           return array($notifications);
        }
        return $responses;
    }
    
    /**
     * List messages of the indicated notification.
     *
     * @param string $notificationId - Notification id
     * @param array $parameters
     * @return ApiResponse
     */
    public function listMessages($notificationId, $parameters = []) {
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
     * @return ApiResponse
     */
    public function sendNotification($deviceTokens) {
        $this->requestData['tokens'] = $deviceTokens;
        $this->requestData['send_to_all'] = false;
        return $this->create();
    }

    /**
     * Send push notification for all registered devices.
     *
     * @return ApiResponse
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
     * @return ApiResponse
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
     * @param string $dateTime
     * @return boolean
     */
    private function isDatetime($dateTime) {
        if (preg_match('/^(\d{4})-(\d\d?)-(\d\d?) (\d\d?):(\d\d?):(\d\d?)$/', $dateTime, $matches)) {
            return checkdate($matches[2], $matches[3], $matches[1]) && $matches[4] / 24 < 1 && $matches[5] / 60 < 1 && $matches[6] / 60 < 1;
        }
        return false;
    }

}
