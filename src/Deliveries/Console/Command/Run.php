<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Deliveries\Aware\Console\Command\BaseCommandAware;

/**
 * Run class. Application Run submission from queue
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Run.php
 */
class Run extends BaseCommandAware {

    /**
     * Command logo
     *
     * @const NAME
     */
    const LOGO = "###################\nSubmission runner #\n###################";

    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'run';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Run submissions from queue';

    /**
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'MAILINGS_PROCESSES'        =>  "<comment>Prepare process mailings : %d</comment>\n",
        'MAILINGS_SUBSCRIBERS'      =>  "<comment>Prepare subscribers : %d</comment>\n",
        'MAILINGS_LETTERS'          =>  "<comment>Prepare letters to send : %d</comment>\n",
        'QUEUES_NOT_FOUND'          =>  "<comment>No active queues fond</comment>"
    ];

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            new InputOption('pid', null, InputOption::VALUE_OPTIONAL, 'Process identifier'),
            new InputOption('date', null, InputOption::VALUE_OPTIONAL, 'Queue start date', (new \DateTime())->format('Y-m-d')),
            new InputOption('limit', null, InputOption::VALUE_OPTIONAL, 'Limit queue lists'),
            new InputOption('subscribers', null, InputOption::VALUE_OPTIONAL, 'Subscribers state', 'active'),
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

        // Get active queues
        $this->getAppServiceManager()->getQueues($input->getOptions(), function($data) use ($output) {

            if(($queueTotal = count($data['queues'])) > 0) {

                // active queues founded
                $subscribersTotal = count($data['subscribers']);
                $mailsTotal = ($subscribersTotal * $queueTotal);

                $output->writeln(
                    sprintf($this->prompt['MAILINGS_PROCESSES'], $queueTotal).
                    sprintf($this->prompt['MAILINGS_SUBSCRIBERS'], $subscribersTotal).
                    sprintf($this->prompt['MAILINGS_LETTERS'], $mailsTotal).
                    "---------------------------------------------------"
                );

                // migrate to process mailing

                $inputForJob = new ArrayInput([
                    'command'               => 'process',
                    '--queues'              => $data['queues'],
                    '--subscribers'         => $data['subscribers'],
                    '--queueTotal'          => $queueTotal,
                    '--subscribersTotal'    => $subscribersTotal,
                ]);
                return  $this->getApplication()->doRun(
                    $inputForJob,
                    new ConsoleOutput()
                );
            }
            else {
                $output->writeln(sprintf($this->prompt['QUEUES_NOT_FOUND']));
            }
            return null;
        });

        return null;
    }
}