<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/Messages.php";
include_once __DIR__ . "/../Model/login.php";
include_once __DIR__ . "/../Model/session.php";
include_once  __DIR__ . "/../Model/problemsGet.php";

//TEACHER
$outgoing_email = $_SESSION["email"];//teacher
$incoming_email= $_GET['user'];//student
$sessionId = $_GET['session'];
$problemId = $_GET['problem'];
$problemName = getProblemWithId($problemId)['title'];
$sessionName = getSessionName($sessionId );

$messages = viewchats($incoming_email, $outgoing_email, $sessionId, $problemId);
changeViewedChatStatus($incoming_email); //Canviar status de los mesajes de estudiante del qual el profesor este entrando y leyendo su chat. Su status pasará de ser 0 (univewd) a 1(viwed)

$student_data = getNameStudent($incoming_email);
$teacher_data = getNameTeacher($outgoing_email);
include_once __DIR__ . "/../View/html/chatMessages.php";