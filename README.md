# ionic-push-php [![Release](https://img.shields.io/github/release/tomloprod/ionic-push-php.svg)](https://github.com/tomloprod/ionic-push-php) [![License](https://img.shields.io/github/license/tomloprod/ionic-push-php.svg)](http://www.opensource.org/licenses/mit-license.php) 

<br>

ionic-push-php is a library that allows you to consume the *Ionic Cloud API* for sending push notifications (*normal and scheduled*), get a paginated list of sending push notifications,  get information of registered devices, remove registered devices by token, ...

<br>

---

### Requirements:

- PHP 5.1+
- cURL

---

<br>

### How to use:


First, instance an object:

    $ionicPush = new IonicPush($ionicProfile, $ionicAPIToken);
 
 Then you can:
 
 **1) Get list of notifications:**
 
    $notificationList = $ionicPush->listNotifications([
        //////////// Number of notifications per page
        "page_size" => 1,
        //////////// Selected page
        "page" => 1,
        //////////// You can also pass other fields like "message_total"
        "fields" => "message_total"
    ]);

***NOTE:** You will need to parse the returned array.*

 **2) Get device information:**
 
     $deviceInformation = $ionicPush->getDeviceInfo($desiredDeviceToken);
 
 
**3) Remove devices:**

    $ionicPush->deleteDevice($desiredDeviceToken);
 
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

You can also pass **custom data** to the notification:

    $ionicPush->setPayload([ 
        "myCustomField" => "This is the content of my customField",
        "anotherCustomField" => "More custom content"
    ]);
    
And define if you want and **silent notification**:

    $ionicPush->setSilentNotification(true);
    
Or/and even an **scheduled notification**:

    $ionicPush->setScheduled("2016-12-10 10:30:10");

When you have configured the notification according to your needs you can send it to some devices:

    $ionicPush->sendPush([$desiredToken1, $desiredToken2, $desiredToken3]);
    
Or send this to all registered devices:

    $ionicPush->sendPushAll();