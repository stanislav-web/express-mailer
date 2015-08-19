<?php
namespace Deliveries\Aware\Handlers;

/**
 * EmailValidator class. SMTP MX email validator
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Handlers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Handlers/EmailValidator.php
 */
class EmailValidator {

    /**
     * @const  MAX_CONNECTION_TIME Max connection time to external socket
     */
    const MAX_CONNECTION_TIME = 30;

    /**
     * @const MAX_READ_TIME Max time for reading external socket
     */
    const MAX_READ_TIME = 5;

    /**
     * @const SMTP_PORT SMTP Port
     */
    const SMTP_PORT = 25;

    /**
     * @const FROM From mail test
     */
    const FROM = 'robot@';

    /**
     * PHP Socket resource from remote MTA
     *
     * @var resource $socket
     */
    private $socket;

    /**
     * SMTP response success codes
     *
     * @var array $successCodes
     */
    private $successCodes = ['220', '250', '451', '452'];

    /**
     * Check status
     *
     * @var boolean $valid
     */
    private $valid = false;

    /**
     * Validate email
     *
     * @var string $mail
     */
    private $email;

    /**
     * Passed domains
     *
     * @var array $domains
     */
    private $domains = [];

    /**
     * Assign an email for verification
     *
     * @param string $email
     */
    public function addEmail($email) {
        $this->email = $email;
    }

    /**
     * Check MX record from DNS
     */
    public function verifySyntax() {

        if(filter_var($this->email, FILTER_VALIDATE_EMAIL) === false) {
            $this->valid = false;
        }
        $this->valid = true;
    }

    /**
     * Verify email via SMTP & MX
     *
     * @return bool
     */
    public function verifySmtp() {

        // get domain name from email
        $domain = $this->getDomain();

        // check if domain is already passed verification
        if(array_key_exists($domain, $this->domains) === true) {

            // return checked state of already verified domain name
            ($this->domains[$domain] == 1) ? $this->valid = true : $this->valid = false;
        }
        else {

            // check domain MX record
            if($this->isMxAvailable($domain) === true) {

                // check domain for smtp connect
                if($this->isSmtpAvailable($domain) === true) {
                    $this->valid = true;
                }
                else {
                    $this->valid = false;
                }
            }
            else {
                $this->valid = false;
            }

            // add as passed domain for next checkout
            $this->domains[$domain] = ($this->valid === true) ? 1 : 0;
        }

        return $this->valid;
    }

    /**
     * Get domain address from an email
     *
     * @return string
     */
    private function getDomain() {
        return substr(strrchr($this->email, "@"), 1);
    }

    /**
     * Check email MX record via DNS
     * Usually a mail server is configured to process emails for a domain.
     * MX Record holds ip address of that mail server, just like a pointer.
     * So by checking MX Records we can make sure whether this domain can process emails or not.
     *
     * @return bool
     */
    private function isMxAvailable($domain) {
        return checkdnsrr($domain, 'MX');
    }

    /**
     * Check SMTP response for domain
     *
     * SMTP Email Validation done by communicating mail server through SMTP commands such as HELO, RCPT To etc.
     * By checking reply code from mail server we can make sure whether given email exist or not.
     *
     * @return bool
     */
    private function isSmtpAvailable($domain) {

        // last fallback is the original domain
        $mxs = $this->getMxRecords($domain);
        $mxs[$domain] = 100;

        $timeout = self::MAX_CONNECTION_TIME / count($mxs);

        // try each host
        while(list($host) = each($mxs)) {

            // connect to SMTP server
            if($this->socket = fsockopen($host, self::SMTP_PORT, $errno, $errstr, (float)$timeout)){
                stream_set_timeout($this->socket, self::MAX_READ_TIME);
                break;
            }
        }

        // did we get a TCP socket
        if(is_resource($this->socket) === true) {

            $code = $this->getReplyCode();

            if(in_array($code, $this->successCodes) === true) {

                // initiate SMTP conversation
                $code = $this->getReplyCode("HELO ".$domain);

                if(in_array($code, $this->successCodes) === true) {

                    // tell of sender
                    $code = $this->getReplyCode("MAIL FROM: <".self::FROM.'@'.$domain.">");
                    return in_array($code, $this->successCodes);
                }
            }

            // quit smtp connection
            fwrite($this->socket, "quit\r\n");

            // close socket
            fclose($this->socket);
        }

        return false;
    }

    /**
     * Get mx records by domain name
     *
     * @param string $domain
     *
     * @return array
     */
    private function getMxRecords($domain) {

        $hosts = [];
        $mxWeights = [];

        // retrieve SMTP Server via MX query on domain
        getmxrr($domain, $hosts, $mxWeights);
        $mxs = array_combine($hosts, $mxWeights);
        asort($mxs, SORT_NUMERIC);

        return $mxs;
    }
    /**
     * Get reply code from SMTP server
     *
     * @param string $msg message to send
     * @param boolean $write is writable
     * @return int
     */
    private function getReplyCode($msg = '', $write = true) {

        if($write === true) {
            fwrite($this->socket, $msg."\r\n");
        }
        $reply = fread($this->socket, 2082);
        preg_match('/^([0-9]{3}) /ims', $reply, $matches);
        return  isset($matches[1]) ? (int)$matches[1] : 0;
    }

    /**
     * Get status of checked email
     *
     * return boolean
     */
    public function isValid() {
        return $this->valid;
    }

    /**
     * Remove checked email
     */
    public function __destruct() {
        unset($this->email);
        unset($this->socket);
        unset($this);
    }
}