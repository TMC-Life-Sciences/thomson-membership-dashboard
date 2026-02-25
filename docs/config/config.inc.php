<?php

$timestamp = date('Y-m-d H:i:s', strtotime('+8 hours'));
$date = date('Y-m-d', strtotime('+8 hours'));
$time = date('H:i:s', strtotime('+8 hours'));

$servername = "110.4.45.238";
$username = "thomson1_admmrd";
$password = "jpVgI.H+EGwE8PhM-W";
$database = "thomson1_mrd";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);
$con = mysqli_connect("110.4.45.238", "thomson1_admmrd", "jpVgI.H+EGwE8PhM-W", "thomson1_mrd");


// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SMTP configuration
$smtpHost = 'mail.thomsonhospitalsapps.com';
$smtpUsername = 'no-reply@medicalreport.thomsonhospitalsapps.com';
$smtpPassword = 'R4(#sFEDhS51vtz[i8';
$smtpSecure = 'ssl';
$smtpPort = 465;
$fromEmail = 'no-reply-onlinemedicalreport@tmclife.com';
$fromName = 'THKD Online Medical Report';

$key = bin2hex(openssl_random_pseudo_bytes(32)); // Generates a 256-bit key

function encryptor(
    $action,
    $string
) {
    $output = false;
    $encrypt_method = "AES-256-CBC";

    $secret_key = 'Tech Area';
    $secret_iv = 'tech@12345678';


    $key = hash(
        'sha256',
        $secret_key
    );


    $iv = substr(hash('sha256', $secret_iv), 0, 16);


    if ($action == 'encrypt') {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if (
        $action == 'decrypt'
    ) {

        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0,      $iv);
    }

    return $output;
}
