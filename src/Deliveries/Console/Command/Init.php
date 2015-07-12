<?php
namespace Deliveries\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Deliveries\Aware\Console\Command\BaseCommandAware;
use Deliveries\Aware\Helpers\FileSysTrait;
use Deliveries\Aware\Helpers\FormatTrait;
use Deliveries\Aware\Helpers\ProgressTrait;

/**
 * Init class. Application Init command
 *
 * @package Deliveries
 * @subpackage Deliveries\Console\Command
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Command/Init.php
 */
class Init extends BaseCommandAware {

    /**
     * Command logo
     *
     * @const NAME
     */
    const LOGO = "###################\nInitialize Tools ##\n###################\n";

    /**
     * Command name
     *
     * @const NAME
     */
    const NAME = 'init';

    /**
     * Command description
     *
     * @const DESCRIPTION
     */
    const DESCRIPTION = 'Create default package configurations';

    /**
     * Assign CLI helpers
     */
    use FileSysTrait, ProgressTrait, FormatTrait;

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    public function execute(InputInterface $input, OutputInterface $output) {

        $this->logo();

        // checkout configuration file
        $configFile = $this->configFile($input, $output);

        if($configFile) {

            $configContent = $this->getConfigContent($input, $output);

            // create config file with default settings
            $progress = $this->getProgress($output);

            sleep(1);

            $i = 0;
            while ($i++ < 100) {

                if (file_exists($configFile) === false) {
                    $progress->advance();
                }
                else {
                    $progress->finish();
                    break;
                }

                // Test Queue connect
                if($this->isQueueConnectSuccess($configContent['Broker'])
                    // Test Storage connect
                    && $this->isStorageConnectSuccess($configContent['Storage'])
                        // Test mail client connect
                        && $this->isMailConnectSuccess($configContent['Mail'])) {

                        // Confirm dialog to prepare save input data
                        if($this->isConfigOk($configContent, $input, $output)) {

                            // Create config file
                            $this->createConfigFile($configFile, $configContent);

                            // Run migration command
                            $this->migrationRunner();
                        }
                }
            }

            $output->writeln(
                "\n<fg=white;bg=magenta>Initializing config file in " . $configFile . "</fg=white;bg=magenta>"
            );
        }
        return;
    }

    /**
     * Run migration command
     *
     * @return mixed
     */
    public function migrationRunner() {

        // setup all needed parameters
        $inputForJob = new ArrayInput(array(
            'command' => 'migrations',
        ));

        return  $this->getApplication()->doRun(
            $inputForJob,
            new ConsoleOutput()
        );
    }

