<?php
namespace Deliveries\Console\Command;

use Deliveries\Exceptions\AppException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Ko\ProcessManager;
use Ko\Process as ForkProcess;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Service\AppServiceManager;
use Deliveries\Aware\Helpers\ProgressTrait;
use Deliveries\Aware\Helpers\FormatTrait;

error_reporting(0);

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

    const COMMAND = 'php bin/xmail thread --command=%s --arguments=%s';

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
        'START_PROCESS'     =>  "Validate process for `%s` is started. Pid %d\n",
        'STATE_PROCESS'     => " \033[1;30mEmails check status:\033[1;30m \033[0;32m%s\033[0;32m / \033[5;31m%s\033[0;30m",
        'DONE_PROCESS'      =>  "Checking complete. Pid: %d",
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
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logo($output);

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
    private function subscribersVerify(OutputInterface $output, AppServiceManager $serviceManager, array $request) {

        // get subscribers
        $subscribers = $serviceManager->getUncheckedSubscribers($request['subscribers']);

        // count number of processes forks
        $forks = count($subscribers);
        $manager = new ProcessManager();

        // start process forks
        for ($f = 0; $f < $forks; $f++) {

            $manager->spawn(function(ForkProcess $p) use ($subscribers, $output, $f, $serviceManager) {

                // create process title
                $processTitle = sprintf($this->prompt['START_PROCESS'], 'subscribers', $p->getPid());
                $p->setProcessTitle($processTitle);
                $this->logOutput($output, $processTitle, '<bg=white;options=bold>%s</>');

                // create progress instance with total of subscribers
                $count = count($subscribers[$f]);
                //$progress = $this->getProgress($output, $count, 'very_verbose');
                //$progress->start();

                $i = 0;
                while ($i < $count) {

                    print "Fork: $f Email:$i \n";

                    // verify subscriber email via SMTP
                    $serviceManager->verifyEmail($subscribers[$f][$i]['email'], true, true);
                    //$status = $serviceManager->verifyEmail($subscribers[$f][$i]['email'], true, true);

//                    // count checked email
//                    if($status->isValid() === true) {
//                        ++$this->valid;
//                    }
//                    else {
//                        ++$this->invalid;
//                    }
//
//                    // print process data
//                    $progress->advance().' '.printf($this->prompt['STATE_PROCESS'], (int)$this->valid, (int)$this->invalid);

                    $i++;
                    //if($i >= $count) break;
                }

                // destroy the process
                $p->kill();
                //$progress->finish();
            })->onSuccess(function(ForkProcess $p) use ($output) {
                // process done
                $this->logOutput($output, sprintf($this->prompt['DONE_PROCESS'], $p->getPid()), ' <bg=white;options=bold>%s</>');
            })->wait();

            $manager->onShutdown(function(ForkProcess $p) use ($manager) {
                throw new AppException('Error process: '.$p->getPid());
            });
        }
    }
}