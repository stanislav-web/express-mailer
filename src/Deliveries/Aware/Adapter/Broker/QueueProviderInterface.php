<?php
namespace Deliveries\Aware\Adapter\Broker;

/**
 * QueueProviderInterface interface. Broker queue interface
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Adapter\Broker
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Adapter/Broker/QueueProviderInterface.php
 */
interface QueueProviderInterface {

    /**
     * Get instance connection
     */
    public function getInstance();

    /**
     * Connect to AMQP server
     *
     * @param array $config
     */
    public function connect(array $config);

    /**
     * Push message
     *
     * @param array $data
     * @return int queue process id
     */
    public function push(array $data);

    /**
     * Read message
     *
     * @param int $pid
     * @param callable $callback
     */
    public function read($pid = null, callable $callback);

    /**
     * Delete message from queue
     *
     * @param int $pid
     * @param callable $callback function handler
     * @return boolean
     */
    public function delete($pid = null, callable $callback = null);

}