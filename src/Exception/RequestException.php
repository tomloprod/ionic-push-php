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

    /**
     * The type of error that occurred.
     *
     * @var string
     */
    protected $type;

    /**
     * A link to our docs to get more information about the error.
     *
     * @var
     */
    protected $link;

    /**
     * RequestException constructor.
     *
     * @param string $type
     * @param int $message A quick summary about the error that occurred.
     * @param string $link
     * @param int $code
     * @param null $e
     */
    public function __construct($type, $message, $link = "", $code = 0, $e = null) {
        //$message = $type. " : " . $message;
        $this->type = $type;
        $this->link = $link;
        parent::__construct($message, $code, $e);
    }

    /**
     * Type getter.
     *
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * Link getter.
     *
     * @return string
     */
    public function getUrl() {
        return $this->link;
    }
}
