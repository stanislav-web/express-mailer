<?php
namespace Deliveries\Console\Command;

use Deliveries\Exceptions\AppException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;

/**
 * Thread class. Application threads command executor
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Thread.php
 */
class Thread extends BaseCommandAware {

    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'thread';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Background threads';

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions()
    {
        return [
            new InputOption('command', null, InputOption::VALUE_REQUIRED, 'Running command'),
            new InputOption('arguments', null, InputOption::VALUE_REQUIRED, 'Passed arguments'),
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
        // get requested data
        $request = $input->getOptions();

        if(empty($request['command']) === false && empty($request['arguments']) === false) {
            var_dump($request);
        }
        else {
            throw new AppException(sprintf('Wrong arguments passed into `%s`', $this->getName()), 'warning');
        }
    }
}