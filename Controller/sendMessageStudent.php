<?php
session_start();
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/Messages.php";
include_once __DIR__ . "/../Model/session.php";

$outgoing_email = $_POST["o_mail"]; //Estudiante
//$incoming_email= $_POST["i_mail"];
$sessionId=$_POST["sessionId"];
$message = $_POST["message"];
$problemId = $_POST['problem'];
$date = date('d/m H:i');

if(count(viewchatsAsStudent($outgoing_email, $sessionId, $problemId))==0){
    $teacherdata = getTeacherCreatedSession($sessionId);
    print_r($teacherdata);
    $teacher_email = $teacherdata[0]['email'];

}
else{
    $teacher_email = getTeacherEmailFromChat($outgoing_email, $sessionId, $problemId);
}


$incoming_email = $teacher_email;

$exit_sending_message = addMessagetoChat($incoming_email, $outgoing_email, $sessionId, $problemId, $message, $date);
if($exit_sending_message){
    echo $outgoing_email . " ".  $incoming_email . " " . $sessionId . " " . $message;
    redirectLocation(query: VIEW_EDITOR, params:array('session'=>$sessionId, 'problem' => $problemId));

}