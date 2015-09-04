<?php
namespace Deliveries\Adapter\Mail;

use Deliveries\Aware\Adapter\Mail\MailProviderInterface;

/**
 * LocalDomain class. Localhost mail connection client
 *
 * @package Deliveries
 * @subpackage Deliveries\Adapter\Mail
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Adapter/Mail/LocalDomain.php
 */
class LocalDomain implements MailProviderInterface {

    /**
     * Default mail host
     */
    const DEFAULT_HOST = 'localhost';

    /**
     * Default mail port
     */
    const DEFAULT_PORT = 25;

    /**
     * Default mail protocol
     */
    const DEFAULT_PROTOCOL = 'tls';

    /**
     * SMTP connection
     *
     * @var \Swift_SmtpTransport $mailer
     */
    protected $mailer;

    /**
     * Get instance connection
     *
     * @return \Swift_SmtpTransport
     */
    public function getInstance() {
        return $this->mailer;
    }

    /**
     * Connect to Mail server
     *
     * @param array $config
     * @return boolean
     */
    public function connect(array $config) {

        // Create the Transport
        $this->mailer = new \Swift_SmtpTransport($config['server'], $config['port'],
            (sizeof($config['secure']) > 0) ? $config['secure'] : null);

        $this->mailer->setUsername($config['username']);
        $this->mailer->setPassword($config['password']);
        $this->mailer->start();
        return  $this->mailer->isStarted();

    }

    /**
     * Get adapter configurations
     *
     * @return array
     */
    public function getConfig() {
        // TODO: Implement getConfig() method.
    }
    /**
     * Create message from config params
     *
     * @param       $subject
     * @param       $message
     * @param array $placeholders email message placeholders in body
     *
     */
    public function createMessage($subject, $message, array $placeholders = []) {
        // TODO: Implement createMessage() method.
    }
    /**
     * Send message to recipient
     *
     * @param array $recipient ['test@email.com' => 'TestName']
     * @param string $subject
     * @param string $message
     * @param array $placeholders mail body variables
     * @return int number of deliveries messages
     */
    public function send(array $recipient, $subject, $message, array $placeholders = [])  {
        // TODO: Implement send() method.
    }
}