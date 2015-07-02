<?php
namespace Deliveries\Aware\Adapter\Mail;

/**
 * MailProviderInterface interface. Mail provider interface
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Adapter\Mail
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Adapter/Mail/MailProviderInterface.php
 */
interface MailProviderInterface {

    /**
     * Get instance connection
     */
    public function getInstance();

    /**
     * Connect to Mail server
     *
     * @param array $config
     */
    public function connect(array $config);

}