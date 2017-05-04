<?php

namespace Tomloprod\IonicApi\Api;

/**
 * Class ApiResponse
 *
 * @package Tomloprod\IonicApi\Api
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class ApiResponse {

    /**
     * HTTP Code Status
     *
     * @var number
     */
    public $status;

    /**
     * Response success?
     *
     * @var boolean
     */
    public $success;

    /**
     * Response data
     *
     * @var array|null
     */
    public $data;

    /**
     * Response error
     * @var null|array
     */
    public $error;


    /**
     * ApiResponse constructor.
     *
     * @param $response
     * @param number $httpStatusCode
     */
    public function __construct($response, number $httpStatusCode) {
        $this->status = $httpStatusCode;
        $response = json_decode($response, true);

        switch($httpStatusCode) {
            case 200:
            case 201:
            case 202:
                $this->success = true;
                $this->data = $response['data'];
                $this->error = null;
                break;
            case 204:
                $this->success = true;
                $this->data = null;
                $this->error = null;
                break;
            default:
                $this->success = false;
                $this->data = null;
                $this->error = $response['error'];
                break;
        }
    }

    /**
     * Returns a quick summary about the error that occurred.
     *
     * @return string
     */
    public function getErrorMessage() {
        if(empty($this->error)) {
            return "Sorry, there is no error message!";
        }

        return $this->error['message'];
    }

}
