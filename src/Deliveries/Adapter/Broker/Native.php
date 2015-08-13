<?php
namespace Deliveries\Adapter\Broker;

use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;
use Deliveries\Exceptions\BrokerException;

/**
 * Native class. Native PHP Connection broker
 * This class provides wrappers for the System V IPC family of functions. It includes semaphores,
 * shared memory and inter-process messaging (IPC).
 * Semaphores may be used to provide exclusive access to resources on the current machine,
 * or to limit the number of processes that may simultaneously use a resource
 *
 * @package Deliveries
 * @subpackage Deliveries\Adapter\Broker
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Adapter/Broker/Native.php
 */
class Native implements QueueProviderInterface {

    /**
     * Broker queue process id
     *
     * @var int $queuePid
     */
    private $queuePid = null;

    /**
     * Max message body size
     *
     * @var int $msgMaxSize
     */
    private $msgMaxSize = 65536;

    /**
     * Permission to save pid
     *
     * @var int $msgPermissions
     */
    private $msgPermissions = 0666;

    /**
     * Message type
     *
     * @var int $msgType
     */
    private $msgType = null;

    /**
     * Message container
     *
     * @var mixed $message
     */
    private $message = null;

    /**
     * Init error handler
     */
    public function __construct() {
        $this->errorHandler();
    }
    /**
     * Get instance connection
     *
     * @return \Deliveries\Adapter\Broker\Native
     */
    public function getInstance() {
        return $this;
    }

    /**
     * Connect to msg
     *
     * @param array $config
     * @return \Deliveries\Adapter\Broker\Native
     */
    public function connect(array $config) {
        return $this;
    }

    /**
     * Push message
     *
     * @param array $data
     * @throws \Deliveries\Exceptions\BrokerException
     * @return int queue process id
     */
    public function push(array $data)
    {
        $this->queuePid = mt_rand(00000, 99999);

        $queue = msg_get_queue($this->queuePid, $this->msgPermissions);

        if (!$queue) {
            throw new BrokerException(sprintf("msg_get_queue failed for key 0x%08x", $this->queuePid));
        }

        if(msg_send($queue, 1, json_encode($data), false, false, $errno) === false) {

            throw new BrokerException(
                'Could not add message to queue. Pid: '.$this->queuePid
            );
        }

        return $this->queuePid;
    }

    /**
     * Read message
     *
     * @param int $pid
     * @param callable $callback
     */
    public function read($pid = null, callable $callback)
    {
        // get process id
        $this->queuePid = (is_null($pid) === false) ? $pid : $this->queuePid;

        // get queue by process id
        $queue = msg_get_queue($this->queuePid, $this->msgPermissions);

        // queue read content
        while(msg_receive($queue, 1, $this->msgType, $this->msgMaxSize, $this->message, MSG_NOERROR)) {

            // process this message
            $callback(json_decode($this->message, true));

            //finally, reset our msg vars for when we loop and run again
            $this->msgType = null;
            $this->message = null;
        }

        // remove process from queue
        $this->delete($this->queuePid);
    }

    /**
     * Delete message from queue
     *
     * @param int $pid
     * @param callable $callback function handler
     * @return boolean
     */
    public function delete($pid = null, callable $callback = null)
    {
        $this->queuePid = (is_null($pid) === false) ? $pid : $this->queuePid;

        if(msg_queue_exists($this->queuePid) === true) {
            msg_remove_queue(msg_get_queue($this->queuePid));
            return true;
        }

        return false;
    }

    /**
     * Init error handler
     * @throws \Deliveries\Exceptions\BrokerException
     */
    private function errorHandler() {

        // setup e_warnings error handler
        set_error_handler(function($errno, $errstr) {

            throw new BrokerException(
                'Code ('.$errno.') '.$errstr
            );

        }, E_WARNING);
    }
}