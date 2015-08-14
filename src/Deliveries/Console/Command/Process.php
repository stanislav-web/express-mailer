<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\ProgressBar;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\ProgressTrait;

/**
 * Process class. Application Process mailing
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Process.php
 */
class Process extends BaseCommandAware {

    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'process';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Process mailing';

    /**
     * Start process format message
     *
     * @const START_PROCESS_DESCRIPTION
     */
    const START_PROCESS_DESCRIPTION = 'Start process #%d';

    /**
     * Send list format message
     *
     * @const SEND_PROCESS_DESCRIPTION
     */
    const SEND_PROCESS_DESCRIPTION = 'Send mail list #%d';

    /**
     * Done list process description
     *
     * @const DONE_PROCESS_DESCRIPTION
     */
    const DONE_PROCESS_DESCRIPTION = 'Done list #%d';

    /**
     * Complete all mailing
     *
     * @const COMPLETE_SENDING
     */
    const COMPLETE_ALL = "Sending completed";

    use ProgressTrait;

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions()
    {

        return [
            new InputOption('queues', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Queues'),
            new InputOption('subscribers', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Subscribers'),
            new InputOption('queueTotal', null, InputOption::VALUE_REQUIRED, 'Queues counter'),
            new InputOption('subscribersTotal', null, InputOption::VALUE_REQUIRED, 'Subscribers count')
        ];
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        // checking config
        if($this->isConfigExist() === false) {
            throw new \RuntimeException(
                'Configuration file does not exist! Run `xmail init`'
            );
        }

        // get requested data
        $request = $input->getOptions();

        foreach($request['queues'] as $queue) {

            $this->logOutput($output, sprintf(self::START_PROCESS_DESCRIPTION, $queue['pid']), "<bg=white;options=bold>%s</>");

            // get queue by process id
            $this->getAppServiceManager()->getQueueData($queue['pid'], function($processData) use ($output, $request) {

                foreach($processData as $data) {

                    // start to send list
                    $this->logOutput($output, sprintf(self::SEND_PROCESS_DESCRIPTION, $data['list_id']));

                    // create progress instance with total of subscribers
                    $progress = $this->getProgress($output, $request['subscribersTotal'], 'debug');
                    $progress->start();

                    $i = 0;
                    while ($i++ < $request['subscribersTotal']) {

                        // send message
                        $progress->advance();
                    }
                    $progress->finish();
                    $progress->getMessage();

                    $this->logOutput($output, sprintf(self::DONE_PROCESS_DESCRIPTION, $data['list_id']), " <bg=white;options=bold>%s</>");
                }
            });

            // remove waste processes from storage
            $this->getStorageServiceManager()->removeQueue($queue['pid']);
        }

        // final message
        $this->logOutput($output, self::COMPLETE_ALL, "<fg=white;bg=magenta>%s</fg=white;bg=magenta>");
    }
}