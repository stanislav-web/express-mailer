<?php
namespace Deliveries\Console;

use Symfony\Component\Console\Application as AppConsole;

/**
 * Application class. Application console access point
 *
 * @package Deliveries
 * @subpackage Deliveries\Console
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Console/Application.php
 */
class Application extends AppConsole {

    /**
     * App version
     */
    const VERSION = '1.0';

    /**
     * App name
     */
    const NAME = 'Express mailer';
}