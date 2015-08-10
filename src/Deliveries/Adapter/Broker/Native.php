<?php
namespace Deliveries\Adapter\Broker;

use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;

/**
 * Native class. Native PHP Connection broker
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
    public function connect(array $config)
    {
        return $this;
    }

    /**
     * Push message
     *
     * @param array $data
     * @throws \RuntimeException
     * @return int queue process id
     */
    public function push(array $data)
    {
        $this->queuePid = mt_rand(00000, 99999);
        $queue = msg_get_queue($this->queuePid);

        if(msg_send($queue, 1, $data) === false) {
            throw new \RuntimeException(
                'Could not add message to queue. Pid: '.$queue
            );
        }

        return $this->queuePid;
    }

    /**
     * Get message
     *
     * @return mixed
     */
    public function get()
    {
        if(is_null($this->queuePid) === false) {
            $queue = msg_get_queue($this->queuePid);

            $msg_type = NULL;
            $msg = NULL;
            $max_msg_size = 512;

            while(msg_receive($queue, 1, $msg_type, $max_msg_size, $msg)) {

                print_r($msg);

                //do your business logic here and process this message!

                //finally, reset our msg vars for when we loop and run again
                $msg_type = NULL;
                $msg = NULL;
            }
        }
    }

    /**
     * Delete message
     *
     * @param array $data
     * @return boolean
     */
    public function delete(array $data)
    {
        // TODO: Implement delete() method.
    }
}