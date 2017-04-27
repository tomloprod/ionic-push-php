<?php

namespace Tomloprod\IonicPush\Api;

/**
 * Class DeviceTokensApi
 *
 * Stores ionic push api methods related to device tokens collection.
 * More info: https://docs.ionic.io/api/endpoints/push.html
 *
 * @package Tomloprod\IonicPush\Api
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class DeviceTokensApi extends IonicApiRequest {

    private static $endPoints = [
        'list' => '/push/tokens', // GET
        'create' => '/push/tokens', // POST
        'retrieve' => '/push/tokens/:token_id', // GET
        'update' => ' /push/tokens/:token_id', // PATCH
        'delete' => '/push/tokens/:token_id', // DELETE,
        'listAssociatedUsers' =>'/push/tokens/:token_id/users', // GET
        'associateUser' =>' /push/tokens/:token_id/users/:user_id', // POST
        'dissociateUser' =>'/push/tokens/:token_id/users/:user_id', // DELETE
    ];

    /**
     * Paginated listing of tokens.
     *
     * @param array $parameters
     * @return array
     */
    public function paginatedList($parameters) {
        $getParameters = http_build_query($parameters);
        return $this->sendRequest(self::METHOD_GET, self::$endPoints['list'] . '?' . $getParameters);
    }

    // TODO: create

    /**
     * Get information about a device of a specific device token.
     *
     * @param string $deviceToken - Device token
     * @return array
     */
    public function retrieve($deviceToken) {
        return $this->prepareRequest(self::METHOD_GET, $deviceToken, self::$endPoints['retrieve']);
    }

    // TODO: update

    /**
     * Delete a device related to the device token.
     *
     * @param string $deviceToken - Device token
     * @return array
     */
    public function delete($deviceToken) {
        return $this->prepareRequest(self::METHOD_DELETE, $deviceToken, self::$endPoints['delete']);
    }

    // TODO: list associated users

    // TODO: associate user

    // TODO: dissociate user

    /**
     * Used by "retrieve" and "deleteDevice".
     *
     * @private
     * @param string $method
     * @param string $deviceToken
     * @param string $endPoint
     * @return array
     */
    private function prepareRequest($method, $deviceToken, $endPoint) {
        return $this->sendRequest($method, str_replace(':token_id', md5($deviceToken), $endPoint));
    }

}
