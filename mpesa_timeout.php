<?php
// mpesa_timeout.php
$mpesaResponse = file_get_contents('php://input');
$logFile = "mpesa_timeouts.txt";
$log = fopen($logFile, "a");
fwrite($log, $mpesaResponse);
fclose($log);

// Log timeout response for troubleshooting
