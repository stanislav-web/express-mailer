<?php
namespace Deliveries\Aware\Handlers;

use Deliveries\Aware\Helpers\FileSysTrait;
use \Psr\Log\LoggerInterface;
use \Psr\Log\LoggerTrait;

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
class BaseException extends \RuntimeException implements LoggerInterface {

    /**
     * Base exception type
     */
    const BASE_TYPE = 'alert';

    /**
     * use logger trait
     */
    use LoggerTrait, FileSysTrait;

    /**
     * Constructor
     *
     * @param string $message If no message is given default from child
     * @param int $code Status code, default from child
     */
    public function __construct($message, $logLevel) {

        // save an exception to log
        $this->log($logLevel, $message, [
            'date' =>  (new \DateTime('now'))->format('Y-m-d H:i:s'),
            'type' =>  static::TYPE // use as late state binding
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

        //@TODO create logger function. setup logger params
        print_r($this->getLoggerConfig());

        if (file_put_contents($this->filename, $line = '', FILE_APPEND | LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write to the log file.');
        }
        var_dump($level, $message,$context);
        exit;
    }

    /**
     * Get logger config
     *
     * @return array
     */
    public function getLoggerConfig() {
        return $this->getConfig()->Logger;
    }
}