    /**
     * Get configuration content
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function getConfigContent(InputInterface $input, OutputInterface $output) {

        $config = [];

        $config['Broker']['adapter'] = $this->getPrompt('<info>Please select queue broker for handling:</info> ', $input, $output,
            function($answer) {

                $queues = $this->getReserved('Broker');


                if(array_search(strtolower($answer), array_map('strtolower', $queues)) === false) {
                    throw new \RuntimeException(
                        'You must select one from existing brokers ('.implode(',', $queues).')'
                    );
                }
                return $answer;
        });

        $broker = 'Deliveries\Adapter\Broker\\'.$config['Broker']['adapter'];
        $config['Broker']['host'] = $this->getPrompt('<info>Please type Queue server IP (default '.$broker::DEFAULT_HOST.'):</info> ', $input, $output,
            function($answer) use ($config, $broker) {

                if(empty($answer) === true) {
                    return $broker::DEFAULT_HOST;
                }

                if(filter_var($answer, FILTER_VALIDATE_IP) === false && $answer != 'localhost') {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Broker']['adapter'].' server IP address!'
                    );
                }
            return $answer;
        });

        $config['Broker']['port'] = $this->getPrompt('<info>Please type AMQP server port (default '.$broker::DEFAULT_PORT.'):</info> ', $input, $output,
            function($answer) use ($config, $broker) {

                if(empty($answer) === true) {
                    return $broker::DEFAULT_PORT;
                }

                if(preg_match('/^(\d){2,4}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid server port!'
                    );
                }
                return (int)$answer;
         });

        $config['Broker']['timeout'] = $this->getPrompt('<info>Please type AMQP server connection timeout (default '.$broker::DEFAULT_TIMEOUT.'):</info> ', $input, $output,
            function($answer) use ($config, $broker) {

                if(empty($answer) === true) {
                    return $broker::DEFAULT_TIMEOUT;
                }

                if(is_numeric($answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid timeout value!'
                    );
                }
                return (int)$answer;
        });

        $config['Broker']['persistent'] = $this->getPrompt('<info>Do you want to use a persistent connection to '.$config['Broker']['adapter'].'? (default "'.$broker::DEFAULT_IS_PERSISTENT.'"):</info> ', $input, $output,
            function($answer) use ($config, $broker) {

                if(empty($answer) === true) {
                    return $broker::DEFAULT_IS_PERSISTENT;
                }

                if(in_array($answer, ['true', 'false']) == false) {
                    throw new \RuntimeException(
                        'Please, type `true` or `false`!'
                    );
                }
                return (int)$answer;
        });

        $config['Broker']['login'] = $this->getPrompt('<info>Please type Queue user login:</info> ', $input, $output, null, true);
        $config['Broker']['password'] = $this->getPrompt('<info>Please type Queue user password:</info> ', $input, $output, null, true);

        $config['Storage']['adapter'] = $this->getPrompt('<info>Please select storage adapter for handling:</info> ', $input, $output,
            function($answer) {
                $storages = $this->getReserved('Storage');
                if(array_search(strtolower($answer), array_map('strtolower', $storages)) === false) {
                    throw new \RuntimeException(
                        'You must select one from existing storages ('.implode(',', $storages).')'
                    );
                }
                return $answer;
        });

        $storage = 'Deliveries\Adapter\Storage\\'.$config['Storage']['adapter'];

        $config['Storage']['host'] = $this->getPrompt('<info>Please type '.$config['Storage']['adapter'].' host (default '.$storage::DEFAULT_HOST.'):</info> ', $input, $output,
            function($answer) use ($config, $storage) {

                if(empty($answer) === true) {
                    return $storage::DEFAULT_HOST;
                }
                if(filter_var(gethostbyname($answer), FILTER_VALIDATE_IP) === false && $answer != 'localhost') {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Storage']['adapter'].' host!'
                    );
                }
                return $answer;
        });

        $config['Storage']['port'] = $this->getPrompt('<info>Please type '.$config['Storage']['adapter'].' port (default '.$storage::DEFAULT_PORT.'):</info> ', $input, $output,
            function($answer) use ($config, $storage) {

                if(empty($answer) === true) {
                    return $storage::DEFAULT_PORT;
                }

                if(preg_match('/^(\d){2,4}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Storage']['adapter'].' port!'
                    );
                }
                return (int)$answer;
        });
        $config['Storage']['db'] = $this->getPrompt('<info>Please type '.$config['Storage']['adapter'].' database name:</info> ', $input, $output, null);
        $config['Storage']['username'] = $this->getPrompt('<info>Please type '.$config['Storage']['adapter'].' username:</info> ', $input, $output, null);
        $config['Storage']['password'] = $this->getPrompt('<info>Please type '.$config['Storage']['adapter'].' user password:</info> ', $input, $output, null, true);

        $config['Mail']['adapter'] = $this->getPrompt('<info>Please select mail adapter for handling:</info> ', $input, $output,
            function($answer) {

                $mails = $this->getReserved('Mail');
                if(array_search(strtolower($answer), array_map('strtolower', $mails)) === false) {
                    throw new \RuntimeException(
                        'You must select one from existing mail adapters ('.implode(',', $mails).')'
                    );
                }
                return $answer;
        });

        $mail = 'Deliveries\Adapter\Mail\\'.$config['Mail']['adapter'];

        $config['Mail']['server'] = $this->getPrompt('<info>Please type '.$config['Mail']['adapter'].' smtp host (default '.$mail::DEFAULT_HOST.'):</info> ', $input, $output,
            function($answer) use ($config, $mail) {

                if(empty($answer) === true) {
                    return $mail::DEFAULT_HOST;
                }

                if(filter_var(gethostbyname($answer), FILTER_VALIDATE_IP) === false && $answer != 'localhost') {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Mail']['adapter'].' host!'
                    );
                }
                return $answer;
        });

        $config['Mail']['port'] = $this->getPrompt('<info>Please type SMTP port for '.$config['Mail']['server'].' (default '.$mail::DEFAULT_PORT.'):</info> ', $input, $output,

            function($answer) use ($config, $mail) {

                if(empty($answer) === true) {
                    return $mail::DEFAULT_PORT;
                }

                if(preg_match('/^(\d){2,4}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid SMTP port!'
                    );
                }
                return (int)$answer;
        });

        $config['Mail']['secure'] = $this->getPrompt('<info>Please type SMTP Secure (ssl/tls) for '.$config['Mail']['adapter'].' (default '.$mail::DEFAULT_PROTOCOL.'):</info> ', $input, $output,

            function($answer) use ($config, $mail) {

                if(empty($answer) === true) {
                    return $mail::DEFAULT_PROTOCOL;
                }

                if(preg_match('/^(\w){3}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid SMTP connection!'
                    );
                }
                return (int)$answer;
            });

        $config['Mail']['username'] = $this->getPrompt('<info>Please type '.$config['Mail']['server'].' username:</info> ', $input, $output, null);
        $config['Mail']['password'] = $this->getPrompt('<info>Please type '.$config['Mail']['server'].' user password:</info> ', $input, $output, null, true);

        return $config;
    }

    /**
     * Get config file
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return string|void
     */
    private function configFile(InputInterface $input, OutputInterface $output) {

        $configFile = getcwd().self::CONFIG_FILENAME;

        if (file_exists($configFile) === true) {
            // rebuild config file
            return $this->rebuildConfig($configFile, $input, $output);
        }
        return $configFile;
    }

