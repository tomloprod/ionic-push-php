<?
use Tomloprod\IonicApi\Exception\RequestException;
use Tomloprod\IonicApi\Push;

$ionicProfile = "yourIonicProfile";
$ionicAPIToken = "youtIonicApiToken";

$ionicPushApi = new Push($ionicProfile, $ionicAPIToken);
?>

<h1>List all device tokens:</h1>

<ul>
    <?
    $desiredUserId = "e5axv...";

    try {
        $response = $ionicPushApi->deviceTokens->paginatedList([
            'show_invalid' => 1,
            'user_id' => $desiredUserId,
            'page_size' => 4,
            'page' => 1
        ]);

        foreach($response->data as $deviceToken){
            ?>
            <li>
                <p><b>Device token ID:</b> <?= $deviceToken['id']; ?> </p>
                <p><b>Token:</b> <?= $deviceToken['token']; ?> </p>
                <p><b>Valid:</b> <?= $deviceToken['valid']; ?> </p>
                <p><b>Invalidated:</b> <?= $deviceToken['invalidated']; ?> </p>
                <p><b>Type:</b> <?= $deviceToken['type']; ?> </p>
                <p><b>Created at:</b> <?= $deviceToken['created']; ?> </p>
                <p><b>App ID:</b> <?= $deviceToken['app_id']; ?> </p>
            </li>
            <?
        }
    } catch(RequestException $e) {
        // Three ways to do the same thing:
        ?><li>Error! Code: <?= $e->getCode() ?> | Message: <?= $e->getMessage()?> | Link: <?= $e->getLink() ?></li><?
        ?><li><?=$e->prettify()?></li><?
        ?><li><?=$e?></li><?
    }
    ?>
</ul>

<hr/>

<h1>Retrieve device token info by token:</h1>

<ul>
    <?
    try {
        $response = $ionicPushApi->deviceTokens->retrieve("4c4ea40...");
        ?>
        <li>
            <p><b>Device token ID:</b> <?= $response->data['id']; ?> </p>
            <p><b>Token:</b> <?= $response->data['token']; ?> </p>
            <p><b>Valid:</b> <?= $response->data['valid']; ?> </p>
            <p><b>Invalidated:</b> <?= $response->data['invalidated']; ?> </p>
            <p><b>Type:</b> <?= $response->data['type']; ?> </p>
            <p><b>Created at:</b> <?= $response->data['created']; ?> </p>
            <p><b>App ID:</b> <?= $response->data['app_id']; ?> </p>
        </li>
        <?
    } catch(RequestException $e) {
        // Three ways to do the same thing:
        ?><li>Error! Code: <?= $e->getCode() ?> | Message: <?= $e->getMessage()?> | Link: <?= $e->getLink() ?></li><?
        ?><li><?=$e->prettify()?></li><?
        ?><li><?=$e?></li><?
    }
    ?>
</ul>