<?php

namespace Tomloprod\IonicApi;

use Tomloprod\IonicApi\Api\Notifications;
use Tomloprod\IonicApi\Api\DeviceTokens;
use Tomloprod\IonicApi\Api\Messages;

/**
 * Ionic API Push Library v 1.1.5
 *
 * @package Tomloprod\IonicApi
 * @category  Library
 * @author  Tomás L.R (@tomloprod)
 * @author  Ramon Carreras (@ramoncarreras)
 */
class Push {

    /**
     * Notifications instance.
     * @var Notifications
     */
    public $notifications;

    /**
     * DeviceTokens instance.
     * @var DeviceTokens
     */
    public $deviceTokens;

    /**
     * Messages instance.
     * @var Messages
     */
    public $messages;

    /**
     * Push constructor.
     *
     * @param $ionicProfile
     * @param $ionicAPIToken
     */
    public function __construct($ionicProfile, $ionicAPIToken) {
        $this->notifications = new Notifications($ionicProfile, $ionicAPIToken);
        $this->deviceTokens = new DeviceTokens($ionicProfile, $ionicAPIToken);
        $this->messages = new Messages($ionicProfile, $ionicAPIToken);
    }

}
