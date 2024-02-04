<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/entregable_problem.php";
include_once __DIR__ . "/../Model/student.php";
session_start();

$student_email = $_POST['email'];
$problem_id = intval($_POST['problemId']);
$grade =  $_POST['grade'];

if(checkIfEntregableStudentRelationExists($problem_id, $student_email)){
    updateGradeEntregable($problem_id, $student_email, $grade);
}
else{
    createEntregableStudentRelation($problem_id, $student_email);
    updateGradeEntregable($problem_id, $student_email, $grade);
}

