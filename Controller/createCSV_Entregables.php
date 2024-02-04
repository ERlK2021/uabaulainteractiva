<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/problemNew.php";
include_once __DIR__ . "/../Model/entregable_problem.php";
include_once __DIR__ . "/../Model/student.php";

$problem_id = $_GET['problem'];
$problem = getProblemWithId($problem_id);
$problem_title = $problem["title"];
if(getIfProblemIsEntregable($problem_id)){

    header('Content-Type: text/csv'); header('Content-Disposition: attachment; filename=' . $problem_title . '.csv');

    $array_student_data = getEntregableData($problem_id);

   /* array(2) {
        [0]=>
  array(3) {
            ["problem_id"]=>
    int(227)
    ["student_NIU"]=>
    int(1525722)
    ["grade"]=>
    int(10)
  }
  [1]=>
  array(3) {
            ["problem_id"]=>
    int(227)
    ["student_NIU"]=>
    int(1525724)
    ["grade"]=>
    int(10)
  }*/
    $user_CSV[0] = array('Name','Surname', 'NIU', 'Grade');
    foreach ($array_student_data as $student_data){
        $student_full_name = getStudentFullNameByNiu($student_data['student_NIU']);
        array_push($user_CSV, array($student_full_name['name'], $student_full_name['surname'], $student_data['student_NIU'],$student_data['grade']));
    }

    $fp = fopen('php://output', 'w');
    foreach ($user_CSV as $line) {
        // though CSV stands for "comma separated value in many countries (including France) separator is ";"
        fputcsv($fp, $line, ';');
    }

    fclose($fp);


}else{
    echo "Aquest problema no es entregable";
}
