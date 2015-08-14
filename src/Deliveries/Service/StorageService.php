<?php
namespace Deliveries\Service;

use Deliveries\Aware\Adapter\Storage\DataProviderInterface;

/**
 * StorageService class. Storage Data Service
 *
 * @package Deliveries
 * @subpackage Deliveries\Service
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Service/StorageService.php
 */
class StorageService {

    /**
     * Data provider
     *
     * @var \Deliveries\Aware\Adapter\Storage\DataProviderInterface
     */
    private $storage;

    /**
     * Assign data provider
     *
     * @param \Deliveries\Aware\Adapter\Storage\DataProviderInterface $storage
     */
    public function __construct(DataProviderInterface $storage) {

        $this->storage = $storage;
    }

    /**
     * Get subscribers statistics
     *
     * @return array
     */
    public function getSubscribersReports() {

        return $this->storage->countSubscribers();
    }

    /**
     * Get deliveries statistics
     *
     * @return array
     */
    public function getMailingsReports() {

        return $this->storage->countMailings();
    }

    /**
     * Get active mail statistics
     *
     * @return array
     */
    public function getActiveMailStatistics() {

        return $this->storage->activeMailsStat();
    }

    /**
     * Get queues process from storage
     *
     * @param string $date
     * @param int $limit limit records
     * @return array
     */
    public function getQueues($date, $limit = null) {

        return $this->storage->getQueues($date, $limit);
    }

    /**
     * Delete queue from storage
     *
     * @param int $pid process id
     * @return int
     */
    public function removeQueue($pid) {

        return $this->storage->removeQueue($pid);
    }

    /**
     * Get subscribers
     *
     * @param string $state subscriber status
     * @param int $limit limit records
     * @return array
     */
    public function getSubscribers($state, $limit = null) {

        return $this->storage->getSubscribers($state, $limit);
    }

    /**
     *Execute queries for create table
     *
     * @param string $query
     */
    public function importTables($query) {

        return $this->storage->importTables($query);
    }
}