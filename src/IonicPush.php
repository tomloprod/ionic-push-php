<?php

namespace Tomloprod\IonicPush;

/**
 * Ionic Push Library v 1.1.0
 *
 * @package Tomloprod\IonicPush
 * @category  Library
 * @author    Tomás L.R (@tomloprod)
 */
class IonicPush {

    /**
     * NotificationsApi instance.
     * @var NotificationsApi
     */
    public $notifications;

    /**
     * DeviceTokensApi instance.
     * @var DeviceTokensApi
     */
    public $deviceTokens;

    /**
     * MessagesApi instance.
     * @var MessagesApi
     */
    // public $messages;

    /**
     * IonicPush constructor.
     *
     * @param $ionicProfile
     * @param $ionicAPIToken
     */
    public function __construct($ionicProfile, $ionicAPIToken) {
        $this->notifications = new NotificationsApi($ionicProfile, $ionicAPIToken);
        $this->deviceTokens = new DeviceTokensApi($ionicProfile, $ionicAPIToken);
        // $this->messages = new MessagesApi($ionicProfile, $ionicAPIToken);
    }

}
