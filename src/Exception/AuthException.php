<?php

namespace Tomloprod\IonicApi\Exception;

use Tomloprod\IonicApi\Exception\RequestException;

/**
 * Class AuthException
 *
 * @package Tomloprod\IonicApi\Exception
 * @author Tomás L.R (@tomloprod)
 * @author Ramon Carreras (@ramoncarreras)
 */
class AuthException extends RequestException {

    /**
     * AuthException constructor.
     *
     * @param string $type
     * @param int $message
     * @param string $link
     * @param int $code
     * @param null $e
     */
    public function __construct($type, $message, $link = "", $code = 0, $e = null) {
        parent::__construct($type, $message, $link, $code, $e);
    }

}
