<?php
namespace Deliveries\Adapter\Mail;

use Deliveries\Aware\Adapter\Mail\MailProviderInterface;

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
}