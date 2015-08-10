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

    private $exceptions = [

    ];

    /**
     * Constructor
     *
     * @param array $data additional info
     * @param string $message If no message is given 'Not Found' will be the message
     * @param int $code Status code, defaults to 404
     */
    public function __construct($code, $message = null) {

        if(is_null($message) === true) {
            $message = self::MESSAGE;
        }

        parent::__construct($message, $code);
    }
}