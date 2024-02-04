<?php
include_once __DIR__ . "/connection.php";
$connection = connectDB();

#var_dump($_POST);
$sessionId = $_POST['sessionId'];
$newVisibility = $_POST['newVisibility'];

/*var_dump($sessionId);
var_dump($newVisibility);*/

/*$statement = $connection->prepare("UPDATE problem SET visibility=:visibility WHERE id= :problemId");
$statement->execute(array(':problemId'=>$problemId, ':visibility'=>$newVisibility));
$data = $statement->fetch(PDO::FETCH_ASSOC);
$connection = null;*/

try {
    $statement = $connection->prepare("UPDATE session SET status=:status WHERE id= :sessionId");
    $statement->execute(array(':sessionId'=>$sessionId, ':status'=> $newVisibility));
    $data = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'The connection failed: ' . $e->getMessage() . "\n";
}
$connection = null;