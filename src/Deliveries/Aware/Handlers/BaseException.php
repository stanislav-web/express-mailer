<?php
namespace Deliveries\Aware\Handlers;

use \Psr\Log\LoggerInterface;

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
abstract class BaseException extends \RuntimeException implements LoggerInterface
{
    /**
     * Base exception type
     */
    const BASE_TYPE = 'alert';

    /**
     * use logger trait
     */
    use \Psr\Log\LoggerTrait;

    /**
     * Constructor
     *
     * @param string $message If no message is given default from child
     * @param int $code Status code, default from child
     * @param string $type Exception type as object name raised an exception
     */
    public function __construct($message, $logLevel, $type) {

        // save an exception to log
        $this->log($logLevel, $message, [
            'date' =>  (new \DateTime('now'))->format('[Y-m-d H:i:s]'),
            'type' =>  $type
        ]);

        parent::__construct($message, null);
    }

    /**
     * Setup logger
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = []) {

        var_dump($level, $message,$context); exit;
        //@TODO create logger function. setup logger params
    }

    /**
     * Get current exception name as type
     *
     * @return string
     */
    abstract function getExceptionType();
}