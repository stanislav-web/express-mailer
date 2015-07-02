<?php
namespace Deliveries\Adapter\Broker;

use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;
use Pheanstalk\Pheanstalk as QueueBroker;

/**
 * Beanstalk class. Beanstalk Connection broker
 *
 * @package Deliveries
 * @subpackage Deliveries\Adapter\Broker
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Adapter/Broker/Beanstalk.php
 */
class Beanstalk implements QueueProviderInterface {

    /**
     * Default broker host
     */
    const DEFAULT_HOST = 'localhost';

    /**
     * Default broker port
     */
    const DEFAULT_PORT = 9000;

    /**
     * Default connection timeout
     */
    const DEFAULT_TIMEOUT = 30;

    /**
     * Default connection type
     */
    const DEFAULT_IS_PERSISTENT = 'false';

    /**
     * Broker connection
     *
     * @var \Pheanstalk\Pheanstalk $broker
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
     * Connect to AMQP server
     *
     * @param array $config
     * @return boolean $isConnect
     */
    public function connect(array $config)
    {
        $host = (empty($config['host']) === true) ? self::DEFAULT_HOST : $config['host'];
        $port = (empty($config['port']) === true) ? self::DEFAULT_PORT : $config['port'];
        $timeout = (empty($config['timeout']) === true) ? self::DEFAULT_TIMEOUT : $config['timeout'];
        $persistent = (empty($config['persistent']) === true) ? self::DEFAULT_IS_PERSISTENT : $config['persistent'];

        $this->broker = new QueueBroker($host, $port, $timeout, $persistent);
        $isConnect = $this->broker->getConnection()->isServiceListening();

        return $isConnect;
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