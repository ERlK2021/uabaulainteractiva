<?php
include_once __DIR__ . "/connection.php";
$connection = connectDB();

$problemId = $_POST['problemId'];
$newVisibility = $_POST['newVisibility'];

$statement = $connection->prepare("UPDATE problem SET visibility=:visibility WHERE id= :problemId");
$statement->execute(array(':problemId'=>$problemId, ':visibility'=>$newVisibility));
$data = $statement->fetch(PDO::FETCH_ASSOC);
$connection = null;
