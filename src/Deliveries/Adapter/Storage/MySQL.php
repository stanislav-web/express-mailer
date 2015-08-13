<?php
namespace Deliveries\Adapter\Storage;

use Deliveries\Aware\Adapter\Storage\DataProviderInterface;
use Deliveries\Exceptions\StorageException;

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
     * @vat string $activeLogTable
     */
    private $activeLogTable   = 'xmail_active_log';

    /**
     * @vat string $queueTable
     */
    private $queueTable   = 'xmail_queue';

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
     * @throws \Deliveries\Exceptions\StorageException
     * @return \PDO
     */
    public function connect(array $config) {

        $dsn = "".strtolower($config['adapter']).":host=".$config['host'].";dbname=".$config['db'];

        try {
            $this->pdo = new \PDO($dsn, $config['username'], $config['password']);
            $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

            if(isset($config['prefix'])) {
                $this->setTables($config['prefix']);
            }

            return $this;

        } catch(\PDOException $e) {
            throw new StorageException(
                $config['adapter'].' error: '.$e->getMessage(), $e->getCode()
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
        $this->queueTable = $this->quoteFiled($prefix.$this->queueTable);

        return $this;
    }

    /**
     * Get subscribers
     *
     * @param string $state subscriber status
     * @param int $limit limit records
     * @return array
     */
    public function getSubscribers($state = 'active', $limit = null) {

        $query = "SELECT id AS subscriber_id, name, email FROM ".$this->subscribersTable." subscribers
                    WHERE subscribers.`state` = ".$this->quoteValue($state)."
	                ORDER BY subscribers.id ASC";

        if(is_null($limit) === false) {
            $query .= " LIMIT ".(int)$limit;
        }

        return $this->fetchAll($query);
    }

    /**
     * Count all subscribers
     *
     * @return array
     */
    public function countSubscribers() {

        $query = "SELECT COUNT(1) AS total,
                  (SELECT COUNT(1) FROM ".$this->subscribersTable." WHERE state = 'moderated') AS moderated,
                  (SELECT COUNT(1) FROM ".$this->subscribersTable." WHERE state = 'disabled') AS disabled,
                  (SELECT COUNT(1) FROM ".$this->subscribersTable." WHERE state = 'active') AS active
                  FROM ".$this->subscribersTable." USE INDEX(PRIMARY)";

        return $this->fetchOne($query);
    }

    /**
     * Count all statistics for mailings
     *
     * @return array
     */
    public function countMailings() {

        $query = "SELECT COUNT(1) AS total,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'ok') AS sent,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'pending') AS pending,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'failed') AS failed,
                  (SELECT COUNT(1) FROM ".$this->statsTable." WHERE status = 'abort') AS abort
                  FROM ".$this->statsTable." USE INDEX(PRIMARY)";

        return $this->fetchOne($query);

    }

    /**
     * Count all active mails stat
     *
     * @return array
     */
    public function activeMailsStat() {

        $query = "SELECT list.id AS listID, list.subject AS Subject, COUNT(log.subscriber_id) AS Sent
	                FROM ".$this->listsTable." AS list
                    INNER JOIN ".$this->activeLogTable." AS log ON(log.list_id = list.id)
	                GROUP BY log.`list_id`
	                ORDER BY log.`date_sent` DESC";

        return $this->fetchAll($query);

    }

    /**
     * Get lists for submissions
     *
     * @param string $status
     * @return array
     */
    public function getLists($status) {

        $query = "SELECT list.id AS list_id, list.subject, list.message FROM ".$this->statsTable." AS stats
	                RIGHT JOIN ".$this->listsTable." AS list ON (list.`id` = stats.`list_id`)
	                WHERE stats.`status` = ".$this->quoteValue($status)." AND stats.`date_finish` IS NULL
	                ORDER BY stats.id";

        return $this->fetchAll($query);
    }

    /**
     * Save queue process in storage
     *
     * @param int $pid
     * @param array $params additional insert params
     * @param datetime $date_activation
     * @param int $priority
     * @throws \Deliveries\Exceptions\StorageException
     * @return int
     */
    public function saveQueue($pid, array $params, $date_activation = null, $priority = 0) {

        $query = "INSERT INTO ".$this->queueTable."
                    (pid, storage, broker, mail, priority, date_activation)
                        VALUES (:pid, :storage, :broker, :mail, :priority, :date_activation)";

        // prepare bind & execute query
        return $this->exec($query, array_merge([
            ':pid'               =>  (int)$pid,
            ':date_activation'   =>  $date_activation,
            ':priority'          =>  (int)$priority
        ], $params));
    }

    /**
     * Remove queue
     *
     * @param int $pid
     * @throws \Deliveries\Exceptions\StorageException
     * @return int
     */
    public function removeQueue($pid) {

        $query = "DELETE FROM ".$this->queueTable."
                    WHERE pid = :pid";

        // prepare bind & execute query
        return $this->exec($query, [
            ':pid'               =>  (int)$pid,
        ]);
    }

    /**
     * Get queues process from storage
     *
     * @param string $date
     * @param int $limit limit records
     * @return array
     */
    public function getQueues($date = null, $limit = null) {

        $query = "SELECT * FROM ".$this->queueTable." queue
                    WHERE `date_activation` >= '".$date."'
	                ORDER BY queue.priority DESC";

        if(is_null($limit) === false) {
            $query .= " LIMIT ".(int)$limit;
        }

        return $this->fetchAll($query);
    }

    /**
     * Import tables
     *
     * @param string $query
     * @return bool|int
     */
    public function importTables($query) {
        return $this->exec($query);
    }

    /**
     * Execute query
     *
     * @param string $query
     * @param array $bindData
     * @throws \Deliveries\Exceptions\StorageException
     * @return boolean|int
     */
    private function exec($query, array $bindData = []) {

        try {
            if(empty($bindData) === false) {
                return $this->prepare($query)->execute($bindData);
            }
            return $this->getInstance()->exec($query);
        }
        catch(\PDOException $e) {

            throw new StorageException($e->getMessage());
        }
    }

    /**
     * Get result query for multiple rows
     *
     * @param string $query
     * @throws \Deliveries\Exceptions\StorageException
     * @return array
     */
    private function fetchAll($query) {

        try {

            $stmt = $this->getInstance()->query($query);
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);

        }
        catch(\PDOException $e) {
            throw new StorageException(
                $e->getMessage()
            );
        }
    }

    /**
     * Get result query for row
     *
     * @param string $query
     * @throws \Deliveries\Exceptions\StorageException
     * @return array
     */
    private function fetchOne($query) {

        try {

            $stmt = $this->getInstance()->query($query);
            return $stmt->fetch(\PDO::FETCH_ASSOC);

        }
        catch(\PDOException $e) {
            throw new StorageException(
                $e->getMessage()
            );
        }
    }

    /**
     * Escape value
     *
     * @param string $query
     * @return string
     */
    private function quoteValue($value) {
        return $this->getInstance()->quote($value);
    }

    /**
     * Escape arguments
     *
     * @param string $field
     * @return string
     */
    private function quoteFiled($field) {
        return "`".str_replace(["'"], ["`"], $field)."`";
    }

    /**
     * Prepare query statement
     *
     * @param string $query
     * @return \PDOStatement
     */
    private function prepare($query) {
        return $this->getInstance()->prepare($query);
    }
}