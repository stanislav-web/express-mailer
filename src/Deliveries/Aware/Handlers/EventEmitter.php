<?php
namespace Deliveries\Aware\Handlers;

use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\ConsoleEvents;
use Symfony\Component\EventDispatcher\EventDispatcher;

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

    /**
     * Before execution command event
     */
    public function beforeExecuteCommand() {

        parent::addListener(ConsoleEvents::COMMAND, function (ConsoleCommandEvent $event) {

            // get the command to be executed
            $command = $event->getCommand();
            // write something about the command
            $message = sprintf('Running command `%s`', $command->getName());
            (new Logger())->debug($message);

        });
    }

    /**
     * After execution command event
     */
    public function afterExecuteCommand() {

        parent::addListener(ConsoleEvents::TERMINATE, function (ConsoleTerminateEvent $event) {

            // get the command to be executed
            $command = $event->getCommand();
            // write something about the command
            $message = sprintf('Completion of the command `%s`', $command->getName());
            (new Logger())->debug($message);
        });
    }

    /**
     * Before exception command event
     */
    public function beforeException() {

        parent::addListener(ConsoleEvents::EXCEPTION, function (ConsoleExceptionEvent $event) {

            $output = $event->getOutput();
            $command = $event->getCommand();
            $level = array_flip(Logger::$codeName);
            $level = $level[$event->getException()->getCode()];

            $output->writeln(sprintf('Oops, exception thrown while running command <info>%s</info>', $command->getName()));

            // save an exception to log
            (new Logger())->{$level}($event->getException()->getMessage());
        });
    }
}