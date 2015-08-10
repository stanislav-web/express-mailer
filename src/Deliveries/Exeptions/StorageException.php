<?php
namespace Deliveries\Exceptions;

/**
 * StorageException class. Storage exception class
 *
 * @package Deliveries
 * @subpackage Deliveries\Exceptions
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Exceptions/StorageException.php
 */
class StorageException extends \RuntimeException {

    /**
     * Exception codes
     *
     * @var array $exceptions
     */
    private $exceptions = [

    ];

    /**
     * Constructor
     *
     * @param array $data additional info
     * @param string $message
     * @param int $code Status code
     */
    public function __construct($code, $message = null) {

        if(is_null($message) === true) {
            $message = $this->exceptions[$code];
        }

        parent::__construct($message, $code);
    }
}