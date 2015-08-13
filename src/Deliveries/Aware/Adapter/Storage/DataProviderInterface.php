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
     * @param array $params additional insert params
     * @param datetime $date_activation
     * @param int $priority
     * @return int
     */
    public function saveQueue($pid, array $params, $date_activation = null, $priority = 0);

    /**
     * Remove queue
     *
     * @param int $pid
     * @return int
     */
    public function removeQueue($pid);

    /**
     * Get queues process from storage
     *
     * @param string $date
     * @param int $limit limit records
     * @return array
     */
    public function getQueues($date = null, $limit = null);

    /**
     * Count all subscribers
     *
     * @return array
     */
    public function countSubscribers();

    /**
     * Count all statistics for mailings
     *
     * @return array
     */
    public function countMailings();

    /**
     * Count all active mails stat
     *
     * @return array
     */
    public function activeMailsStat();

    /**
     * Get subscribers
     *
     * @param string $state subscriber status
     * @param int $limit limit records
     * @return array
     */
    public function getSubscribers($state, $limit = null);

}