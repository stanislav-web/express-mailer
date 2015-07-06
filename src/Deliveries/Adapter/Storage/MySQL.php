<?php
namespace Deliveries\Adapter\Storage;

use Deliveries\Aware\Adapter\Storage\DataProviderInterface;

/**
 * MySQL class. MySQL Storage
 *
 * @package Deliveries
 * @subpackage Deliveries\Adapter\Storage
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Adapter/Storage/MySQL.php
 */
class MySQL implements DataProviderInterface {

    /**
     * Default storage host
     */
    const DEFAULT_HOST = 'localhost';

    /**
     * Default storage port
     */
    const DEFAULT_PORT = 3306;

    /**
     * PDO connection
     *
     * @var \PDO $pdo
     */
    protected $pdo;

    /**
     * Check if storage already support & available
     *
     * @return boolean
     */
    public function isSupport()
    {
        if(extension_loaded('PDO') && extension_loaded('pdo_mysql')) {
            return true;
        }
        return false;
    }

    /**
     * Get instance connection
     *
     * @return \PDO
     */
    public function getInstance() {
        return $this->pdo;
    }

    /**
     * Make a connect to storage
     *
     * @param array $config
     * @throws \PDOException
     * @return boolean
     */
    public function connect(array $config)
    {
        $dsn = "".strtolower($config['adapter']).":host=".$config['host'].";dbname=".$config['db'];

        try {
            $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return true;

        } catch(\PDOException $e) {
            throw new \RuntimeException(
                $config['adapter'].' error: '.$e->getMessage()
            );
        }
    }

    /**
     * Get structure tables list
     *
     * @return array
     */
    public function getTablesList()
    {

        $query = $this->getInstance()->query("SHOW TABLES");

        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }

}