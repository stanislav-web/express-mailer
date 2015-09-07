<?php
namespace Deliveries\Aware\Helpers;

/**
 * FileSysTrait trait. Filesystem helper
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Helpers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Helpers/FileSysTrait.php
 */
trait FileSysTrait {

    /**
     * Default config filename
     *
     * @var string $configFile
     */
    private $configFile = '/delivery.json';

    /**
     * Broker path
     *
     * @var string $brokersPath
     */
    private $brokersPath = '/src/Deliveries/Adapter/Broker/';

    /**
     * Storages path
     *
     * @var string $storagesPath
     */
    private $storagesPath = '/src/Deliveries/Adapter/Storage/';

    /**
     * Mail path
     *
     * @var string $mailPath
     */
    private $mailPath = '/src/Deliveries/Adapter/Mail/';

    /**
     * Get configuration
     *
     * @return object
     * @throws \RuntimeException
     */
    public function getConfig() {

        $configFile = getcwd().$this->configFile;

        if(file_exists($configFile) === true) {
            return (object)json_decode(file_get_contents($configFile), true);
        }
        throw new \RuntimeException('Configuration file '.$configFile.' does not exist');
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
     * Get reserved adapters
     *
     * @params string $adapter
     * @param $adapter
     *
     * @return array
     */
    public function getReserved($adapter) {

        $dir = '';

        switch($adapter) {
            case 'Storage' :
                $dir = $this->storagesPath;
            break;

            case 'Mail' :
                $dir = $this->mailPath;
            break;

            case 'Broker' :
                $dir = $this->brokersPath;
            break;
        }

        $files = [];
        foreach (new \DirectoryIterator(getcwd().$dir) as $file) {

            if($file->isDot()) continue;
            $files[] = pathinfo($file->getFilename(), PATHINFO_FILENAME);
        }

        return $files;
    }

    /**
     * Add to log file
     *
     * @param string $level
     * @param string $message
     * @param array $context
     */
    public function addToLog($level, $message, array $context = []) {

        $config = $this->getConfig()->Logger;

        // create log file (if not exist)
        $this->createLogFile($config['logFile']);

        // format content
        $content = array_merge([
            'date'      =>  (new \DateTime('now'))->format($config['logDateFormat']),
            'message'   =>  $message,
            'level'     =>  strtoupper($level)
        ], $context);

        // stringify content
        $content = str_ireplace(array_keys($content), array_values($content), $config['LogFormat'])."\n";

        if (file_put_contents($config['logFile'], $content, FILE_APPEND | LOCK_EX) === false) {
            throw new \RuntimeException('Unable to write to the log file: '.$config['logFile']);
        }
    }

    /**
     * Create log file
     *
     * @param string    $file
     * @param int $permissions
     */
    private static function createLogFile($file, $permissions = 0666) {

        if(file_exists($file) === false) {
            if(is_dir(dirname($file)) === false) {
                mkdir(dirname($file), 0777, true);
            }
            file_put_contents($file, '');
            chmod($file, $permissions);
        }
    }
}