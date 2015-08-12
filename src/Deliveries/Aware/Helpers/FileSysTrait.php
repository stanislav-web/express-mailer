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
    protected $configFile = '/delivery.json';

    /**
     * Broker path
     *
     * @var string $brokersPath
     */
    protected $brokersPath = '/src/Deliveries/Adapter/Broker/';

    /**
     * Storages path
     *
     * @var string $storagesPath
     */
    protected $storagesPath = '/src/Deliveries/Adapter/Storage/';

    /**
     * Mail path
     *
     * @var string $mailPath
     */
    protected $mailPath = '/src/Deliveries/Adapter/Mail/';

    /**
     * Get configuration
     *
     * @return object
     * @throws \RuntimeException
     */
    protected function getConfig() {

        $configFile = getcwd().$this->configFile;

        if(file_exists($configFile) === true) {
            return (object)json_decode(file_get_contents($configFile), true);
        }
        throw new \RuntimeException('Configuration file '.$configFile.' does not exist');
    }

    /**
     * Get reserved adapters
     *
     * @params string $adapter
     * @return array
     */
    public function getReserved($adapter) {

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


}