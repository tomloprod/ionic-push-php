# ionic-push-php [![Release](https://img.shields.io/github/release/tomloprod/ionic-push-php.svg)](https://github.com/tomloprod/ionic-push-php) [![License](https://img.shields.io/github/license/tomloprod/ionic-push-php.svg)](http://www.opensource.org/licenses/mit-license.php) 

ionic-push-php is a library that allows you to consume the *Ionic Cloud API* for **sending push notifications** (*normal and scheduled*), get a paginated **list of sending push notifications**,  get **information of registered devices**, **remove registered devices by token**, ...

---

### Requirements:

- PHP 5.1+
- cURL

---

### Installation:

    composer require tomloprod/ionic-push-php

---

### $ionicProfile and $ionicAPIToken:

In the next link you can see how to get this two configuration values: https://github.com/tomloprod/ionic-push-php/issues/1

---



### How to use:


First, instance an object:

```php
use Tomloprod\IonicPush\IonicPush;


$ionicPush = new IonicPush($ionicProfile, $ionicAPIToken);
 ```

 
 Then you can:
 
 <br>
 
 **1) Get list of notifications:**
```php
$notificationList = $ionicPush->listNotifications([
    // Number of notifications per page
    "page_size" => 1,
    // Selected page
    "page" => 1,
    // You can also pass other fields like "message_total"
    "fields" => "message_total"
]);
```
<br>

 **2) Get list of tokens:**
```php
$notificationList = $ionicPush->listTokens([
    // Determines whether to include invalidated tokens
    show_invalid => 1,
    // Only display tokens associated with the User ID.
    user_id => $desiredUserId,
    // Number of tokens per page
    page_size => 4,
    // Selected page
    page => 1
]);
```

 **3) Get device information:**
 ```php
 $deviceInformation = $ionicPush->getDeviceInfo($desiredDeviceToken);
 ```
 
 <br>
 
**4) Remove devices:**
```php
$ionicPush->deleteDevice($desiredDeviceToken);
```
 <br>
 
 **5) Send notifications:**
 ```php
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
```
<br>

You can also pass **custom data** to the notification:
```php
$ionicPush->setPayload([ 
    "myCustomField" => "This is the content of my customField",
    "anotherCustomField" => "More custom content"
]);
```
<br>
    
    
And define, if you need it, a **silent notification**:
```php
$ionicPush->setSilentNotification(true);
```
<br>

Or/and even a **scheduled notification**:
```php
$ionicPush->setScheduled("2016-12-10 10:30:10");
```
<br>

When you have configured the notification according to your needs you can send it to some devices:
```php
$ionicPush->sendPush([$desiredToken1, $desiredToken2, $desiredToken3]);
```
<br>

Or send this to all registered devices:
```php
$ionicPush->sendPushAll();
```

<br>
    
##### *NOTE: You will need to parse the returned array of the methods that return information.*
