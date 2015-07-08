<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Deliveries\Aware\Console\Command\BaseCommandAware;

/**
 * Run class. Bulk mailing run command
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
    const LOGO = "###################\nRun ###############\n###################\n";
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
    const DESCRIPTION = 'Express mailer start mailing';

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

        return;
    }
}