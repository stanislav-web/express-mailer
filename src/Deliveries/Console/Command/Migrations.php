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
     * @const CONFIG_FILENAME
     */
    const DESCRIPTION = 'Storage migrations tool';

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logo();
        exit('Migration tool');
        return;
    }
}