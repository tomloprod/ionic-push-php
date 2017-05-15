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
class AuthException extends RequestException{

   public function __construct($type, $message, $code = 0, $e = null) {
       parent::__construct($type, $message, $code, $e);
   }

}
