<?php
namespace Deliveries\Aware\Helpers;
use Symfony\Component\Console\Helper\Table;
use Deliveries\Aware\Handlers\EmailValidator;

/**
 * FormatTrait trait.
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Helpers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Helpers/FormatTrait.php
 */
trait FormatTrait {

    /**
     * Email validator
     *
     * @var \Deliveries\Aware\Handlers\EmailValidator $validator
     */
    protected $validator;

    /**
     * Draw console table multiple rows down
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array                                             $content
     */
    protected function tableLong(\Symfony\Component\Console\Output\OutputInterface $output, array $content) {

        // write config table
        $title = key($content);
        $content = array_shift($content);
        $table = new Table($output);
        $output->writeln("<fg=yellow;options=bold>" . $title . "</fg=yellow;options=bold>");

        if(empty($content)  === false) {
            $headers = array_keys($content[0]);
            $table->setHeaders($headers);
            $rows = [];
            foreach($content as $row) {
                $rows[] = $row;
            }
            $table->setRows($rows);
            $table->render();
        }
    }

    /**
     * Draw console table
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array                                             $content
     */
    protected function table(\Symfony\Component\Console\Output\OutputInterface $output, array $content) {

        // write config table
        $table = new Table($output);

        if(count($content) > 0) {

            // multiple tables
            foreach($content as $header => $rows) {
                $output->writeln("<fg=yellow;options=bold>" . $header . "</fg=yellow;options=bold>");

                $table->setHeaders(array_keys($rows))
                    ->setRows([$rows])
                    ->render();
            }
        }
        else {
            $table
                ->setHeaders(array_keys($content))
                ->setRows(array_values($content));
            $table->render();
        }
    }

    /**
     * Verify date
     *
     * @param string $date
     * @param boolean $strict
     * @throws \RuntimeException
     */
    protected function verifyDate($date, $strict = true) {

        \DateTime::createFromFormat('Y-m-d H:i:s', $date);

        if ($strict) {
            $errors = \DateTime::getLastErrors();
            if (!empty($errors['warning_count'])) {

                throw new \RuntimeException(reset($errors['warnings']));
            }
        }
    }

    /**
     * Validate email addresses via SMTP, validate syntax
     *
     * @param string $email subscriber email
     * @param boolean $syntax validate syntax of email
     * @param boolean $smtp smtp mx record & lookup verify
     *
     * @return \Deliveries\Aware\Handlers\EmailValidator
     */
    public function verifyEmail($email, $syntax = true, $smtp = true) {

        if(!$this->validator) {
            $this->validator = new EmailValidator();
        }

        // verify assigned email address
        $this->validator->addEmail($email);

        if($syntax === true) {
            // check email syntax
            $this->validator->verifySyntax();
        }

        if($smtp === true) {
            // check via dns mx records & smtp
            $this->validator->verifySmtp();
        }

        return $this->validator;
    }

    /**
     * Format array keys as format
     *
     * @param array $params
     * @param string $format
     */
    protected function arrayKeysPlaceholders(array $params, $format = "{%s}") {

        $result = [];
        foreach($params as $key => $value) {
            $result[sprintf($format, $key)] = $value;
        }

        return $result;
    }

    /**
     * Get short class name
     *
     * @param object $object any class instance
     * @return string
     */
    protected function getClassName($object) {
        return (new \ReflectionClass($object))->getShortName();
    }
}