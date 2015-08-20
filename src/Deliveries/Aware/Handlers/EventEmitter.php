<?php
namespace Deliveries\Aware\Handlers;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Deliveries\Service\AppLoggerService;

/**
 * EventEmitter class. Console event emitter
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Handlers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Handlers/EventEmitter.php
 */
class EventEmitter extends EventDispatcher {

    private $executionTime = 0;

    /**
     * Before execution command event
     */
    public function beforeExecuteCommand() {

        parent::addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {

            // enable time elapse
            $start	=	explode(" ", microtime());
            $this->executionTime = $start[1] + $start[0];

            // get the command to be executed
            $command = $event->getCommand();
            // write something about the command
            $message = sprintf('Running command `%s` by %s', $command->getName(), get_current_user());
            (new AppLoggerService())->debug($message);
        });
    }

    /**
     * After execution command event
     */
    public function afterExecuteCommand() {

        parent::addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {

            // fixed end command execution time
            $time = explode(" ", microtime());

            // get the command to be executed
            $command = $event->getCommand();
            // write something about the command
            $message = sprintf('Completion of the command `%s`. Time elapsed %f sec.', $command->getName(), (($time[1] + $time[0]) - $this->executionTime));
            (new AppLoggerService())->debug($message);
        });
    }

    /**
     * Before exception command event
     */
    public function beforeException() {

        parent::addListener(ConsoleEvents::EXCEPTION, function (ConsoleExceptionEvent $event) {

            $output = $event->getOutput();
            $command = $event->getCommand();
            $code = $event->getException()->getCode();
            $level = array_flip(AppLoggerService::$codeName);

            $level = (isset($level[$code])) ? $level[$code] : 'emergency';

            $output->writeln(sprintf('Oops, exception thrown while running command <info>%s</info>', $command->getName()));

            // save an exception to log
            (new AppLoggerService())->{$level}($event->getException()->getMessage());
        });
    }
}