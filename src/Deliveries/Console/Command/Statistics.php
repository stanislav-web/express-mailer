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

    use FormatTrait;

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            new InputOption('subscribers', 's', InputOption::VALUE_NONE, 'Show subscribers reports'),
            new InputOption('mailings', 'm', InputOption::VALUE_NONE, 'Show mailings reports'),
            new InputOption('active', 'a', InputOption::VALUE_NONE, 'Show active mail stats'),
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
                'Reports subscribers' => $this->getStorage()->getSubscribersReports()
            ]);
        }
        else if ($input->getOption('mailings')) {
            // throw deliveries stats
            $this->table($output,[
                'Reports mailings' => $this->getStorage()->getMailingsReports()
            ]);
        }
        else if ($input->getOption('active')) {
            // throw active mailing stats
            $this->tableLong($output,[
                'Tail activity mail log' => $this->getStorage()->getActiveMailStatistics()
            ]);
        }
        else {

            // throw subscribers stats
            $this->table($output,[
                'Reports subscribers' => $this->getStorage()->getSubscribersReports()
            ]);
            // throw deliveries stats
            $this->table($output,[
                'Reports mailings' => $this->getStorage()->getMailingsReports()
            ]);
            // throw active mailing stats
            $this->tableLong($output,[
                'Tail activity mail log' => $this->getStorage()->getActiveMailStatistics()
            ]);
        }
    }
}