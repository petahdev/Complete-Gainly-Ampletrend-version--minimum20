<?php
// mpesa_result.php
$mpesaResponse = file_get_contents('php://input');
$logFile = "mpesa_results.txt";
$log = fopen($logFile, "a");
fwrite($log, $mpesaResponse);
fclose($log);

// You can decode the response and save to your database
$decodedResponse = json_decode($mpesaResponse, true);

// Example: You can extract the status
$status = $decodedResponse['Result']['ResultCode']; // 0 means success
