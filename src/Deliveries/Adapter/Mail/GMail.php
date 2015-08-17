<?php
namespace Deliveries\Adapter\Mail;

use Deliveries\Aware\Adapter\Mail\MailProviderInterface;
use Deliveries\Exceptions\MailException;
use Deliveries\Exceptions\MailSMTPExceptions;

/**
 * GMail class. Google Mail connection client
 *
 * @package Deliveries
 * @subpackage Deliveries\Adapter\Mail
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Adapter/Mail/GMail.php
 */
class GMail implements MailProviderInterface {

    /**
     * Default mail host
     */
    const DEFAULT_HOST = 'smtp.gmail.com';

    /**
     * Default mail port
     */
    const DEFAULT_PORT = 587;

    /**
     * Default mail socket
     */
    const DEFAULT_SOCKET = 'tls';

    /**
     * Adapter config
     *
     * @var array $config
     */
    private $config = [];

    /**
     * SMTP connection
     *
     * @var \Swift_SmtpTransport $connect
     */
    private $connect;

    /**
     * Mailer
     *
     * @var \Swift_Mailer $mailer
     */
    protected $mailer;

    /**
     * Message builder
     *
     * @var \Swift_Message $message
     */
    protected $message;

    /**
     * Get instance of adapter
     *
     * @return \Deliveries\Aware\Adapter\Mail\MailProviderInterface
     */
    public function getInstance() {

        if(!$this->mailer) {
            // create the Mailer using your created Transport
            $this->mailer = \Swift_Mailer::newInstance($this->connect);
        }

        return $this;
    }

    /**
     * Get adapter configurations
     *
     * @return array
     */
    public function getConfig() {

        return $this->config;
    }


    /**
     * Connect to Mail server
     *
     * @param array $config
     * @throws \RuntimeException
     * @return \Swift_SmtpTransport
     */
    public function connect(array $config) {

        // save config
        $this->config = $config;

        // Create the Transport
        $this->connect = new \Swift_SmtpTransport($config['server'], $config['port'],
            (count($config['socket']) > 0) ? $config['socket'] : null);

        $this->connect->setUsername($config['username']);
        $this->connect->setPassword($config['password']);
        $this->connect->start();

        if($this->connect->isStarted() === false) {
            throw new MailException('Mail connection failed! Check configurations');
        }
        return  $this;

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

        // get mail configurations
        $config = $this->getConfig();

        // add smtp exception plugin to resolve SMTP errors
        $this->mailer->registerPlugin(new MailSMTPExceptions());

        // add decorator plugin to resolve messages placeholders
        $this->mailer->registerPlugin(new \Swift_Plugins_DecoratorPlugin($placeholders));

        // prepare message to transport
        $this->message = \Swift_Message::newInstance();
        $this->message->setFrom([$config['fromEmail'] => $config['fromName']]);
        $this->message->setSubject($subject);
        $this->message->setBody($message, 'text/html');
        $this->message->setCharset('UTF-8');
        $this->message->setPriority(1);
    }

    /**
     * Send message to recipient
     *
     * @param array $recipient ['test@email.com' => 'TestName']
     * @param string $subject
     * @param string $message
     * @param array $placeholders mail body variables
     * @return int count of successfully
     */
    public function send(array $recipient, $subject, $message, array $placeholders = []) {

        // create message if not exists
        $this->createMessage($subject, $message, $placeholders);

        // add recipient
        $this->message->setTo($recipient['email']);

        try {

            // send message
            return $this->mailer->send($this->message);
        }
        catch(\Exception $e) {
            throw new MailException($e->getMessage());
        }
    }
}