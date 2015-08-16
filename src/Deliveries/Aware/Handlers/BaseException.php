<?php
namespace Deliveries\Aware\Handlers;

/**
 * BaseException class. Base exception handler
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Handlers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Handlers/BaseException.php
 */
class BaseException extends \RuntimeException {

    /**
     * @const DELIMITER default
     */
    const DELIMITER = ' : ';

    /**
     * Constructor
     *
     * @param string $message If no message is given default from child
     * @param string $code Status code, default from child
     */
    public function __construct($message, $code) {

        $message = static::TYPE.self::DELIMITER.$message; // use as late state binding

        // save an exception to log
        (new Logger())->{$code}($message);

        parent::__construct($message, static::CODE);
    }
}