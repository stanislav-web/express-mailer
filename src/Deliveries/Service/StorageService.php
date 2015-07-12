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
}