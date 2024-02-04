<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/problemsGet.php";
include_once __DIR__ . "/../Model/problemNew.php";
include_once __DIR__ . "/../Model/addFilesToProblem.php";
session_start();

$files = array_filter($_FILES['file']['name']);

$route = str_replace('\\', '/', realpath($_POST['solution_path']));

$problemId = $_POST['problem'];
$sessionId = $_POST['session'];

$rootEdited = filter_var($_POST['root_edited'], FILTER_VALIDATE_BOOLEAN); //De momento no se usa.
$problem = getProblemWithId($problemId);
$subjectId = $problem['subject_id'];
$problem_title = $problem["title"];

if (basename($route) == "teacherSolution") {/*La ruta para guardar la solucion ya fue creada antes, osea ya han subido algun archivo*/}
else{
    $route = "./../app/problemes/$subjectId/$problem_title" . '/teacherSolution';
    mkdir($route, 0777);
}

try {
    uploadFiles($route, $_FILES);

} catch (WrongFileExtension | FileTooLarge $e) {
    $_SESSION['error'] = $e->getMessage();
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
} catch (Exception) {
    $_SESSION['error'] = "Error desconegut";
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
}

addProblemTeacherSolutionRoute($problemId,$route);

$directory = $route;
$files_scanned_directory = array_diff(scandir($directory), array('..', '.','__pycache__'));
$file_text=[];
foreach ($files_scanned_directory as $file) {

  if(!str_contains($file,'.txt')) { //Avoid .txt files
      $file_text_dict = file($directory . "/" . $file); //Returns the file in an array. Each element of the array corresponds to a line in the file. Upon failure, file() returns false.

      if($file_text_dict) {
          $trim_file_text =  array_values(array_filter($file_text_dict , "trim"));
          foreach ($trim_file_text as $k => $v) {
              array_push($file_text, $v);
          }
      }
      else{
          echo "Error leyendo el fichero: " . $file;
      }
  }
}
$problemLines = count($file_text);
$str_file_text = implode($file_text);
$problemQualityInfo =[substr_count($str_file_text ,'if (') + substr_count($str_file_text ,'if('),
    "-", substr_count($str_file_text ,'for (')+ substr_count($str_file_text ,'for('),
    "-", substr_count($str_file_text ,'while (') + substr_count($str_file_text ,'while('),
    "-", substr_count($str_file_text ,'switch (') + substr_count($str_file_text ,'switch(')];

addProblemExtraData($problemId, $problemLines, implode($problemQualityInfo));

if($sessionId!=NULL) {
    $params = array("problem" => $problemId, "session" => $sessionId);// OJO Pude dar conflicto si estamos en una sesión.???
}
else{
    $params = array("problem" => $problemId);// OJO Pude dar conflicto si estamos en una sesión.???
}

if($_POST["query"] == VIEW_EDITOR ) {

  redirectLocation(VIEW_EDITOR, params: $params); //REDIRECCIONA A PROBLEMAS NO A SESIONES.
}
elseif($_POST["query"] == VIEW_PROBLEM_SOLUTION){

   redirectLocation(VIEW_PROBLEM_SOLUTION, params: $params);
}
