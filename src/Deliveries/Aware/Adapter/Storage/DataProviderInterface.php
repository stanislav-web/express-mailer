<?php
namespace Deliveries\Aware\Adapter\Storage;

/**
 * DataProviderInterface interface. Storages interface
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Adapter\Storage
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Adapter/Storage/DataProviderInterface.php
 */
interface DataProviderInterface {

    /**
     * Check if storage already support & available
     *
     * @return boolean
     */
    public function isSupport();

    /**
     * Get instance connection
     */
    public function getInstance();

    /**
     * Make a connect to storage
     *
     * @param array $config
     */
    public function connect(array $config);

    /**
     * Get structure tables list
     *
     * @return mixed
     */
    public function getTablesList();

    /**
     * Set tables
     *
     * @param string $prefix
     * @return \Deliveries\Aware\Adapter\Storage\DataProviderInterface
     */
    public function setTables($prefix);

    /**
     * Execute query
     *
     * @param string $query
     * @param array $bindData
     * @return boolean|int
     */
    public function exec($query, array $bindData = []);

    /**
     * Prepare query string
     *
     * @param string $query
     * @return array|object
     */
    public function prepare($query);

    /**
     * Get result query for multiple rows
     *
     * @param string $query
     * @return array
     */
    public function fetchAll($query);

    /**
     * Get result query for row
     *
     * @param string $query
     * @return array
     */
    public function fetchOne($query);

    /**
     * Escape value
     *
     * @param string $value
     * @return string
     */
    public function quoteValue($value);

    /**
     * Escape field
     *
     * @param string $field
     * @return string
     */
    public function quoteFiled($field);

    /**
     * Get lists for submissions
     *
     * @param string $status
     * @return array
     */
    public function getLists($status);

    /**
     * Save queue process in storage
     *
     * @param int $pid
     * @param datetime $date_activation
     * @return int
     */
    public function saveQueue($pid, $date_activation = null);
}