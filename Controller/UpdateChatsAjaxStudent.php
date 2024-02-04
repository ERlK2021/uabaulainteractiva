<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/Messages.php";

$outgoing_email = $_POST['outgoing_email'];
$sessionId = $_POST['sessionId'];
$problemId = $_POST['problemId'];

$messages = viewchatsAsStudent($outgoing_email, $sessionId, $problemId );
echo json_encode($messages);