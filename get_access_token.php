<?php
// get_access_token.php

function getMpesaAccessToken() {
    $consumerKey = 'your_consumer_key'; // Replace with your consumer key
    $consumerSecret = 'your_consumer_secret'; // Replace with your consumer secret
    $url = 'https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'; // Live URL
    // For sandbox: 'https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials'

    $credentials = base64_encode($consumerKey . ':' . $consumerSecret);

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_HTTPHEADER, ['Authorization: Basic ' . $credentials]);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($curl);
    $jsonResponse = json_decode($response);

    curl_close($curl);

    return $jsonResponse->access_token;
}