    /**
     * Unlink & make a new config
     *
     * @param string $configFile
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param boolean $skip
     * @return int|null|void
     */
    private function rebuildConfig($configFile, $input, $output) {

        $helper = $this->getHelper('question');

        $question = new Question(array(
            "<comment>Config is already initialized $configFile</comment>\n",
            "<question>Do you want to overwrite it?:</question> [<comment>no/yes</comment>] ",
        ));

        $question->setValidator(function($typeInput) {
            if (!in_array($typeInput, array('no', 'yes'))) {
                throw new \InvalidArgumentException('Invalid input type. Please [yes] or [no]');
            }
            return $typeInput;
        });

        $isRewrite = $helper->ask($input, $output, $question);

        if($isRewrite == 'yes') {

            if(file_exists($configFile) === true) {
                unlink($configFile);
            }
                return $this->execute($input, $output);
        }
        return;

    }

    /**
     * Draw results for prepare saving
     *
     * @param $configContent
     * @param $input
     * @param $output
     * @return boolean
     */
    private function isConfigOk($configContent, $input, $output) {

        $this->table($output, $configContent);

        $helper = $this->getHelper('question');

        $question = new Question("<question>Do you want to save your configurations?:</question> [<comment>no/yes</comment>] ");

        $question->setValidator(function($typeInput) {
            if (!in_array($typeInput, array('no', 'yes'))) {
                throw new \InvalidArgumentException('Invalid input type. Please [yes] or [no]');
            }
            return $typeInput;
        });

        $isRewrite = $helper->ask($input, $output, $question);

        if($isRewrite == 'no') {
            return $this->execute($input, $output);
        }

        return true;
    }
}