<?php
namespace Deliveries\Aware\Service;
use Deliveries\Aware\Adapter\Storage\DataProviderInterface;
use Deliveries\Aware\Adapter\Mail\MailProviderInterface;
use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;
use Deliveries\Aware\Helpers\FormatTrait;
use Deliveries\Exceptions\AppException;

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

    const PENDING = 'pending';
    const ABORT = 'abort';
    const SENT = 'sent';
    const FAILED = 'failed';

    use FormatTrait;

    /**
     * Mail Adapter instance
     *
     * @var MailProviderInterface $mailInstance
     */
    private $mailInstance;

    /**
     * Queue Adapter instance
     *
     * @var QueueProviderInterface $queueInstance
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
     * Create queue mailing by status only
     *
     * @param array $options
     * @throws \RuntimeException
     * @return int $pid created process id
     */
    public function createQueue(array $options) {

        // get status from options
        $status = array_search(true, $options, true);

        if(empty($status) === true) {
            throw new AppException('Submission create status not defined', 'info');
        }

        // get lists by status argument
        $lists = $this->storageInstance->getLists($status);

        try {
            // push lists to queue processing & get process id
            $pid = $this->queueInstance->push($lists);


            // date create verification
            $this->verifyDate($options['date']);

            // save process id , adapter to storage
            $this->storageInstance->saveQueue($pid, [
                ':storage'  =>  $this->getClassName($this->storageInstance),
                ':broker'   =>  $this->getClassName($this->queueInstance),
                ':mail'     =>  $this->getClassName($this->mailInstance),
            ], $options['date'], $options['priority']);

        }
        catch(\Exception $e) {

            // remove queue from list
            $this->queueInstance->delete();
            throw new \RuntimeException($e->getMessage());
        }

        return $pid;
    }

    /**
     * Get queue data by process id
     *
     * @param int $pid process id
     * @param callable $callback
     */
    public function getQueueData($pid, callable $callback) {

        try {

            $this->queueInstance->read($pid, function($data) use ($callback) {
                // process message
                $callback($data);
            });

        }
        catch(\RuntimeException $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get reserved queue from storage
     *
     * @param array $options input options
     * @param callable callback handler
     * @throws \Deliveries\Exceptions\StorageException
     * @return array
     */
    public function getQueues(array $options, callable $callback) {

        try {

            // date create verification
            $this->verifyDate($options['date']);

            // get queues & subscribers
            $callback([
                'queues'         =>  $this->storageInstance->getQueues($options['date'], $options['limit']),
                'subscribers'    =>  $this->storageInstance->getSubscribers($options['subscribers'])
            ]);
        }
        catch(\RuntimeException $e) {
            throw new \Exception($e->getMessage());
        }
    }
}