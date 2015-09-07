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
    const LOGO = "###################\nInitialize Tools ##\n###################";

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
     * Prompt string formatter
     *
     * @var array $prompt
     */
    private $prompt = [
        'CONFIG_ALREADY_INIT'       =>  "<comment>Config is already initialized in %s</comment>\n",
        'CONFIG_OVERWRITE'          =>  "<question>Do you want to overwrite it?:</question> [<comment>no/yes</comment>] ",
        'CONFIG_SAVE'               =>  "<question>Do you want to save your configurations?:</question> [<comment>no/yes</comment>] ",
        'CONFIG_CREATED'            =>  "Initializing config file in %s ",
        'QUEUE_ADAPTER_SELECT'      =>  "<info>Please select queue broker for handling:</info> ",
        'QUEUE_HOST_TYPE'           =>  "<info>Please type Queue server IP (default %s):</info> ",
        'QUEUE_PORT_TYPE'           =>  "<info>Please type Queue server port (default %s):</info> ",
        'QUEUE_TIMEOUT_TYPE'        =>  "<info>Please type Queue server connection timeout (default %d):</info> ",
        'QUEUE_IS_PERSISTENT'       =>  "<info>Do you want to use a persistent connection to %s? (default %s):</info> ",
        'QUEUE_LOGIN_TYPE'          =>  "<info>Please type Queue user login:</info> ",
        'QUEUE_PASSWORD_TYPE'       =>  "<info>Please type Queue user password:</info> ",
        'STORAGE_ADAPTER_SELECT'    =>  "<info>Please select storage adapter for handling:</info> ",
        'STORAGE_HOST_TYPE'         =>  "<info>Please type %s host (default %s):</info> ",
        'STORAGE_PORT_TYPE'         =>  "<info>Please type %s port (default %d):</info> ",
        'STORAGE_DB_NAME_TYPE'      =>  "<info>Please type %s database name:</info> ",
        'STORAGE_DB_USER_TYPE'      =>  "<info>Please type %s username:</info> ",
        'STORAGE_DB_PASSWORD_TYPE'  =>  "<info>Please type %s user password:</info> ",
        'MAIL_ADAPTER_SELECT'       =>  "<info>Please select mail adapter for handling:</info> ",
        'MAIL_HOST_TYPE'            =>  "<info>Please type %s smtp host (default %s):</info> ",
        'MAIL_PORT_TYPE'            =>  "<info>Please type SMTP port for %s (default %d):</info> ",
        'MAIL_SOCKET_TYPE'          =>  "<info>Please type SMTP socket (ssl/tls) for %s (default %s):</info> ",
        'MAIL_AUTH_USER_TYPE'       =>  "<info>Please type %s username:</info> ",
        'MAIL_AUTH_PASSWORD_TYPE'   =>  "<info>Please type %s user password:</info> ",
        'MAIL_FROM_NAME_TYPE'       =>  "<info>Please type sender name (from):</info> ",
        'MAIL_FROM_EMAIL_TYPE'      =>  "<info>Please type sender email (from):</info> ",
        'LOGGER_FILE_TYPE'          =>  "<info>Please type the logger file destination path:</info> ",
        'LOGGER_DATE_FORMAT_TYPE'   =>  "<info>Please type Logger date format (default %s):</info> ",
        'LOGGER_RECORD_FORMAT_TYPE' =>  "<info>Please type Logger record format (default %s):</info> ",
    ];

    /**
     * Assign CLI helpers
     */
    use FileSysTrait, ProgressTrait, FormatTrait;

    /**
     * Execute command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     * @return null
     */
    public function execute(InputInterface $input, OutputInterface $output) {

        $this->logo($output);

        // checkout configuration file
        $configFile = $this->configFile($input, $output);

        if($configFile) {

            // create config file with default settings

            $configContent = $this->getConfigContent($input, $output);

            if (file_exists($configFile) === false) {

                // test Queue connect
                if($this->isQueueConnectSuccess($configContent['Broker'])
                    // test Storage connect
                    && $this->isStorageConnectSuccess($configContent['Storage'])
                    // test mail client connect
                    && $this->isMailConnectSuccess($configContent['Mail'])) {

                    // confirm dialog to prepare save input data
                    if($this->isConfigOk($configContent, $input, $output)) {

                        // create config file
                        $isCreate = $this->createConfigFile($configFile, $configContent);

                        if($isCreate > 0) {
                            $this->logger()->info(sprintf($this->prompt['CONFIG_CREATED'], $configFile));
                            $output->writeln("<info>".sprintf($this->prompt['CONFIG_CREATED'], $configFile)."</info>");

                            sleep(1);

                            // run migration command
                            $this->migrationRunner();
                        }
                        else {
                            throw new \RuntimeException('Create config failed!');
                        }
                    }
                }
            }
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
        $inputForJob = new ArrayInput([
            'command' => 'migrations',
        ]);

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
     * @throws \RuntimeException
     * @return array
     */
    private function getConfigContent(InputInterface $input, OutputInterface $output) {

        $config = [];

        // create broker configurations
        $this->createBrokerConfigurations($config,$input, $output);

        // create storage configurations
        $this->createStorageConfigurations($config,$input, $output);

         // create mail configurations
        $this->createMailConfigurations($config,$input, $output);

        // create logger configurations
        $this->createLoggerConfigurations($config,$input, $output);

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

        $configFile = getcwd().$this->configFile;

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
     * @throws \InvalidArgumentException
     * @return int|null|void
     */
    private function rebuildConfig($configFile, InputInterface $input, OutputInterface $output) {

        $helper = $this->getHelper('question');

        $question = new Question([
            sprintf($this->prompt['CONFIG_ALREADY_INIT'],$configFile),
            sprintf($this->prompt['CONFIG_OVERWRITE']),
        ]);

        $question->setValidator(function($typeInput) {
            if (!in_array($typeInput, ['no', 'yes'])) {
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
        return null;

    }

    /**
     * Draw results for prepare saving
     *
     * @param array $configContent
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \InvalidArgumentException
     * @return boolean
     */
    private function isConfigOk(array $configContent, $input, $output) {

        $this->table($output, $configContent);

        $helper = $this->getHelper('question');

        $question = new Question(sprintf($this->prompt['CONFIG_SAVE']));

        $question->setValidator(function($typeInput) {
            if (!in_array($typeInput, ['no', 'yes'])) {
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

    /**
     * Create queue Broker configurations
     *
     * @param array $config
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    private function createBrokerConfigurations(&$config, $input, $output) {

        $config['Broker']['adapter']    = $this->getPrompt(sprintf($this->prompt['QUEUE_ADAPTER_SELECT']), $input, $output,
            function($answer) {

                $queues = $this->getReserved('Broker');
                if(array_search(strtolower($answer), array_map('strtolower', $queues)) === false) {
                    throw new \RuntimeException(
                        'You must select one from existing brokers ('.implode(',', $queues).')'
                    );
                }
                return $answer;
            }
        );

        $broker = 'Deliveries\Adapter\Broker\\'.$config['Broker']['adapter'];

        if($config['Broker']['adapter'] != 'Native') {

            /** @noinspection PhpUndefinedFieldInspection */
            $config['Broker']['host']       = $this->getPrompt(sprintf($this->prompt['QUEUE_HOST_TYPE'], $broker::DEFAULT_HOST), $input, $output,
                function($answer) use ($config, $broker) {

                    if(empty($answer) === true) {
                        /** @noinspection PhpUndefinedFieldInspection */
                        return $broker::DEFAULT_HOST;
                    }

                    if(filter_var($answer, FILTER_VALIDATE_IP) === false && $answer != 'localhost') {
                        throw new \RuntimeException(
                            'Please, type a valid '.$config['Broker']['adapter'].' server IP address!'
                        );
                    }
                    return $answer;
                }
            );
            /** @noinspection PhpUndefinedFieldInspection */
            $config['Broker']['port']       = $this->getPrompt(sprintf($this->prompt['QUEUE_PORT_TYPE'], $broker::DEFAULT_PORT), $input, $output,
                function($answer) use ($config, $broker) {

                    if(empty($answer) === true) {
                        /** @noinspection PhpUndefinedFieldInspection */
                        return $broker::DEFAULT_PORT;
                    }

                    if(preg_match('/^(\d){2,4}$/', $answer) == false) {
                        throw new \RuntimeException(
                            'Please, type a valid server port!'
                        );
                    }
                    return (int)$answer;
                }
            );
            /** @noinspection PhpUndefinedFieldInspection */
            $config['Broker']['timeout']    = $this->getPrompt(sprintf($this->prompt['QUEUE_TIMEOUT_TYPE'], $broker::DEFAULT_TIMEOUT), $input, $output,
                function($answer) use ($config, $broker) {

                    if(empty($answer) === true) {
                        /** @noinspection PhpUndefinedFieldInspection */
                        return $broker::DEFAULT_TIMEOUT;
                    }

                    if(is_numeric($answer) == false) {
                        throw new \RuntimeException(
                            'Please, type a valid timeout value!'
                        );
                    }
                    return (int)$answer;
                }
            );
            /** @noinspection PhpUndefinedFieldInspection */
            $config['Broker']['persistent'] = $this->getPrompt(sprintf($this->prompt['QUEUE_IS_PERSISTENT'], $config['Broker']['adapter'], $broker::DEFAULT_IS_PERSISTENT), $input, $output,
                function($answer) use ($config, $broker) {

                    if(empty($answer) === true) {
                        /** @noinspection PhpUndefinedFieldInspection */
                        return $broker::DEFAULT_IS_PERSISTENT;
                    }

                    if(in_array($answer, ['true', 'false']) == false) {
                        throw new \RuntimeException(
                            'Please, type `true` or `false`!'
                        );
                    }
                    return (int)$answer;
                }
            );
            $config['Broker']['login']      = $this->getPrompt(sprintf($this->prompt['QUEUE_LOGIN_TYPE']), $input, $output, null, true);
            $config['Broker']['password']   = $this->getPrompt(sprintf($this->prompt['QUEUE_PASSWORD_TYPE']), $input, $output, null, true);

        }

    }

    /**
     * Create Storage configurations
     *
     * @param array $config
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    private function createStorageConfigurations(&$config, $input, $output) {

        $config['Storage']['adapter']   = $this->getPrompt(sprintf($this->prompt['STORAGE_ADAPTER_SELECT']), $input, $output,
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

        /** @noinspection PhpUndefinedFieldInspection */
        $config['Storage']['host']      = $this->getPrompt(sprintf($this->prompt['STORAGE_HOST_TYPE'], $config['Storage']['adapter'], $storage::DEFAULT_HOST), $input, $output,
            function($answer) use ($config, $storage) {

                if(empty($answer) === true) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    return $storage::DEFAULT_HOST;
                }
                if(filter_var(gethostbyname($answer), FILTER_VALIDATE_IP) === false && $answer != 'localhost') {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Storage']['adapter'].' host!'
                    );
                }
                return $answer;
            });
        /** @noinspection PhpUndefinedFieldInspection */
        $config['Storage']['port']      = $this->getPrompt(sprintf($this->prompt['STORAGE_PORT_TYPE'], $config['Storage']['adapter'], $storage::DEFAULT_PORT), $input, $output,
            function($answer) use ($config, $storage) {

                if(empty($answer) === true) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    return $storage::DEFAULT_PORT;
                }

                if(preg_match('/^(\d){2,4}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Storage']['adapter'].' port!'
                    );
                }
                return (int)$answer;
            });
        $config['Storage']['db']        = $this->getPrompt(sprintf($this->prompt['STORAGE_DB_NAME_TYPE'], $config['Storage']['adapter']), $input, $output, null);
        $config['Storage']['username']  = $this->getPrompt(sprintf($this->prompt['STORAGE_DB_USER_TYPE'], $config['Storage']['adapter']), $input, $output, null);
        $config['Storage']['password']  = $this->getPrompt(sprintf($this->prompt['STORAGE_DB_PASSWORD_TYPE'], $config['Storage']['adapter']), $input, $output, null, true);

    }

    /**
     * Create Mail configurations
     *
     * @param array $config
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @throws \RuntimeException
     */
    private function createMailConfigurations(&$config, $input, $output) {

        $config['Mail']['adapter'] = $this->getPrompt(sprintf($this->prompt['MAIL_ADAPTER_SELECT']), $input, $output,
            function($answer) {

                $mails = $this->getReserved('Mail');
                if(array_search(strtolower($answer), array_map('strtolower', $mails)) === false) {
                    throw new \RuntimeException(
                        'You must select one from existing mail adapters ('.implode(',', $mails).')'
                    );
                }
                return $answer;
            }
        );

        $mail = 'Deliveries\Adapter\Mail\\'.$config['Mail']['adapter'];

        /** @noinspection PhpUndefinedFieldInspection */
        $config['Mail']['server']       = $this->getPrompt(sprintf($this->prompt['MAIL_HOST_TYPE'], $config['Mail']['adapter'], $mail::DEFAULT_HOST), $input, $output,
            function($answer) use ($config, $mail) {

                if(empty($answer) === true) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    return $mail::DEFAULT_HOST;
                }

                if(filter_var(gethostbyname($answer), FILTER_VALIDATE_IP) === false && $answer != 'localhost') {
                    throw new \RuntimeException(
                        'Please, type a valid '.$config['Mail']['adapter'].' host!'
                    );
                }
                return $answer;
            }
        );
        /** @noinspection PhpUndefinedFieldInspection */
        $config['Mail']['port']         = $this->getPrompt(sprintf($this->prompt['MAIL_PORT_TYPE'], $config['Mail']['server'], $mail::DEFAULT_PORT), $input, $output,

            function($answer) use ($mail) {

                if(empty($answer) === true) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    return $mail::DEFAULT_PORT;
                }

                if(preg_match('/^(\d){2,4}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid SMTP port!'
                    );
                }
                return (int)$answer;
            }
        );
        /** @noinspection PhpUndefinedFieldInspection */
        $config['Mail']['socket']       = $this->getPrompt(sprintf($this->prompt['MAIL_SOCKET_TYPE'], $config['Mail']['adapter'], $mail::DEFAULT_SOCKET), $input, $output,

            function($answer) use ($mail) {

                if(empty($answer) === true) {
                    /** @noinspection PhpUndefinedFieldInspection */
                    return $mail::DEFAULT_PROTOCOL;
                }

                if(preg_match('/^(\w){3}$/', $answer) == false) {
                    throw new \RuntimeException(
                        'Please, type a valid SMTP connection!'
                    );
                }
                return (int)$answer;
            }
        );
        $config['Mail']['username']     = $this->getPrompt(sprintf($this->prompt['MAIL_AUTH_USER_TYPE'], $config['Mail']['server']), $input, $output, null);
        $config['Mail']['password']     = $this->getPrompt(sprintf($this->prompt['MAIL_AUTH_PASSWORD_TYPE'], $config['Mail']['server']), $input, $output, null, true);
        $config['Mail']['fromName']     = $this->getPrompt(sprintf($this->prompt['MAIL_FROM_NAME_TYPE']), $input, $output);
        $config['Mail']['fromEmail']    = $this->getPrompt(sprintf($this->prompt['MAIL_FROM_EMAIL_TYPE']), $input, $output,

            function($answer)  {

                if(filter_var($answer, FILTER_VALIDATE_EMAIL) === false) {
                    throw new \RuntimeException(
                        'Please, type a valid sender email'
                    );
                }
                return $answer;
            }
        );

    }

    /**
     * Create Log configurations
     *
     * @param array $config
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    private function createLoggerConfigurations(&$config, $input, $output) {

        $config['Logger']['logFile'] = $this->getPrompt(sprintf($this->prompt['LOGGER_FILE_TYPE']), $input, $output, null);
        $config['Logger']['logDateFormat'] = $this->getPrompt(sprintf($this->prompt['LOGGER_DATE_FORMAT_TYPE'], $this->logger()->getDefaultDateFormat()), $input, $output,

            function($answer) {

                if(empty($answer) === true) {
                    return $this->logger()->getDefaultDateFormat();
                }
                return $answer;
            }
        );
        $config['Logger']['LogFormat'] = $this->getPrompt(sprintf($this->prompt['LOGGER_RECORD_FORMAT_TYPE'], $this->logger()->getDefaultLogFormat()), $input, $output,

            function($answer) {

                if(empty($answer) === true) {
                    return $this->logger()->getDefaultLogFormat();
                }
                return $answer;
            }
        );

    }
}