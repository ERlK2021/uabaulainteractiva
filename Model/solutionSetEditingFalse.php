<?php
include_once __DIR__ . "/connection.php";
include_once __DIR__ . "/constants.php";

if ($_POST['folder'] == STUDENT) {
    return;
}

$connection = connectDB();
$route = $_POST['folder'];

$statement = $connection->prepare("UPDATE solution SET editing=0 WHERE route= :route");
$statement->execute(array(":route" => $route));
$solution = $statement->fetch(PDO::FETCH_ASSOC);

$connection = null;
