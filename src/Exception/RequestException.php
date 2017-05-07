<?php

namespace Tomloprod\IonicApi\Exception;

 /**
  * Class RequestException
  *
  * @package Tomloprod\IonicApi\Exception
  * @author Tomás L.R (@tomloprod)
  * @author Ramon Carreras (@ramoncarreras)
  */
class RequestException extends \Exception {

   protected $type;

   public function __construct($type, $message, $code = 0, $e = null) {
       //$message = $type. " : " . $message;
       $this->type = $type;
       parent::__construct($message, $code, $e);
   }

   public function getType(){
     return $this->type;
   }
}
