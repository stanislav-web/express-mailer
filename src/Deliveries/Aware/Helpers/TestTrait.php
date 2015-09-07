<?php
namespace Deliveries\Aware\Helpers;

use Deliveries\Exceptions\MailException;

/**
 * TestTrait trait.
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Helpers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Helpers/TestTrait.php
 */
trait TestTrait {

    /**
     * Mail Adapter instance
     *
     * @var \Deliveries\Aware\Adapter\Mail\MailProviderInterface $mailInstance
     */
    private $mailInstance;

    /**
     * Queue Adapter instance
     *
     * @var \Deliveries\Aware\Adapter\Broker\QueueProviderInterface $queueInstance
     */
    private $queueInstance;

    /**
     * Storage Adapter instance
     *
     * @var \Deliveries\Aware\Adapter\Storage\DataProviderInterface $storageInstance
     */
    private $storageInstance;

    /**
     * Get mail instance object
     *
     * @param array $config
     * @return \Deliveries\Aware\Adapter\Mail\MailProviderInterface
     */
    private function getMailInstance($config = null) {

        return (null === $this->mailInstance) ?
            $this->isMailConnectSuccess($config)
            : $this->mailInstance;
    }

    /**
     * Get storage instance object
     *
     * @param array $config
     * @return \Deliveries\Aware\Adapter\Storage\DataProviderInterface
     */
    protected function getStorageInstance($config = null) {
        return (null === $this->storageInstance) ?
            $this->isStorageConnectSuccess($config)
            : $this->storageInstance;
    }

    /**
     * Get queue instance object
     *
     * @param array $config
     * @return \Deliveries\Aware\Adapter\Broker\QueueProviderInterface
     */
    protected function getQueueInstance($config = null) {
        return (null === $this->queueInstance) ?
            $this->isQueueConnectSuccess($config)
            : $this->queueInstance;
    }

    /**
     * Testing for connect to Mail Server
     *
     * @param array $config
     * @throws \Deliveries\Exceptions\MailException
     * @return boolean
     */
    public function isMailConnectSuccess(array $config) {

        if(empty($config) === false) {

            $Mail = "\\Deliveries\\Adapter\\Mail\\".$config["adapter"];

            if(true === class_exists($Mail)) {

                try {

                    /** @noinspection PhpUndefinedMethodInspection */
                    $connect = (new $Mail())->connect($config);
                    /** @noinspection PhpUndefinedMethodInspection */
                    $this->mailInstance = $connect->getInstance();
                    return $connect;
                }
                catch(\RuntimeException $e) {
                    throw new MailException('Connection to mail server: '.$config["adapter"].' is not allow. Check configurations');
                }
            }
            throw new MailException($config["adapter"]. ' mail adapter is not exist');
        }
        throw new MailException('Mail config is not exist');
    }

    /**
     * Testing for connect to Queue
     *
     * @param array $config
     * @throws \RuntimeException
     * @return boolean
     */
    public function isQueueConnectSuccess(array $config) {

        if(empty($config) === false) {

            $Broker = "\\Deliveries\\Adapter\\Broker\\".$config["adapter"];

            if(true === class_exists($Broker)) {

                /** @noinspection PhpUndefinedMethodInspection */
                $this->queueInstance = (new $Broker())->connect($config);

                if($this->queueInstance === false) {
                    throw new \RuntimeException('Connection to AMQP server: '.$config["adapter"].' is not allow. Check configurations');
                }
                return $this->queueInstance;
            }
            throw new \RuntimeException($config["adapter"]. ' broker adapter is not exist');
        }
        throw new \RuntimeException('Broker config is not exist');
    }

    /**
     * Testing for connect to DB Storage
     *
     * @param array $config
     * @throws \RuntimeException
     * @return boolean
     */
    public function isStorageConnectSuccess(array $config) {

        $Storage = "\\Deliveries\\Adapter\\Storage\\".$config["adapter"];

        if(true === class_exists($Storage)) {

            $this->storageInstance = new $Storage();

            if($this->storageInstance->isSupport()) {
                return $this->storageInstance->connect($config);
            }
            throw new \RuntimeException($config["adapter"]. ' is not supported');
        }
        throw new \RuntimeException($config["adapter"]. ' is not exist');
    }

}