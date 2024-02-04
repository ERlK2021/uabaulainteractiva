<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/online_visualization.php";

$user_type = $_POST['userType'];// 1 = Student, 0 = teacher.
$session_id = intval($_POST['id']);
$user_email = $_POST['email']; //Este user puede ser tanto el mail del profesor como el del alumno
$output = $_POST['output'];
$problemId = $_POST['problemId'];
$directory = $_POST['route'];

$files_scanned_directory = array_diff(scandir($directory), array('..', '.','__pycache__'));
$file_text=[];

foreach ($files_scanned_directory as $file) {
    if(!str_contains($file,'.txt')) { //Avoid .txt files
        $file_text_dict = file($directory . "/" . $file); //Returns the file in an array. Each element of the array corresponds to a line in the file. Upon failure, file() returns false.

        if ($file_text_dict) {
            $trim_file_text = array_values(array_filter($file_text_dict, "trim"));
            foreach ($trim_file_text as $k => $v) {
                array_push($file_text, $v);
            }
        } else {
            echo "Error leyendo el fichero: " . $file;
        }
    }
}
$problemLines = count($file_text);
$str_file_text = implode($file_text);
$problemQualityInfo = implode([substr_count($str_file_text ,'if (') + substr_count($str_file_text ,'if('),
    "-", substr_count($str_file_text ,'for (')+ substr_count($str_file_text ,'for('),
    "-", substr_count($str_file_text ,'while (') + substr_count($str_file_text ,'while('),
    "-", substr_count($str_file_text ,'switch (') + substr_count($str_file_text ,'switch(')]);

echo $problemQualityInfo;

if($user_type == 1) {// STUDENT

    $array_student_sessions = getStudentSessionRelation();
    $exists = false;
    foreach ($array_student_sessions as $array_student_session) {

        if ($array_student_session['student_email'] == $user_email && $array_student_session['session_id'] == $session_id) {
            $exists = true;
        }
    }
    if (!$exists) { //Student-Sesion aun no existe
        if($output != null) { createStudentSessionRelation($user_email, $session_id, $output, $problemId, $problemLines, $problemQualityInfo); }
        else{ createStudentSessionRelationNoOutput($user_email, $session_id, $problemId, $problemLines, $problemQualityInfo); }
    } else {
        if($output != null) { updateData($user_email, $session_id, $output, $problemId, $problemLines, $problemQualityInfo); }
        else{ updateDataNoOutput($user_email, $session_id, $problemId, $problemLines, $problemQualityInfo); }
    }
}
else{ //$user_type == 0 -> El profesor está editando el código de un alunmno guardado en la variable $_POST['usuario_visualizado']

    $mate = $_POST['usuario_visualizado']; //El PROFESOR(0) está ejecutando el código de este alumno.
    teacherUpdatesStudentCode($mate, $session_id, $output, $problemId, $problemLines, $problemQualityInfo);
}
