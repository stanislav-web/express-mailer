<?php
namespace Deliveries\Exceptions;

use Deliveries\Aware\Handlers\BaseException;

/**
 * AppException class. Application exception class
 *
 * @package Deliveries
 * @subpackage Deliveries\Exceptions
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Exceptions/AppException.php
 */
class AppException extends BaseException {

    /**
     * @const TYPE exception type as object name raised an exception
     */
    const TYPE = 'AppException';

    /**
     * Constructor
     *
     * @param string $message
     * @param int $code status code
     */
    public function __construct($message, $code = null) {
        parent::__construct($message, $code);
    }
}