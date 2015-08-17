<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\FormatTrait;

/**
 * Statistics class. Application Statistics command
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Statistics.php
 */
class Statistics extends BaseCommandAware {

    /**
     * Command logo
     *
     * @const NAME
     */
    const LOGO = "###################\nStatistics Tools ##\n###################";
    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'statistics';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Statistics tool';

    /**
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'REPORT_SUBSCRIBERS'        =>  "Reports subscribers",
        'REPORT_MAILINGS'           =>  "Reports mailings",
        'REPORT_ACTIVITY'           =>  "Tail activity mail log"
    ];

    use FormatTrait;

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            new InputOption('subscribers', null, InputOption::VALUE_NONE, 'Show subscribers reports'),
            new InputOption('mailings', null, InputOption::VALUE_NONE, 'Show mailings reports'),
            new InputOption('active', null, InputOption::VALUE_NONE, 'Show active mail stats'),
        ];
    }

    /**
     * Get Storage configurations
     *
     * @return array
     */
    public function getConfig() {
        return parent::getConfig()->Storage;
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

        if ($input->getOption('subscribers')) {
            // throw subscribers stats
            $this->table($output,[
                $this->prompt['REPORT_SUBSCRIBERS'] => $this->getAppServiceManager()->getSubscribersReports()
            ]);
        }
        else if ($input->getOption('mailings')) {
            // throw deliveries stats
            $this->table($output,[
                $this->prompt['REPORT_MAILINGS'] => $this->getAppServiceManager()->getMailingsReports()
            ]);
        }
        else if ($input->getOption('active')) {
            // throw active mailing stats
            $this->tableLong($output,[
                $this->prompt['REPORT_ACTIVITY'] => $this->getAppServiceManager()->getActiveMailStatistics()
            ]);
        }
        else {

            // throw subscribers stats
            $this->table($output,[
                $this->prompt['REPORT_SUBSCRIBERS'] => $this->getAppServiceManager()->getSubscribersReports()
            ]);
            // throw deliveries stats
            $this->table($output,[
                $this->prompt['REPORT_MAILINGS'] => $this->getAppServiceManager()->getMailingsReports()
            ]);
            // throw active mailing stats
            $this->tableLong($output,[
                $this->prompt['REPORT_ACTIVITY'] => $this->getAppServiceManager()->getActiveMailStatistics()
            ]);
        }
    }
}