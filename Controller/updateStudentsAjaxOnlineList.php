<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/Messages.php";
include_once __DIR__ . "/../Model/online_visualization.php";
include_once __DIR__ . "/../Model/Messages.php";

$session_id= $_POST['sessionId'];
$problem_id = $_POST['problemId'];

$students = getStudentsWithSessionAndProblem($session_id, $problem_id );
//if(count($students) > 0) {

echo json_encode($students);

