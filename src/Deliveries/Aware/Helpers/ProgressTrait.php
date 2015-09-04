<?php
namespace Deliveries\Aware\Helpers;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
/**
 * ProgressTrait trait. CLI Progress bar helper
 *
 * @package Deliveries
 * @subpackage Deliveries\Aware\Helpers
 * @since PHP >=5.5
 * @version 1.0
 * @author Stanislav WEB | Lugansk <stanisov@gmail.com>
 * @copyright Stanislav WEB
 * @filesource /Deliveries/Aware/Helpers/ProgressTrait.php
 */
trait ProgressTrait {

    /**
     * Progress bar define
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param int                                               $finish
     * @param string                                            $format
     * @return \Symfony\Component\Console\Helper\ProgressBar
     */
    public function getProgress(OutputInterface $output, $finish = 100, $format = 'normal') {

        $progress = new ProgressBar($output, $finish);
        $progress->setProgressCharacter('ϟ');
        $progress->setFormat($format);
        $progress->setBarCharacter('<comment>=</comment>');

        return $progress;
    }

}