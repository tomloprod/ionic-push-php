<?php

namespace Tomloprod\IonicApi\Api;

/**
 * Class Messages
 *
 * Stores ionic push api methods related to messages collection.
 * More info: https://docs.ionic.io/api/endpoints/push.html
 *
 * @package Tomloprod\IonicApi\Api
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class Messages extends Request {

    private static $endPoints = [
        'retrieve' => '/push/messages/:message_id', // GET
        'delete' => '/push/messages/:message_id' // DELETE
    ];

    /**
     * Get Message details. Use this method to check the current status of a message or to lookup the error code for failures.
     *
     * @param string $messageId - Message ID
     * @return ApiResponse
     */
    public function retrieve($messageId) {
        return $this->sendRequest(
            self::METHOD_GET,
            str_replace(':message_id', $messageId, self::$endPoints['retrieve'])
        );
    }

    /**
     * Deletes a message.
     *
     * @param string $messageId - Message ID
     * @return ApiResponse
     */
    public function delete($messageId) {
        return $this->sendRequest(
            self::METHOD_DELETE,
            str_replace(':message_id', $messageId, self::$endPoints['delete'])
        );
    }

}
