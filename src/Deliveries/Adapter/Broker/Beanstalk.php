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
    const DEFAULT_PORT = 11300;

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
     * @return \Pheanstalk\Pheanstalk
     */
    public function getInstance() {
        return $this->broker;
    }

    /**
     * Connect to AMQP server
     *
     * @param array $config
     * @return \Pheanstalk\Pheanstalk
     * @throws \RuntimeException
     */
    public function connect(array $config)
    {
        $host = (empty($config['host']) === true) ? self::DEFAULT_HOST : $config['host'];
        $port = (empty($config['port']) === true) ? self::DEFAULT_PORT : $config['port'];
        $timeout = (empty($config['timeout']) === true) ? self::DEFAULT_TIMEOUT : $config['timeout'];
        $persistent = (empty($config['persistent']) === true) ? self::DEFAULT_IS_PERSISTENT : $config['persistent'];

        $this->broker = new QueueBroker($host, $port, $timeout, $persistent);
        $isConnect = $this->broker->getConnection()->isServiceListening();

        if(!$isConnect) {
            throw new \RuntimeException('Queue connection failed! Check configurations');
        }

        return $this;
    }

    /**
     * Push message
     *
     * @param array $data
     * @throws \RuntimeException
     * @return \Pheanstalk\Pheanstalk
     */
    public function push(array $data)
    {
        $this->broker->useTube(mt_rand(00000, 99999))->put($data);
        return $this->broker;
    }

    /**
     * Read message
     *
     * @param int $pid
     * @param callable $callback
     */
    public function read($pid = null, callable $callback)
    {
        // TODO: Implement read() method.
    }

    /**
     * Delete message from queue
     *
     * @param int $pid
     * @return boolean
     */
    public function delete($pid = null)
    {
        // TODO: Implement delete() method.
    }
}