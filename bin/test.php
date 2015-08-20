<?php
$start = microtime(true);

require 'threads.php';

$threads = new Threads;

for ($i=0;$i<10;$i++) {
    $threads->newThread('/Users/stanislavmenshykh/Projects/express-mailer/bin/delay.php', array('delay' => rand(1, 5)));
}

while (false !== ($result = $threads->iteration())) {
    if (!empty($result)) {
        echo $result."\r\n";
    }
}

$end = microtime(true);
echo "Execution time ".round($end - $start, 2)."\r\n";

?>