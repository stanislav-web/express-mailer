<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
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
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'START_PROCESS'     =>  "Start process #%d / %d mail receivers",
        'DONE_PROCESS'      =>  "Done list #%d",
        'DONE_ALL'          =>  "Sending completed",
    ];

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
     * @return null
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
        $serviceManager = $this->getAppServiceManager();

        foreach($request['queues'] as $queue) {

            $this->logOutput($output, sprintf($this->prompt['START_PROCESS'], $queue['pid'], $request['subscribersTotal']), "<bg=white;options=bold>%s</>");

            // get queue by process id
            $serviceManager->getQueueData($queue['pid'], function($processData) use ($output, $request, $serviceManager) {

                foreach($processData as $data) {

                    // create progress instance with total of subscribers
                    $progress = $this->getProgress($output, $request['subscribersTotal'], 'debug');
                    $progress->start();

                    $i = 0;
                    while ($i++ < $request['subscribersTotal']) {

                        // send message
                        $serviceManager->sendMessage($request['subscribers'][$i], $data);

                        $progress->advance();
                    }
                    $progress->finish();
                    $progress->getMessage();

                    $this->logOutput($output, sprintf($this->prompt['DONE_PROCESS'], $data['list_id']), " <bg=white;options=bold>%s</>");
                }
            });

            // remove waste processes from storage
            $serviceManager->removeQueue($queue['pid']);
        }

        // final message
        $this->logOutput($output, $this->prompt['DONE_ALL'], "<fg=white;bg=magenta>%s</fg=white;bg=magenta>");
    }
}