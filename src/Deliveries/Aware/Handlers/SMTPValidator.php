<?php
namespace Deliveries\Aware\Handlers;

/**
 * SMTPValidator class. SMTP MX email validator
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Handlers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Handlers/SMTPValidator.php
 */
class SMTPValidator {

    /**
     * Check status
     *
     * @var int $status
     */
    private $status = 0;

    /**
     * Validate email
     *
     * @var string $mail
     */
    private $email;

    /**
     * Passed domains
     *
     * @var string $domains
     */
    private $domains = [];

    /**
     * Assign an email for verification
     *
     * @param string $email
     */
    public function __construct($email) {
        $this->email = $email;
    }

    /**
     * Check MX record from DNS
     */
    public function verifyMX() {

        $domain = substr(strrchr($this->email, "@"), 1);

        if(in_array($domain, $this->domains) === false) {

            if(checkdnsrr($domain)) {
                $this->status = false;
            }else {
                $this->status = true;
            }

            // add as passed domain
            $this->domains[] = substr(strrchr($this->email, "@"), 1);
        }

    }

    /**
     * Get status of checked email
     *
     * return boolean
     */
    public function isValid() {
        return $this->status;
    }

    /**
     * Remove checked email
     */
    public function __destruct() {
        unset($this->email);
        unset($this);
    }
}