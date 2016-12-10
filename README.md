# ionic-push-php [![Release](https://img.shields.io/github/release/tomloprod/ionic-push-php.svg)](https://github.com/tomloprod/ionic-push-php) [![License](https://img.shields.io/github/license/tomloprod/ionic-push-php.svg)](http://www.opensource.org/licenses/mit-license.php) 

ionic-push-php is a library that allows you to consume the *Ionic Cloud API* for sending push notifications (*normal and scheduled*), get a paginated list of sending push notifications,  get information of registered devices, remove registered devices by token, ...

---

### Requirements:

- PHP 5.1+
- cURL

---

### Installation:

    composer require tomloprod/ionic-push-php

---

### How to use:

<br>
First, instance an object:

    $ionicPush = new IonicPush($ionicProfile, $ionicAPIToken);
 
 <br>
 
 Then you can:
 
 <br>
 
 **1) Get list of notifications:**
 
    $notificationList = $ionicPush->listNotifications([
        //////////// Number of notifications per page
        "page_size" => 1,
        //////////// Selected page
        "page" => 1,
        //////////// You can also pass other fields like "message_total"
        "fields" => "message_total"
    ]);

<br>

 **2) Get device information:**
 
     $deviceInformation = $ionicPush->getDeviceInfo($desiredDeviceToken);
 
 <br>
 
**3) Remove devices:**

    $ionicPush->deleteDevice($desiredDeviceToken);
 
 <br>
 
 **4) Send notifications:**
 
    $ionicPush->setConfig([
        "title" => "Your notification title",
        "tickerText" => "Your ticker text",
        "message" => "Your notification message. Bla, bla, bla, bla.",
        "android" => [
            "tag" => "YourTagIfYouNeedIt"
        ],
        "ios" => [
            "priority" => 10
        ]
    ]);

<br>

You can also pass **custom data** to the notification:

    $ionicPush->setPayload([ 
        "myCustomField" => "This is the content of my customField",
        "anotherCustomField" => "More custom content"
    ]);
    
<br>
    
    
And define, if you need it, a **silent notification**:

    $ionicPush->setSilentNotification(true);

<br>

Or/and even a **scheduled notification**:

    $ionicPush->setScheduled("2016-12-10 10:30:10");

<br>

When you have configured the notification according to your needs you can send it to some devices:

    $ionicPush->sendPush([$desiredToken1, $desiredToken2, $desiredToken3]);
  
<br>

Or send this to all registered devices:

    $ionicPush->sendPushAll();
    

<br>
    
##### *NOTE: You will need to parse the returned array of the methods that return information.*
