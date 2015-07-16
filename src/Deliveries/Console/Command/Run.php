<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\FormatTrait;

/**
 * Run class. Application Run command
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
    const LOGO = "###################\nSubmission Runner ##\n###################";

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
    const DESCRIPTION = 'Submission runner';

    use FormatTrait;

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            new InputOption('pending', 'p', InputOption::VALUE_NONE, 'Run all the mailing in the status "pending"'),
            new InputOption('abort', 'a', InputOption::VALUE_NONE, 'Re run all the mailing in the status "abort"'),
            new InputOption('sent', 's', InputOption::VALUE_NONE, 'Re run all the mailing in the status "sent"'),
            new InputOption('failed', 'f', InputOption::VALUE_NONE, 'Re run all the mailing in the status "failed"'),
        ];
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
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

        if ($input->getOption('pending')) {
            // start to process pending mails
            $this->getAppServiceManager()->submit('pending');
        }
        else if ($input->getOption('abort')) {
            // start to process abort mails
            $this->getAppServiceManager()->submit('abort');
        }
        else if ($input->getOption('sent')) {
            // start to process sent mails
            $this->getAppServiceManager()->submit('sent');
        }
        else if ($input->getOption('failed')) {
            // start to process failed mails
            $this->getAppServiceManager()->submit('failed');
        }
    }
}