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
     */
    public function post(array $data);

    /**
     * Get message
     *
     * @param array $data
     * @return mixed
     */
    public function get(array $data);

    /**
     * Delete message
     *
     * @param array $data
     * @return boolean
     */
    public function delete(array $data);

}