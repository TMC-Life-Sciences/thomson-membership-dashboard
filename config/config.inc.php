<?php

date_default_timezone_set('Asia/Kuala_Lumpur');

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// $servername = "110.4.45.238";
// $username = "thomson1_thkdmembership";
// $password = "U2YGq{(Sa3~a";
// $database = "thomson1_thkdmembership";

$servername = $_ENV['DB_SERVER'];
$username = $_ENV['DB_USERNAME'];
$password = $_ENV['DB_PASSWORD'];
$database = $_ENV['DB_DATABASE'];

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SMTP configuration
$smtpHost = 'mail.thomsonhospitalsapps.com';
$smtpUsername = 'no-reply@thomsonhospitalsapps.com';
$smtpPassword = 'n0e$*phkpo(!';
$smtpSecure = 'ssl';
$smtpPort = 465;
$fromEmail = 'no-reply@thomsonhospitalsapps.com';
$fromName = 'Thomson Hospital Kota Damansara';

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
