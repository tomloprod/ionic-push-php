# ionic-push-php [![Release](https://img.shields.io/github/release/tomloprod/ionic-push-php.svg)](https://github.com/tomloprod/ionic-push-php) [![License](https://img.shields.io/github/license/tomloprod/ionic-push-php.svg)](http://www.opensource.org/licenses/mit-license.php) 

ionic-push-php is a library that allows you to consume the *Ionic Cloud API* for **sending push notifications** (*normal and scheduled*), get a paginated **list of sending push notifications**,  get **information of registered devices**, **remove registered devices by token**, ...


## Requirements:

- PHP 5.1+
- cURL


## Installation:

    composer require tomloprod/ionic-push-php


## $ionicProfile and $ionicAPIToken:

In the next link you can see how to get this two configuration values: https://github.com/tomloprod/ionic-push-php/issues/1


## How to use:

First, instance an object:

```php
use Tomloprod\IonicPush\IonicPush;

$ionicPush = new IonicPush($ionicProfile, $ionicAPIToken);
```

 
Then you can:

### Device Tokens

 **1) List tokens:**
```php
$tokenList = $ionicPush->deviceTokens->paginatedList([
    // Determines whether to include invalidated tokens
    'show_invalid' => 1,
    // Only display tokens associated with the User ID.
    'user_id' => $desiredUserId,
    // Number of tokens per page
    'page_size' => 4,
    // Selected page
    'page' => 1
]);
```

<br>

**2) Retrieve device information related to the device token:**
```php
$deviceInformation = $ionicPush->deviceTokens->retrieve($desiredDeviceToken);
```
 
<br>
 
**3) Delete a device related to the device token:**
```php
$deviceInformation = $ionicPush->deviceTokens->delete($desiredDeviceToken);
```

### Notifications
 
**1) List notifications:**
```php
$notificationList = $ionicPush->notifications->paginatedList([
    // Number of notifications per page
    'page_size' => 1,
    // Selected page
    'page' => 1,
    // You can also pass other fields like "message_total"
    'fields' => 'message_total'
]);
```

**2) Retrieve specific notification:**
```php
$notification = $ionicPush->notifications->retrieve($desiredNotificationId);
```
 
<br>
 
**3) Send notifications:**
```php
// Configuration of the notification
$notificationConfig = [
    'title' => 'Your notification title',
    'tickerText' => 'Your ticker text',
    'message' => 'Your notification message. Bla, bla, bla, bla.',
    'android' => [
        'tag' => 'YourTagIfYouNeedIt'
    ],
    'ios' => [
        'priority' => 10
    ]
];

// [OPTIONAL] You can also pass custom data to the notification. Default => []
$payload = [ 
    'myCustomField' => 'This is the content of my customField',
    'anotherCustomField' => 'More custom content'
];

// [OPTIONAL] And define, if you need it, a silent notification. Default => false
$silent = true;

// [OPTIONAL] Or/and even a scheduled dateteime. Default => ''
$scheduled = '2016-12-10 10:30:10';


// Configure notification:
$ionicPush->notifications->setConfig($notificationConfig, $payload, $silent, $scheduled);

// Send notification...
$ionicPush->notifications->sendPushToAll(); // ...to all registered devices
$ionicPush->notifications->sendPush([$desiredToken1, $desiredToken2, $desiredToken3]); // ...to some devices
```

<br>
    
##### *NOTE: You will need to parse the returned array of the methods that return information.*
