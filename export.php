<?php

session_start();

date_default_timezone_set('Asia/Kuala_Lumpur');
$timestamp = date('h:i:s a', time());

echo $timestamp;


?>