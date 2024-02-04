<?php

function connectDBMoodle(): ?PDO
{
    $connection = null;
    $moodleDB = "moodle_db"; //DATABASE NAME
    $username = "root"; //DATABASE PASSWORD
    $password = "";
    try {
        $connection = new PDO('mysql:host=localhost;dbname='.$moodleDB, $username, $password);
        if (mysqli_connect_errno()) {
            echo mysqli_connect_error();
        }
    } catch (PDOException $e) {
        echo 'The connection failed: ' . $e->getMessage() . "\n";
    }
    return $connection;
}
