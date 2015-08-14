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
     * Send list message format
     *
     * @const SEND_PROCESS_DESCRIPTION
     */
    const SEND_PROCESS_DESCRIPTION = 'Send list #%d';

    /**
     * Send list message format
     *
     * @const DONE_PROCESS_DESCRIPTION
     */
    const DONE_PROCESS_DESCRIPTION = ' Done list #%d';

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
            new InputOption('qc', null, InputOption::VALUE_REQUIRED, 'Queues counter'),
            new InputOption('qs', null, InputOption::VALUE_REQUIRED, 'Subscribers count')
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

            // get queue by process id
            $this->getAppServiceManager()->getQueueData($queue['pid'], function($processData) use ($output, $request) {

                foreach($processData as $data) {

                    // start to send list
                    $output->writeln(sprintf(self::SEND_PROCESS_DESCRIPTION, $data['list_id']));

                    // create progress instance with total of subscribers
                    $progress = $this->getProgress($output, $request['qs'], 'debug');
                    $progress->start();

                    $i = 0;
                    while ($i++ < $request['qs']) {

                        // send message
                        usleep(rand(100000, 1000000));
                        $progress->advance();
                    }
                    $progress->finish();
                    $output->writeln(sprintf(self::DONE_PROCESS_DESCRIPTION, $data['list_id']));
                }
            });
        }
    }
}