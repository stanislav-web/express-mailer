<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\ProgressTrait;
use Deliveries\Aware\Helpers\FormatTrait;

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

    private $valid = 0;
    private $invalid = 0;

    /**
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'START_PROCESS'     =>  "Validate process for `%s` is started",
        'STATE_PROCESS'     => " \033[1;30mEmails check status:\033[1;30m \033[0;32m%s\033[0;32m / \033[5;31m%s\033[0;30m",
        'DONE_PROCESS'      =>  "Checking complete",
    ];

    use ProgressTrait, FormatTrait;

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

            $this->subscribersVerify($output, $serviceManager, $request);
        }
    }

    /**
     * Verify subscribers email addresses
     *
     * @param OutputInterface                       $output
     * @param \Deliveries\Service\AppServiceManager $serviceManager
     * @param array                                 $request
     */
    private function subscribersVerify(OutputInterface $output, \Deliveries\Service\AppServiceManager $serviceManager, array $request) {

        $subscribers = $serviceManager->getUncheckedSubscribers($request['subscribers']);
        $countSubscribers = count($subscribers);
        $this->logOutput($output, sprintf($this->prompt['START_PROCESS'], 'subscribers'), '<bg=white;options=bold>%s</>');

        // create progress instance with total of subscribers
        $progress = $this->getProgress($output, $countSubscribers, 'very_verbose');
        $progress->start();

        $i = 0;
        while ($i < $countSubscribers) {

            // verify subscriber email via SMTP
            $status = $serviceManager->verifyEmail($subscribers[$i]['email'], true, true);

            // count checked email
            if($status->isValid() === true) {
                ++$this->valid;
            }
            else {
                ++$this->invalid;
            }

            // print process data
            $progress->advance().' '.printf($this->prompt['STATE_PROCESS'], (int)$this->valid, (int)$this->invalid);

            $i++;
        }

        // process done
        $progress->finish();
        $this->logOutput($output, "\n".sprintf($this->prompt['DONE_PROCESS']), ' <bg=white;options=bold>%s</>');
    }
}