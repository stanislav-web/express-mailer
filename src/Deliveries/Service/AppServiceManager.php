<?php
namespace Deliveries\Service;

use Deliveries\Aware\Adapter\Storage\DataProviderInterface;
use Deliveries\Aware\Adapter\Mail\MailProviderInterface;
use Deliveries\Aware\Adapter\Broker\QueueProviderInterface;
use Deliveries\Aware\Helpers\FormatTrait;
use Deliveries\Exceptions\AppException;
use Deliveries\Service\AppCacheService as Cache;

/**
 * AppServiceManager class.
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Service
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Service/AppServiceManager.php
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

        $this->storageInstance = $storage;
        $this->mailInstance = $mail;
        $this->queueInstance = $queue;

    }

    /**
     * Import tables
     *
     * @param string $query
     * @return bool|int
     */
    public function importTables ($query) {
        $this->storageInstance->importTables($query);
    }

    /**
     * Get subscribers statistics
     *
     * @return array
     */
    public function getSubscribersReports() {

        return $this->storageInstance->countSubscribers();
    }

    /**
     * Get deliveries statistics
     *
     * @return array
     */
    public function getMailingsReports() {

        return $this->storageInstance->countMailings();
    }

    /**
     * Get active mail statistics
     *
     * @return array
     */
    public function getActiveMailStatistics() {

        return $this->storageInstance->activeMailsStat();
    }

    /**
     * Create queue mailing by status only
     *
     * @param array $options
     * @throws \Deliveries\Exceptions\AppException
     * @throws \Exception
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
        catch(\RuntimeException $e) {

            // remove queue from list
            $this->queueInstance->delete();
            throw new \Exception($e->getMessage(), $e->getCode());
        }

        return $pid;
    }

    /**
     * Delete queue from storage
     *
     * @param int $pid process id
     * @return int
     */
    public function removeQueue($pid) {

        return $this->storageInstance->removeQueue($pid);
    }

    /**
     * Get queue data by process id
     *
     * @throws \Exception
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
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Get reserved queue from storage
     *
     * @param array $options input options
     * @param callable callback handler
     * @throws \Exception
     * @return array
     */
    public function getQueues(array $options, callable $callback) {

        try {

            // date create verification
            $this->verifyDate($options['date']);

            // get queues & subscribers
            $callback([
                'queues'         =>  $this->storageInstance->getQueues($options),
                'subscribers'    =>  $this->storageInstance->getSubscribers($options['subscribers'])
            ]);
        }
        catch(\RuntimeException $e) {
            throw new \Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Send message to recipient
     *
     * @param array $recipient ['test@email.com' => 'TestName']
     * @param array $msg message params with placeholders
     * @return int count of successfully
     */
    public function sendMessage(array $recipient, array $msg) {

        // formatting mail data placeholders for put into message body
        $placeholders = $this->arrayKeysPlaceholders(array_merge($recipient, $msg));

        // send message to recipient
        return $this->mailInstance->send($recipient, $msg['subject'], $msg['message'], $placeholders);
    }

    /**
     * Get unchecked subscribers by state
     *
     * @param string $state subscriber status
     * @return array
     */
    public function getUncheckedSubscribers($state) {

        // get unchecked subscribers
        $key = md5(__FUNCTION__.$state);

        $subscribers =  (Cache::exists($key))
            ? Cache::fetch($key) : $this->storageInstance->getSubscribers($state, 0);

        Cache::store($key, $subscribers);

        return $this->arrayPartition($subscribers, $this->cpuCoreCount());
    }

    /**
     * Get number of CPU
     *
     * @used It serves to perform threading
     * @return int
     */
    public function cpuCoreCount() {

        $num = 1;

        if(is_file('/proc/cpuinfo') === true) {
            $info = file_get_contents('/proc/cpuinfo');
            preg_match_all('/^processor/m', $info, $matches);

            $num = count($matches[0]);
        }
        else {

            $process = @popen('sysctl -a', 'rb');
            if($process != false) {

                $output = stream_get_contents($process);
                preg_match('/hw.ncpu: (\d+)/', $output, $matches);

                if($matches) {
                    $num = (int)$matches[1][0];
                }
                pclose($process);
            }
        }

        return $num;
    }
}