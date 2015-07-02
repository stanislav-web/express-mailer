<?php
namespace Deliveries\Aware\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Output\ConsoleOutput;

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
     * Command logo
     *
     * @uses latest static bindings
     * @return logo
     */
    protected function logo() {
        echo (new ConsoleOutput())->writeln(
            "\n<fg=cyan;options=bold>" . static::LOGO . "</fg=cyan;options=bold>"
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
}