<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;

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
    const LOGO = "###################\nStatistics Tools ##\n###################\n";
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
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            'subscribers' => [
                'name'          => 'subscribers',
                'shortcut'      => 's',
                'mode'          => InputOption::VALUE_NONE,
                'description'   => 'Show subscribers statistics'
            ],
            'deliveries' => [
                'name'          => 'deliveries',
                'shortcut'      => 'd',
                'mode'          => InputOption::VALUE_NONE,
                'description'   => 'Show deliveries statistics'
            ]
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

        $this->getStorage();
    }
}