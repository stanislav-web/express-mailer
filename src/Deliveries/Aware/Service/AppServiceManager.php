<?php
namespace Deliveries\Aware\Service;
use Deliveries\Aware\Adapter\Storage\DataProviderInterface;
use Deliveries\Aware\Adapter\Mail\MailProviderInterface;
use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;
use Deliveries\Aware\Helpers\FormatTrait;

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
            throw new \RuntimeException('Submission create status not defined');
        }

        // get lists by status argument
        $lists = $this->storageInstance->getLists($status);

        try {
            // push lists to queue processing & get process id
            $pid = $this->queueInstance->push($lists);

            // date create verification
            $this->verifyDate($options['date']);

            try {

                // save process id , adapter to storage
                $this->storageInstance->saveQueue($pid, [
                    ':storage'  =>  $this->getClassName($this->storageInstance),
                    ':broker'   =>  $this->getClassName($this->queueInstance),
                    ':mail'     =>  $this->getClassName($this->mailInstance),
                    ':priority' =>  $this->getClassName($this->mailInstance),
                ], $options['date'], $options['priority']);
            }
            catch(\PDOException $e) {

                // remove queue from list
                $this->queueInstance->delete();

                throw new \RuntimeException(
                    'Create queue failed: '.$e->getMessage()
                );
            }
        }
        catch(\Exception $e) {
            throw new \RuntimeException(
                'Create queue failed: '.$e->getMessage()
            );
        }

        return $pid;
    }

    /**
     * Run mails queue
     *
     * @throws \RuntimeException
     */
    public function runQueue(array $options) {

        try {

            // date create verification
            $this->verifyDate($options['date']);

            // get queues
            $queues = $this->storageInstance->getQueues($options['date'], $options['limit']);

            //@todo parse queues pids
            print_r($queues); exit;

        }
        catch(\Exception $e) {
            throw new \RuntimeException(
                'Get queue failed: '.$e->getMessage()
            );
        }
    }
}