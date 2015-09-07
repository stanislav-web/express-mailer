<?php
namespace Deliveries\Adapter\Broker;

use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;
use Deliveries\Exceptions\BrokerException;
use Pheanstalk\Exception;
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

    const DEFAULT_TUBE = 'xmail';

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
     * @throws \Deliveries\Exceptions\BrokerException
     * @return \Pheanstalk\Pheanstalk
     */
    public function connect(array $config)
    {
        $host = (empty($config['host']) === true) ? self::DEFAULT_HOST : $config['host'];
        $port = (empty($config['port']) === true) ? self::DEFAULT_PORT : $config['port'];
        $timeout = (empty($config['timeout']) === true) ? self::DEFAULT_TIMEOUT : $config['timeout'];
        $persistent = (empty($config['persistent']) === true) ? self::DEFAULT_IS_PERSISTENT : $config['persistent'];

        $this->broker = new QueueBroker($host, $port, $timeout, $persistent);
        /** @noinspection PhpUndefinedMethodInspection */
        $isConnect = $this->broker->getConnection()->isServiceListening();

        if(!$isConnect) {
            throw new BrokerException('Queue connection failed! Check configurations');
        }

        return $this;
    }

    /**
     * Push message
     *
     * @param array $data
     * @return int queue process id
     */
    public function push(array $data)
    {
        $pid = $this->broker->useTube(self::DEFAULT_TUBE)->put(json_encode($data));

        return $pid;
    }

    /**
     * Read message
     *
     * @param int $pid
     * @param callable $callback
     * @throws \Deliveries\Exceptions\BrokerException
     */
    public function read($pid = null, callable $callback)
    {
        try {

            $data = json_decode($this->broker->peek($pid)->getData(), true);

            if(is_null($data) === false) {

                // process this message
                $callback($data);

                // remove from queue
                $this->delete($pid);
            }
            else {
                // remove broken job from queue precess
                $this->delete($pid, function() use ($callback, $pid) {
                    $callback('Queue process #'.$pid.' was broken and immediately removed from queue list');
                });
            }
        }
        catch(Exception $e) {
            throw new BrokerException($e->getMessage());
        }
    }

    /**
     * Delete message from queue
     *
     * @param int $pid
     * @param callable $callback function handler
     * @throws \Deliveries\Exceptions\BrokerException
     * @return boolean
     */
    public function delete($pid = null, callable $callback = null)
    {
        try {

            // get broker job by process id
            $job = $this->broker->peek($pid);

            // remove process
            $this->broker->delete($job);

            if(is_null($callback) === false) {
                $callback();
            }

            return true;
        }
        catch(Exception $e) {
            throw new BrokerException($e->getMessage());
        }
    }
}