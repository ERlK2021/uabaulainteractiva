<?php
session_start();
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/Messages.php";

$outgoing_email = $_POST["o_mail"];
$incoming_email= $_POST["i_mail"];
$sessionId=$_POST["sessionId"];
$message = $_POST["message"];
$problemId = $_POST['problem'];
$date = date('d/m H:i');

$exit_sending_message = addMessagetoChat($incoming_email, $outgoing_email, $sessionId, $problemId, $message, $date);
if($exit_sending_message){
    echo $outgoing_email . " ".  $incoming_email . " " . $sessionId . " " . $message;
    redirectLocation(query: VIEW_MESSAGES_CHAT, params:array('problem' => $problemId, 'show-chat' =>1, 'user'=>$incoming_email,'session'=>$sessionId));//Esto fallará cuando sea un alumno enviando mensaje a profe.

}
