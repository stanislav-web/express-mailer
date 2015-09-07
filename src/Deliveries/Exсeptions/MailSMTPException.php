<?php
namespace Deliveries\Exceptions;

use Deliveries\Aware\Handlers\BaseException;

/**
 * MailSMTPException class. SMTP exception class
 *
 * @package Deliveries
 * @subpackage Deliveries\Exceptions
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Exceptions/MailSMTPException.php
 */
class MailSMTPExceptions extends BaseException
    implements \Swift_Events_TransportExceptionListener {

    /**
     * Invoked as a TransportException is thrown in the Transport system.
     *
     * @param \Swift_Events_TransportExceptionEvent $evt
     * @throws \Swift_TransportException
     */
    public function exceptionThrown(\Swift_Events_TransportExceptionEvent $evt)
    {
        $evt->cancelBubble(true);
        try{

            throw $evt->getException();
        }
        catch(\Swift_TransportException $e) {
            throw new MailException($e->getMessage(), 'warning');
        }
    }
}