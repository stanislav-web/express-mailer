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
     * @vat string $subscribersTable
     */
    private $subscribersTable   = 'xmail_subscribers';

    /**
     * @vat string $listsTable
     */
    private $listsTable   = 'xmail_lists';

    /**
     * @vat string $statsTable
     */
    private $statsTable   = 'xmail_stats';

    /**
     * Check if storage already support & available
     *
     * @return boolean
     */
    public function isSupport() {
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
    public function connect(array $config) {
        $dsn = "".strtolower($config['adapter']).":host=".$config['host'].";dbname=".$config['db'];

        try {
            $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            return $this;

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
    public function getTablesList() {

        $query = $this->getInstance()->query("SHOW TABLES");

        return $query->fetchAll(\PDO::FETCH_COLUMN);
    }

    /**
     * Set tables
     *
     * @param string $prefix
     * @return \Deliveries\Aware\Adapter\Storage\DataProviderInterface
     */
    public function setTables($prefix) {

        $this->subscribersTable = $this->quoteFiled($prefix.$this->subscribersTable);
        $this->statsTable = $this->quoteFiled($prefix.$this->statsTable);
        $this->listsTable = $this->quoteFiled($prefix.$this->listsTable);

        return $this;
    }

    /**
     * Execute query
     *
     * @param string $query
     * @return boolean
     */
    public function exec($query) {
        return $this->getInstance()->exec($query);
    }

    /**
     * Get result query for multiple rows
     *
     * @param string $query
     * @return array
     */
    public function fetchAll($query) {
        $stmt = $this->getInstance()->query($query);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get result query for row
     *
     * @param string $query
     * @return array
     */
    public function fetchOne($query) {
        $stmt = $this->getInstance()->query($query);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * Escape value
     *
     * @param string $query
     * @return string
     */
    public function quoteValue($value) {
        return $this->getInstance()->quote($value);
    }

    /**
     * Escape arguments
     *
     * @param string $field
     * @return string
     */
    public function quoteFiled($field) {
        return "`".str_replace(["'"], ["`"], $field)."`";
    }

    /**
     * Prepare query statement
     *
     * @param string $query
     * @return bool
     */
    public function prepare($query) {
        return $this->getInstance()->prepare($query)->execute();
    }

    /**
     * Count all subscribers
     *
     * @param $table
     * @return array
     */
    public function countSubscribers() {

        $query = "SELECT COUNT(1) AS total,
                  (SELECT COUNT(1) FROM ".$this->subscribersTable." WHERE state = 'moderated') AS moderated,
                  (SELECT COUNT(1) FROM ".$this->subscribersTable." WHERE state = 'disabled') AS disabled,
                  (SELECT COUNT(1) FROM ".$this->subscribersTable." WHERE state = 'active') AS active
                  FROM ".$this->subscribersTable;
        return $this->fetchOne($query);
    }

    /**
     * Count all statistics for deliveries messages
     *
     * @param $table
     * @return array
     */
    public function countDeliveries() {

        $query = "SELECT COUNT(1) AS total,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'ok') AS send,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'pending') AS pending,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'failed') AS failed,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'abort') AS abort
                  FROM ".$this->statsTable;

        return $this->fetchOne($query);
    }
}