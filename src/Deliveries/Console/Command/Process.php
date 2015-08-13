<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;

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
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions()
    {

        return [
            new InputOption('queues', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Queues'),
            new InputOption('subscribers', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Subscribers'),
            new InputOption('qc', null, InputOption::VALUE_REQUIRED, 'Queues counter'),
            new InputOption('qs', null, InputOption::VALUE_REQUIRED, 'Subscribers count')
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
        // checking config
        if($this->isConfigExist() === false) {
            throw new \RuntimeException(
                'Configuration file does not exist! Run `xmail init`'
            );
        }

        // resolve mailing process
        //var_dump($input->getOptions());
        //exit();

    }
}