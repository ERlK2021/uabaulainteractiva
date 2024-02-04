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
$date = date('d/m H:i:s');

$exit_sending_message = sendComunMessage($outgoing_email, $sessionId, $problemId, $message, $date);
if($exit_sending_message){
    //echo $outgoing_email . " ".  $incoming_email . " " . $sessionId . " " . $message;
    redirectLocation(query: VIEW_COMUN_MESSAGES, params:array('problem' => $problemId,'session'=>$sessionId));
}