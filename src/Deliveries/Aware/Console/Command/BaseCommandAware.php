<?php
namespace Deliveries\Aware\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputDefinition;
use Deliveries\Aware\Helpers\TestTrait;
use Deliveries\Aware\Helpers\FileSysTrait;
use Deliveries\Service\AppServiceManager;
use Deliveries\Service\AppLoggerService;

/**
 * BaseCommandAware class. BaseCommand aware interface
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Init.php
 */
class BaseCommandAware extends Command {

    /**
     * Logger interface
     *
     * @var \Deliveries\Service\AppLoggerService $logger
     */
    private $logger = null;

    use TestTrait, FileSysTrait;

    /**
     * Command logo
     *
     * @uses latest static bindings
     * @return logo
     */
    protected function logo() {
        echo (new ConsoleOutput())->writeln(
            "<info>" . static::LOGO . "</info>"
        );
    }

    /**
     * Configure bootstrap by default (assign -path to create config file)
     *
     * @uses latest static bindings
     * @param string $command
     */
    protected function configure() {

        $this->setName(static::NAME)
            ->setDescription($this->getDescription());

        if(method_exists($this,'getOptions')) {
            $this->setDefinition(
                    new InputDefinition($this->getOptions())
            );
        }
    }

    /**
     * Command description
     *
     * @uses latest static bindings
     * @return string
     */
    public function getDescription() {
        return static::DESCRIPTION;
    }

    /**
     * Get user command prompt
     *
     * @param string $string
     * @param string  $string
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param callable $validator
     * @param boolean $skipEmpty
     * @throws \RuntimeException
     * @return string
     */
    protected function getPrompt($string, InputInterface $input, OutputInterface $output,
                               $validator = null, $skipEmpty = false) {

        $helper = $this->getHelper('question');

        $question = new Question($string);

        if($skipEmpty === false) {
            $question->setValidator(function ($answer) {

                if(empty($answer) === true) {
                    throw new \RuntimeException(
                        'Please, fill out the entry!'
                    );
                }
                return $answer;
            });
        }

        if(is_null($validator) === false) {
            $question->setValidator($validator);
        }
        return $helper->ask($input, $output, $question);
    }

    /**
     * Get ServiceManager
     *
     * @return \Deliveries\Service\AppServiceManager
     */
    protected function getAppServiceManager() {

        return new AppServiceManager(
            $this->getStorageInstance(self::getConfig()->Storage),
            $this->getMailInstance(self::getConfig()->Mail),
            $this->getQueueInstance(self::getConfig()->Broker)
        );

    }

    /**
     * Get logger
     *
     * @return \Deliveries\Service\LoggerService
     */
    protected function logger() {

        if(is_null($this->logger) == true) {
            $this->logger = new AppLoggerService();
        }
        return $this->logger;
    }

    /**
     * Output with logging
     *
     * @param OutputInterface $output
     * @param string          $message
     * @param string          $format
     */
    protected function logOutput(OutputInterface $output, $message, $format = '') {

        $this->logger()->info($message);

        if(empty($format) === true) {
            return $output->writeln($message);
        }
        else {
            return $output->writeln(sprintf($format, $message));
        }
    }
}