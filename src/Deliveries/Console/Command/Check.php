<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\ProgressTrait;

/**
 * Check class. Application checking command
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Check.php
 */
class Check extends BaseCommandAware {

    /**
     * Command logo
     *
     * @const NAME
     */
    const LOGO = "###################\nCheck tools #######\n###################";

    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'check';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Check tools. Validate subscriber\'s list & process';

    /**
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'START_PROCESS'     =>  "Validate process for `%s` is started",
        'STATE_PROCESS'     => " \033[1;30mEmails check status:\033[1;30m \033[0;32m%d\033[0;32m / \033[5;31m%d\033[0;30m",
        'DONE_PROCESS'      =>  "\nChecking complete",
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
            new InputOption('queues', null, InputOption::VALUE_OPTIONAL, 'Queues'),
            new InputOption('subscribers', null, InputOption::VALUE_OPTIONAL | InputOption::VALUE_NONE, 'Subscribers'),
            new InputOption('autoremove', null, InputOption::VALUE_NONE, 'Remove invalid'),

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
        $this->logo();

        // checking config
        if($this->isConfigExist() === false) {
            throw new \RuntimeException(
                'Configuration file does not exist! Run `xmail init`'
            );
        }

        // get requested data
        $request = $input->getOptions();
        $serviceManager = $this->getAppServiceManager();

        // check subscribers for valid
        if($request['subscribers'] !== false) {

            $subscribers = $serviceManager->getUncheckedSubscribers($request['subscribers']);
            $countSubscribers = count($subscribers);
            $this->logOutput($output, sprintf($this->prompt['START_PROCESS'], 'subscribers'), "<bg=white;options=bold>%s</>");

            // create progress instance with total of subscribers
            $progress = $this->getProgress($output, $countSubscribers, 'debug');
            $progress->start();

            $i = 0; $valid = 0; $invalid = 0;
            while ($i < $countSubscribers) {

                // validate subscriber
                $status = $serviceManager->verifyEmail($subscribers[$i]['email']);

                // count checked email
                $valid += ($status->isValid() === true) ? 1 : 0;

                // print process data
                printf($this->prompt['STATE_PROCESS'], $valid, $invalid).$progress->advance();

                $i++;
            }


            $progress->finish();
            $this->logOutput($output, sprintf($this->prompt['DONE_PROCESS']), " <bg=white;options=bold>%s</>");
            //$serviceManager->verifyEmail($subscribers, function($validate) use ($subscribers, $output) {


            //});
        }
    }
}