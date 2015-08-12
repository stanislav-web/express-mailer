<?php
namespace Deliveries\Aware\Console\Command;

use Deliveries\Aware\Helpers\FileSysTrait;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\InputDefinition;
use Deliveries\Aware\Helpers\TestTrait;
use Deliveries\Service\StorageService;
use Deliveries\Aware\Service\AppServiceManager;

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

    use TestTrait, FileSysTrait;

    /**
     * Command logo
     *
     * @uses latest static bindings
     * @return logo
     */
    protected function logo() {
        echo (new ConsoleOutput())->writeln(
            "\n<info>" . static::LOGO . "</info>"
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
     * Check configuration exist
     *
     * @return bool
     */
    protected function isConfigExist() {
        return file_exists(getcwd().$this->configFile);
    }

    /**
     * Create config file
     *
     * @param string $file
     * @param mixed $content
     * @return int
     */
    protected function createConfigFile($file = null, $content = '') {

        $file = ($file === null) ? getcwd().$this->configFile : $file;
        return file_put_contents($file, json_encode($content));
    }

    /**
     * Create config file
     *
     * @param string $file
     * @param array $content
     * @return int
     */
    protected function addToConfig($file = null, array $content) {

        $file = ($file === null) ? getcwd().$this->configFile : $file;

        $config = (array)self::getConfig();

        foreach($content as $key => $values) {
            $config[$key] = array_merge($config[$key], $values);
        }
        return file_put_contents($file, json_encode($config));
    }

    /**
     * Get ServiceManager
     *
     * @return \Deliveries\Aware\Service\AppServiceManager
     */
    protected function getAppServiceManager() {
        return new AppServiceManager(
            $this->getStorageInstance(self::getConfig()->Storage),
            $this->getMailInstance(self::getConfig()->Mail),
            $this->getQueueInstance(self::getConfig()->Broker)
        );
    }

    /**
     * Get Storage instance
     *
     * @return \Deliveries\Service\StorageService
     */
    protected function getStorage() {
        return new StorageService($this->getStorageInstance(self::getConfig()->Storage));
    }
}