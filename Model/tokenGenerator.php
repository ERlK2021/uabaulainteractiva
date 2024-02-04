<?php
include_once __DIR__ . "/connection.php";
//Generate a random string.
$token = openssl_random_pseudo_bytes(32);

//Convert the binary data into hexadecimal representation.
$token = bin2hex($token);

$connection = connectDB();
$statement = $connection->prepare("INSERT INTO tokens(value) VALUES (:token)");
$statement->execute(array(":token" => $token));

$statement->closeCursor();
$connection = null;
echo $token;
