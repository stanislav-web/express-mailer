<?php
namespace Deliveries\Service;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Deliveries\Aware\Helpers\FileSysTrait;

/**
 * AppLoggerService class. Log data handler
 *
 * @package Application
 * @subpackage Helpers
 * @since PHP >=5.6
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Service/AppLoggerService.php
 */
class AppLoggerService extends LogLevel implements LoggerInterface {

    /**
     * Default date format
     *
     * @const DEFAULT_DATE_FORMAT
     */
    const DEFAULT_DATE_FORMAT = 'Y-m-d H:i:s';

    /**
     * Default log record format
     *
     * @const DEFAULT_LOG_FORMAT
     */
    const DEFAULT_LOG_FORMAT = '[date][level] message';

    /**
     * Code names
     *
     * @var array $codeName
     */
    public static $codeName = [
        parent::EMERGENCY => 1,
        parent::ALERT     => 2,
        parent::CRITICAL  => 3,
        parent::ERROR     => 4,
        parent::WARNING   => 5,
        parent::NOTICE    => 6,
        parent::INFO      => 7,
        parent::DEBUG     => 8
    ];

    /**
     * use logger trait
     */
    use FileSysTrait;

    /**
     * System is unusable.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function emergency($message, array $context = array()) {
        $this->log(self::EMERGENCY, $message, $context);
    }

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function alert($message, array $context = array()) {
        $this->log(self::ALERT, $message, $context);
    }

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function critical($message, array $context = array()) {
        $this->log(self::CRITICAL, $message, $context);
    }

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function error($message, array $context = array()) {
        $this->log(self::ERROR, $message, $context);
    }
    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function warning($message, array $context = array()) {
        $this->log(self::WARNING, $message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function notice($message, array $context = array()) {
        $this->log(self::NOTICE, $message, $context);
    }

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function info($message, array $context = array()) {
        $this->log(self::INFO, $message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function debug($message, array $context = array()) {
        $this->log(self::DEBUG, $message, $context);
    }

    /**
     * Setup logger
     *
     * @param string $level
     * @param string $message
     * @param array $context
     * @return null
     */
    public function log($level, $message, array $context = []) {
        $this->addToLog($level, $message, $context);
    }
}