<?php
namespace Deliveries\Exceptions;

use Deliveries\Aware\Handlers\BaseException;

/**
 * BrokerException class. Storage exception class
 *
 * @package Deliveries
 * @subpackage Deliveries\Exceptions
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Exceptions/BrokerException.php
 */
class BrokerException extends BaseException {

    /**
     * @const TYPE exception type as object name raised an exception
     */
    const TYPE = 'BrokerException';

    /**
     * @const CODE exception code
     */
    const CODE = 500;

    /**
     * Constructor
     *
     * @param string $message If no message is given default from child
     * @param string $code Status code, default from child
     */
    public function __construct($message, $code = 'critical') {
        parent::__construct($message, $code);
    }
}