# ionic-push-php [![Release](https://img.shields.io/github/release/tomloprod/ionic-push-php.svg)](https://github.com/tomloprod/ionic-push-php) [![Join the chat at https://gitter.im/tomloprod/ionic-push-php](https://badges.gitter.im/tomloprod/ionic-push-php.svg)](https://gitter.im/tomloprod/ionic-push-php?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge&utm_content=badge) [![License](https://img.shields.io/github/license/tomloprod/ionic-push-php.svg)](http://www.opensource.org/licenses/mit-license.php)

ionic-push-php is a library that allows you to consume the *Ionic Cloud API* for **sending push notifications** (*normal and scheduled*), get a paginated **list of sending push notifications**,  get **information of registered devices**, **remove registered devices by token**, ...

Ionic official documentation: [Ionic HTTP API - Push](https://docs.ionic.io/api/endpoints/push.html).

## Requirements:

- PHP 5.1+
- cURL

## Installation:

    composer require tomloprod/ionic-push-php

## Configuration:


First, make sure you have your `$ionicAPIToken` and your `$ionicProfile`:

- (string) **$ionicAPIToken:** The API token that you must create in *Settings › API Keys* in the [Dashboard](https://apps.ionic.io).
- (string) **$ionicProfile:** The Security Profile tag found in *Settings › Certificates* in the [Dashboard](https://apps.ionic.io)

> More information [here](https://github.com/tomloprod/ionic-push-php/issues/1).

If you don't know how to configure your ionic app, you can take a look here: [Setup Ionic Push](http://docs.ionic.io/services/push/#setup)


## Exceptions

This library could throw any of the follow exceptions:

- AuthException
- NotFoundException
- BadRequestException

## How to use:

First, instance an object as follow:

```php
use Tomloprod\IonicApi\Push,
    Tomloprod\IonicApi\Exception\AuthException,
    Tomloprod\IonicApi\Exception\NotFoundException,
    Tomloprod\IonicApi\Exception\BadRequestException;

$ionicPushApi = new Push($ionicProfile, $ionicAPIToken);
```

Then you can interact (*list, remove, create, ...*) with `device tokens`, `messages` and `notifications`.

Remember that all the interactions returns an **ApiResponse** object instance, except __notifications->deleteAll__ that returns an array of **ApiResponse**s.


### [Device Tokens]

 **1) List tokens:**

```php
try {

  $response = $ionicPushApi->deviceTokens->paginatedList([
      // Determines whether to include invalidated tokens (boolean)
      'show_invalid' => 1,
      // Only display tokens associated with the User ID (string)
      'user_id' => $desiredUserId,
      // Sets the number of items to return per page (integer)
      'page_size' => 4,
      // Sets the page number (integer)
      'page' => 1
  ]);

  foreach($response->data as $deviceToken){        
      print_r($deviceToken);
  }

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**2) List users associated with a device token:**

```php
try {

  $response = $ionicPushApi->deviceTokens->listAssociatedUsers($desiredDeviceToken, [
      // Sets the number of items to return per page (integer)
      'page_size' => 1,
      // Sets the page number (integer)
      'page' => 1,
  ]);

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**3) Associate a user with a device token:**

```php
try {

  $deviceToken = "c686...";
  $userId = "a99ee...";
  $ionicPushApi->deviceTokens->associateUser($deviceToken, $userId);

  // Here, the user has been associated.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**4) Dissociate a user with a device token:**

```php
try {

  $deviceToken = "c686...";
  $userId = "a99ee...";
  $ionicPushApi->deviceTokens->dissociateUser($deviceToken, $userId);

  // Here, the user has been dissociated.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**5) Create device token that was previously generated by a device platform:**

```php
try {

  $response = $ionicPushApi->deviceTokens->create([
      // Device token (string)
      'token' => $newToken,
      // User ID. Associate the token with the User (string)
      'user_id' => $uuid
  ]);

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**6) Retrieve device information related to the device token:**

```php
try {

  $response = $ionicPushApi->deviceTokens->retrieve($desiredDeviceToken);

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**5) Update an specific token:**

```php
try {

  $isValid = true; // Determines whether the device token is valid (boolean)
  $ionicPushApi->deviceTokens->update($desiredDeviceToken, ['valid' => $isValid]);

  // Here, the device token has been updated.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**6) Delete a device related to the device token:**

```php
try {

  $ionicPushApi->deviceTokens->delete($desiredDeviceToken);

  // Here, the device token has been deleted.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

### [Messages]

**1) Retrieve specific message:**

```php
try {
  $response = $ionicPushApi->messages->retrieve($desiredMessageId);

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**2) Delete a message:**

```php
try {
  $ionicPushApi->messages->delete($desiredMessageId);

  // Here, the message has been deleted.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

### [Notifications]

**1) List notifications:**
```php

try {

  $response = $ionicPushApi->notifications->paginatedList([
      // Sets the number of items to return per page (integer)
      'page_size' => 1,
      // Sets the page number (integer)
      'page' => 1,
      // You can also pass other fields like "message_total" or "overview" (string[])
      'fields' => [
          // Total number of messages tied to each notification.
          'message_total',
          // Get an overview of messages delivered and failed for each notification.
          'overview'
      ]
  ]);

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**2) Retrieve specific notification:**

```php
try {

  $response = $ionicPushApi->notifications->retrieve($desiredNotificationId);

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**3) Delete a notification:**

```php
try {
  $ionicPushApi->notifications->delete($desiredNotificationId);

  // Here, notification has been deleted.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**4) Delete all notifications:**

```php
try {
?
  $responses = $ionicPushApi->notifications->deleteAll();

  // Here, notifications have been deleted.

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

**5) List messages of a notification:**

```php
try {

  $response = $ionicPushApi->notifications->listMessages($desiredNotificationId, [
      // Sets the number of items to return per page (integer)
      'page_size' => 1,
      // Sets the page number (integer)
      'page' => 1
  ])

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
 ```

**6) Send notifications:**

```php
/**
* ANDROID [OPTIONAL] CONFIG PARAMETERS
*/

// Filename of the Icon to display with the notification (string)
$icon = "icon";

// Filename or URI of an image file to display with the notification (string)
$image = "image";

// Indicates whether each notification message results in a new entry on the notification center on Android.
// If not set, each request creates a new notification.
// If set, and a notification with the same tag is already being shown, the new notification replaces the existing one in notification center.
$tag = "yourTagIfYouNeedIt";

// When this parameter is set to true, it indicates that the message should not be sent until the device becomes active. (boolean)
$delayWhileIdle = false;

// Identifies a group of messages that can be collapsed, so that only the last message gets sent when delivery can be resumed. (string)
$collapseKey = "group1";


/**
* IOS [OPTIONAL] CONFIG PARAMETERS
*/

// Message Priority. A value of 10 will cause APNS to attempt immediate delivery.
// A value of 5 will attempt a delivery which is convenient for battery life. (integer)
$priority = 10;

// The number to display as the badge of the app icon (integer)
$badge = 1;

// Alert Title, only applicable for iWatch devices
$iWatchTitle = "Hi!";


// Assign the previously defined configuration parameters to each platform, as well as the title and message:
$notificationConfig = [
    'title' => 'Your notification title',
    'message' => 'Your notification message. Bla, bla, bla, bla.',
    'android' => [
        'tag' => $tag,
        'icon' => $icon,
        'image' => $image,
        'delay_while_idle' => $delayWhileIdle,
        'collapse_key' => $collapseKey
    ],
    'ios' => [
        'priority' => $priority,
        'badge' => $badge,
        'title' => $iWatchTitle
    ]
];

// [OPTIONAL] You can also pass custom data to the notification. Default => []
$notificationPayload = [
    'myCustomField' => 'This is the content of my customField',
    'anotherCustomField' => 'More custom content'
];

// [OPTIONAL] And define, if you need it, a silent notification. Default => false
$silent = true;

// [OPTIONAL] Or/and even a scheduled notification for an indicated datetime. Default => ''
$scheduled = '2016-12-10 10:30:10';

// [OPTIONAL] Filename of audio file to play when a notification is received. Setting this to default will use the default device notification sound. Default => 'default'
$sound = 'default';

// Configure notification:
$ionicPushApi->notifications->setConfig($notificationConfig, $notificationPayload, $silent, $scheduled, $sound);

try {

  // Send notification...
  $response = $ionicPushApi->notifications->sendNotificationToAll(); // ...to all registered devices
  // or
  $response = $ionicPushApi->notifications->sendNotification([$desiredToken1, $desiredToken2, $desiredToken3]); // ...to some devices

  // Do what you want with $response->data

} catch(AuthException $e) { // Auth errors
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(NotFoundException $e){ // Not found exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
} catch(BadRequestException $e) { // Bad request exceptions
  echo $e->getCode(). " ". $e->getType(). " : " .$e->getMessage();
}
```

## Contributing:
1. Fork it
1. Create your feature branch (git checkout -b my-new-feature)
1. Commit your changes (git commit -m 'Add some feature')
1. Push to the branch (git push origin my-new-feature)
1. Create new Pull Request


## TODO:

1. Methods replace() ~~and listMessages()~~ of **Notifications**.
1. Methods ~~listAssociatedUsers(), associateUser() and dissociateUser()~~ of **DeviceTokens**.
1. New examples for versions >= 1.3.0.
