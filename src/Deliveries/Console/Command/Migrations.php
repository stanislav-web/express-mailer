<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;

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
class Migrations extends Command {

    /**
     * Command description
     *
     * @const CONFIG_FILENAME
     */
    const DESCRIPTION = 'Storage migrations tool';

    /**
     * Configure bootstrap by default (assign -path to create config file)
     */
    protected function configure() {

        $this->setName('migrations')
            ->setDescription($this->getDescription());
    }

    /**
     * Command description
     *
     * @return string
     */
    public function getDescription() {
        return self::DESCRIPTION;
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        exit('Migration tool');
        return;
    }
}