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
     * Execute query
     *
     * @return boolean
     */
    public function exec($query);

    /**
     * Get result query
     *
     * @return array|object
     */
    public function fetchAll($query);
}