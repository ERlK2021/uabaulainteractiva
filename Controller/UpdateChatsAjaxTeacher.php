<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/Messages.php";

$outgoing_email = $_POST['outgoing_email'];
$incoming_email = $_POST['incoming_email'];
$sessionId = $_POST['sessionId'];
$problemId = $_POST['problemId'];

$messages = viewchats(incoming_mail: $incoming_email, outgoing_mail: $outgoing_email, session_id:$sessionId, problem_id:$problemId);
echo json_encode($messages);