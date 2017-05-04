<?
use Tomloprod\IonicApi\Push;

$ionicProfile = "yourIonicProfile";
$ionicAPIToken = "youtIonicApiToken";

$ionicPushApi = new Push($ionicProfile, $ionicAPIToken);
?>


<h1>List all notifications and get data:</h1>

<ul>
    <?
    $response = $ionicPushApi->notifications->paginatedList();
    // If $notifications is not null, you can loop through the notifications.
    if($response->success) {
        foreach($response->data as $notification) {
            ?>
            <li>
                <p><b>Notification ID:</b> <?= $notification['uuid']; ?> </p>
                <p><b>Title:</b> <?= $notification['config']['notification']['title']; ?> </p>
                <p><b>Message:</b> <?= $notification['config']['notification']['message']; ?> </p>
                <p><b>Created at:</b> <?= $notification['created']; ?> </p>
            </li>
            <?
        }
    }
    else {
        ?>
        <li>Error response: Code <?= $response->status ?></li>
        <?
    }
    ?>
</ul>

<hr/>

<h1>Retrieve notification by uuid:</h1>

<ul>
    <?
    $response = $ionicPushApi->notifications->retrieve("e5aaf...");
    if($response->success) {
        ?>
        <li>
            <p><b>Notification ID:</b> <?= $response->data['uuid']; ?> </p>
            <p><b>Title:</b> <?= $response->data['config']['notification']['title']; ?> </p>
            <p><b>Message:</b> <?= $response->data['config']['notification']['message']; ?> </p>
            <p><b>Created at:</b> <?= $response->data['created']; ?> </p>
        </li>
        <?
    }
    else {
        ?>
        <li>Error response: Code <?= $response->status ?></li>
        <?
    }
    ?>
</ul>

<hr/>
