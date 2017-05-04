<?php
use Tomloprod\IonicApi\Push;
$ionicPushApi = new Push($ionicProfile, $ionicAPIToken);
?>

<h1>IMPORTANT! - Examples <b>DEPRECATED</b> since 1.3.0</h1>

<h2>List all device tokens:</h2>

<ul>
    <?php
    $deviceTokens = $ionicPushApi->deviceTokens->paginatedList();
    // If $deviceTokens is not null, you can loop through the device tokens.
    if($deviceTokens !== null){
        foreach($deviceTokens->data as $deviceToken){
    ?>
        <li>    
            <p> <b>Device token ID:</b> <?php echo $deviceToken->id; ?> </p>
            <p> <b>Token:</b> <?php echo $deviceToken->token; ?> </p>
            <p> <b>Valid:</b> <?php echo $deviceToken->valid; ?> </p>
            <p> <b>Invalidated:</b> <?php echo $deviceToken->invalidated; ?> </p>
            <p> <b>Type:</b> <?php echo $deviceToken->type; ?> </p>
            <p> <b>Created at:</b> <?php echo $deviceToken->created; ?> </p>
            <p> <b>App ID:</b> <?php echo $deviceToken->app_id; ?> </p>
        </li>
    <?php 
        }
    } else {
    ?>
            <li>Response is null!</li>
    <?php
    }
    ?>
</ul>

<h2>Retrieve device token info by token:</h2>

<ul>
    <?php
    $deviceToken = $ionicPushApi->deviceTokens->retrieve("4c4ea40...");
    if($deviceToken !== null){
    ?>
        <li>    
            <p> <b>Device token ID:</b> <?php echo $deviceToken->data->id; ?> </p>
            <p> <b>Token:</b> <?php echo $deviceToken->data->token; ?> </p>
            <p> <b>Valid:</b> <?php echo $deviceToken->data->valid; ?> </p>
            <p> <b>Invalidated:</b> <?php echo $deviceToken->data->invalidated; ?> </p>
            <p> <b>Type:</b> <?php echo $deviceToken->data->type; ?> </p>
            <p> <b>Created at:</b> <?php echo $deviceToken->data->created; ?> </p>
            <p> <b>App ID:</b> <?php echo $deviceToken->data->app_id; ?> </p>
        </li>
    <?php 
    } else {
    ?>
            <li>Response is null!</li>
    <?php
    }
    ?>
</ul>