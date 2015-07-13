<?php
namespace Deliveries\Aware\Helpers;
use Symfony\Component\Console\Helper\Table;

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
     * Draw console table multiple rows down
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array                                             $content
     */
    public function tableLong(\Symfony\Component\Console\Output\OutputInterface $output, array $content) {

        // write config table
        $table = new Table($output);
        $headers = array_keys($content[0]);
        $table->setHeaders($headers);
        $rows = [];
        foreach($content as $row) {
            $rows[] = $row;
        }
        $table->setRows($rows);
        $table->render();
    }

    /**
     * Draw console table
     *
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @param array                                             $content
     */
    public function table(\Symfony\Component\Console\Output\OutputInterface $output, array $content) {

        // write config table
        $table = new Table($output);

        if(count($content) > 0) {

            // multiple tables
            foreach($content as $header => $rows) {
                $output->writeln("\n<comment>" . $header . "</comment>");

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
}