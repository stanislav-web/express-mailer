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
     * Get adapter configurations
     *
     * @return array
     */
    public function getConfig();

    /**
     * Connect to Mail server
     *
     * @param array $config
     */
    public function connect(array $config);

    /**
     * Get instance of mail provider
     *
     * @return \Deliveries\Aware\Adapter\Mail\MailProviderInterface
     */
    public function getInstance();

    /**
     * Create message from config params
     *
     * @param       $subject
     * @param       $message
     * @param array $placeholders email message placeholders in body
     *
     */
    public function createMessage($subject, $message, array $placeholders = []);

    /**
     * Send message to recipient
     *
     * @param array $recipient ['test@email.com' => 'TestName']
     * @param string $subject
     * @param string $message
     * @param array $placeholders mail body variables
     * @return int number of deliveries messages
     */
    public function send(array $recipient, $subject, $message, array $placeholders = []) ;

}