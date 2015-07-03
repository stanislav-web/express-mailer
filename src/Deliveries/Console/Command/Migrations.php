<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;

/**
 * Migrations class. Application Migrations command
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Migrations.php
 */
class Migrations extends BaseCommandAware {

    /**
     * Command logo
     *
     * @const NAME
     */
    const LOGO = "###################\nMigration Tools ###\n###################\n";
    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'migrations';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Storage migrations tool';

    /**
     * Default storage prefix
     *
     * @const DEFAULT_PREFIX
     */
    const DEFAULT_PREFIX = '';

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logo();
        if($this->isConfigExist() === false) {
            throw new \RuntimeException(
                'Configuration file does not exist! Run `xmail init`'
            );
        }

        return;
    }
}