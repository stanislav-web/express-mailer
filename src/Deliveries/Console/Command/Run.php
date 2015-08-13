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
     * @const NOT_FOUND command not found
     */
    const NOT_FOUND = 'No active queues fond';

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            new InputOption('date', 'd', InputOption::VALUE_OPTIONAL, 'Queue start date', (new \DateTime())->format('Y-m-d')),
            new InputOption('limit', 'l', InputOption::VALUE_OPTIONAL, 'Limit queue lists'),
            new InputOption('subscribers', 's', InputOption::VALUE_OPTIONAL, 'Subscribers state', 'active'),
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

        // Get active queues
        $this->getAppServiceManager()->getQueues($input->getOptions(), function($data) use ($output) {

            if(($queueCount = count($data['queues'])) > 0) {

                // active queues founded
                $subscribersCount = count($data['subscribers']);
                $mailsCount = ($subscribersCount * $queueCount);

                $output->writeln(
                    "<comment>Prepare process mailings: ".$queueCount."</comment>\n".
                    "<comment>Prepare subscribers : ".$subscribersCount."</comment>\n".
                    "<comment>Prepare letters to send : ".$mailsCount."</comment>"
                );

                // migrate to process mailing

                $inputForJob = new ArrayInput([
                    'command'           => 'process',
                    '--queues'          => $data['queues'],
                    '--subscribers'     => $data['subscribers'],
                    '--qc'              => $queueCount,
                    '--qs'              => $subscribersCount,
                ]);
                return  $this->getApplication()->doRun(
                    $inputForJob,
                    new ConsoleOutput()
                );
            }
            else {
                $output->writeln('<comment>'.self::NOT_FOUND.'</comment>');
            }
        });
    }
}