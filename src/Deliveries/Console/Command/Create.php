<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\FormatTrait;

/**
 * Create class. Application Create submission command
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Create.php
 */
class Create extends BaseCommandAware {

    /**
     * Command logo
     *
     * @const NAME
     */
    const LOGO = "###################\nSubmission create #\n###################";

    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'create';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Submission create';

    /**
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'QUEUE_CREATE_SUCCESS'      =>  "Queue has been created. QID: %d",
    ];

    use FormatTrait;

    /**
     * Get command additional options
     *
     * @return array
     */
    public static function getOptions() {

        return [
            new InputOption('pending', null, InputOption::VALUE_NONE, 'Create all the mailing in the status "pending"'),
            new InputOption('abort', null, InputOption::VALUE_NONE, 'Re create all the mailing in the status "abort"'),
            new InputOption('sent', null, InputOption::VALUE_NONE, 'Re create all the mailing in the status "sent"'),
            new InputOption('failed', null, InputOption::VALUE_NONE, 'Re create all the mailing in the status "failed"'),
            new InputOption('date', null, InputOption::VALUE_OPTIONAL, 'Queue start date', (new \DateTime())->format('Y-m-d H:i:s')),
            new InputOption('priority', null, InputOption::VALUE_NONE, 'Queue priority'),
        ];
    }

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logo($output);

        // checking config
        if($this->isConfigExist() === false) {
            throw new \RuntimeException(
                'Configuration file does not exist! Run `xmail init`'
            );
        }

        // create queue
        $pid = $this->getAppServiceManager()->createQueue($input->getOptions());

        $this->logger()->info(sprintf($this->prompt['QUEUE_CREATE_SUCCESS'], $pid));

        $output->writeln(
            "<info>".sprintf($this->prompt['QUEUE_CREATE_SUCCESS'], $pid)."</info>"
        );
    }
}