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
     * Run mails queue
     *
     * @throws \Deliveries\Exceptions\StorageException
     */
    public function runQueue(array $options, callable $callback) {

        try {

            // date create verification
            $this->verifyDate($options['date']);

            try {
                // get queues
                $queues = $this->storageInstance->getQueues($options['date'], $options['limit']);

            }
            catch(\RuntimeException $e) {
                throw new \Exception($e->getMessage());
            }

            if(count($queues) > 0) {

                foreach($queues as $queue) {
                    $this->queueInstance->read($queue['pid'], function($message) use ($callback, $queue) {

                        // process received message & get subscribers & send mails
                        //print_r($message);
                        // $this->storageInstance->getSubscribers('active') to UP!

                        $callback($message);

                        // remove queue from storage after read
                        $this->storageInstance->removeQueue($queue['pid']);
                    });
                }
            }
            else {
                // not found queues
                $callback('Queues not found');
            }
        }
        catch(\RuntimeException $e) {

            throw new \Exception($e->getMessage());
        }
    }
}