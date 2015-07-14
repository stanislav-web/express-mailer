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
     * Broker process
     */
    protected $broker;

    /**
     * Get instance connection
     *
     * @return QueueBroker
     */
    public function getInstance() {
        return $this->broker;
    }

    /**
     * Connect server
     *
     * @param array $config
     * @return boolean $isConnect
     */
    public function connect(array $config = []) {
        $this->broker = pcntl_fork();
    }

    /**
     * Push message
     *
     * @param array $data
     */
    public function post(array $data)
    {
        // TODO: Implement post() method.
    }

    /**
     * Get message
     *
     * @param array $data
     * @return mixed
     */
    public function get(array $data)
    {
        // TODO: Implement get() method.
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