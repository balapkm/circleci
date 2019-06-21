<?php
echo "Jegan ASASD New array_change_key_case sd snadasnd circleci run successfullddy  vvvv ff 111 new change\n";
// exit;
$servername = "localhost";
$username = "root";
$password = "Bala@123";
try {
    $conn = new PDO("mysql:host=$servername", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully circleci run successfullddy  vvvv ff 111\n";
}
catch(PDOException $e)
{
    echo "Connection failed: " . $e->getMessage() . "\n";
    die(1);
}

?>
