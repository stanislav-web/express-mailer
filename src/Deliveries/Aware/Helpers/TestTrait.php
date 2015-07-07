<?php
namespace Deliveries\Aware\Helpers;

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
     * @var object $mailInstance
     */
    public $mailInstance;

    /**
     * Queue Adapter instance
     *
     * @var object $queueInstance
     */
    public $queueInstance;

    /**
     * Storage Adapter instance
     *
     * @var object $storageInstance
     */
    public $storageInstance;

    /**
     * @return object
     */
    public function getMailInstance()
    {
        return $this->mailInstance;
    }

    /**
     * @return object
     */
    public function getStorageInstance()
    {
        return $this->storageInstance;
    }

    /**
     * @return object
     */
    public function getQueueInstance()
    {
        return $this->queueInstance;
    }

    /**
     * Testing for connect to Mail Server
     *
     * @return boolean
     */
    public function isMailConnectSuccess(array $config) {

        if(empty($config) === false) {

            $Mail = "\\Deliveries\\Adapter\\Mail\\".$config["adapter"];

            if(true === class_exists($Mail)) {

                $this->mailInstance = (new $Mail())->connect($config);

                if($this->mailInstance === false) {
                    throw new \RuntimeException('Connection to mail server: '.$config["adapter"].' is not allow. Check configurations');
                }
                return $this->mailInstance;
            }
            throw new \RuntimeException($config["adapter"]. ' mail adapter is not exist');
        }
        throw new \RuntimeException('Mail config is not exist');
    }

    /**
     * Testing for connect to Queue
     *
     * @return boolean
     */
    public function isQueueConnectSuccess(array $config) {

        if(empty($config) === false) {

            $Broker = "\\Deliveries\\Adapter\\Broker\\".$config["adapter"];

            if(true === class_exists($Broker)) {

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
     * @return bool
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