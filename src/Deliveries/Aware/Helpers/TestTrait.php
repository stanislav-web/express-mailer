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
     * Testing for connect to Mail Server
     *
     * @return boolean
     */
    public function isMailConnectSuccess(array $config) {

        if(empty($config) === false) {

            $Mail = "\\Deliveries\\Adapter\\Mail\\".$config["adapter"];

            if(true === class_exists($Mail)) {

                $connect = (new $Mail())->connect($config);

                if($connect === false) {
                    throw new \RuntimeException('Connection to mail server: '.$config["adapter"].' is not allow. Check configurations');
                }
                return $connect;
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

                $connect = (new $Broker())->connect($config);

                if($connect === false) {
                    throw new \RuntimeException('Connection to AMQP server: '.$config["adapter"].' is not allow. Check configurations');
                }
                return $connect;
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

            $dsn = new $Storage();

            if($dsn->isSupport()) {
                return $dsn->connect($config);
            }
            throw new \RuntimeException($config["adapter"]. ' is not supported');
        }
        throw new \RuntimeException($config["adapter"]. ' is not exist');

    }

}