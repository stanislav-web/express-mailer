<?php

require 'threads.php';

if ($params = Threads::getParams()) {
    sleep($params['delay']);
    echo 'Wait for '.$params['delay'].' s.';
}

?>