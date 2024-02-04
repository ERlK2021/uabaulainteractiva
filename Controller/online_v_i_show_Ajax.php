<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/online_visualization.php";
include_once __DIR__ . "/../Model/problemsGet.php";

$student_mail = $_POST['email'] ;
$sessionId = $_POST['id'] ;
$problemId = $_POST['problemId'];

$students_solution_data = getStudentsSessionExtraData($sessionId, $problemId); //student_email, output, executed_times_count, teacher_executed_times_count, number_lines_file, solution_quality
$extraData = getProblemExtraData($problemId);

$official_solution_lines = $extraData['solution_lines'];
//$official_solution_quality = $extraData['solution_quality'];

foreach ($students_solution_data as $student_solution_data) {

    if ($student_solution_data['student_email'] == $student_mail) {

        $solution_quality = explode("-", $student_solution_data['solution_quality']);
        $student_lines_percentage = null;

        if($official_solution_lines!=0){
            $student_lines_percentage = intval((intval($student_solution_data['number_lines_file']) * 100) / $official_solution_lines);
        };

        echo json_encode(array('solution_quality' => $solution_quality , 'number_lines_file' => $student_solution_data['number_lines_file'], 'student_lines_percentage' => $student_lines_percentage,
        'student_executions' => $student_solution_data['executed_times_count'], 'student_output' => $student_solution_data['output']));
    }
}