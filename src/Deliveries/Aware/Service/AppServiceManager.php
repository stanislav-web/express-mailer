<?php
namespace Deliveries\Aware\Service;
use Deliveries\Aware\Adapter\Storage\DataProviderInterface;
use Deliveries\Aware\Adapter\Mail\MailProviderInterface;
use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;

/**
 * AppServiceManager class.
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Service
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Service/AppServiceManager.php
 */
class AppServiceManager {

    /**
     * Mail Adapter instance
     *
     * @var DataProviderInterface $mailInstance
     */
    private $mailInstance;

    /**
     * Queue Adapter instance
     *
     * @var DataProviderInterface $queueInstance
     */
    private $queueInstance;

    /**
     * Storage Adapter instance
     *
     * @var DataProviderInterface $storageInstance
     */
    private $storageInstance;

    /**
     * Initialize scopes of objects
     *
     * @param DataProviderInterface  $storage
     * @param MailProviderInterface  $mail
     * @param QueueProviderInterface $queue
     */
    public function __construct(DataProviderInterface $storage, MailProviderInterface $mail, QueueProviderInterface $queue) {

        $this->queueInstance = $queue;
        $this->mailInstance = $mail;
        $this->storageInstance = $storage;
    }

    /**
     * Submit mailing by status only
     *
     * @param string $status
     */
    public function submit($status) {

    }

}