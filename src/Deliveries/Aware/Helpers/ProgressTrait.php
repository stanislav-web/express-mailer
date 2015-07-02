<?php
namespace Deliveries\Aware\Helpers;

use Symfony\Component\Console\Helper\ProgressBar;

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
     * @param int $output
     * @param string $format
     * @return ProgressBar
     */
    public function getProgress(\Symfony\Component\Console\Output\OutputInterface $output, $format = 'normal', $finish = 100) {

        ProgressBar::setFormatDefinition('minimal', 'Progress: %percent%%');
        $progress = new ProgressBar($output, $finish);
        $progress->setBarCharacter('<comment>=</comment>');
        $progress->setRedrawFrequency(100);
        $progress->setFormat($format);

        return $progress;
    }

}