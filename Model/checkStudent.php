<?php
session_start();

include_once __DIR__ . "/connection.php";
$connection = connectDBMoodle();
$route = $_POST['route'];
$variable = 1;

$statement = $connection->prepare("SELECT * FROM mdl_uab_interactive_solucio WHERE route=:route");
$statement->execute(array(":route" => $route));
$solutions = $statement->fetch(PDO::FETCH_ASSOC);
$connection = null;

echo $solutions['editing'];
