<?php
namespace Deliveries\Console\Command;

use Deliveries\Aware\Helpers\TestTrait;
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

    use TestTrait;

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

        // checking connection
        if($this->isStorageConnectSuccess($this->getConfig())) {

            $prefix = $this->getPrompt('<info>Please type import '.$this->getConfig()['adapter'].' tables prefix (default `'.self::DEFAULT_PREFIX.'`):</info> ', $input, $output,
                function($answer) {

                    if(empty($answer) === true) {
                        return self::DEFAULT_PREFIX;
                    }
                    return $answer;
            });
        }

        return;
    }
}