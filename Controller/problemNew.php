<?php
session_start();
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/problemNew.php";
include_once __DIR__ . "/../Model/addFilesToProblem.php";

$title = $_POST['title'];
$description = $_POST['description'];
$max_memory_usage = $_POST['max_memory_usage'];
$max_execution_time = $_POST['max_execution_time'];
$visibility = 'private'; # $_POST['visibility'];
$language = $_POST['language'];
$subjectId = $_POST['subject'];
$entregable = isset($_POST['entregable']) ? $_POST['entregable'] : "off";
$deadline="";
if(isset($_POST['datepicker'])){

    $deadline = ($_POST['datepicker'] != "")? date('Y-m-d',strtotime($_POST['datepicker'])) : null;
}else {$deadline = null; }

# If the title already exists redirect the user to the error view.
if (problemTitleExists($title)) {
    $_SESSION['error'] = "Un problema amb el mateix nom ja existeix: $title";
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
}

$subjectRoute = "../app/problemes/$subjectId/";
$problemRoute = trim($subjectRoute . $_POST['title']);
$problemId = createProblem($problemRoute, $title, $description, $max_memory_usage, $visibility, $max_execution_time, $language, $subjectId, $entregable, $deadline);

# If any problem arises when creating the problem redirect the user to the error view
if ($problemId === -1) {
    $_SESSION['error'] = "Error desconegut al crear el problema a la BDD";
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
}

if (!file_exists($subjectRoute)) {

      print_r("Directorio actual: " . __DIR__ . "<br>");

      print_r("Fichero $subjectRoute no existe <br>");
      $exists = mkdir($subjectRoute, 0777);
      
      if($exists == true){
        print_r("La ruta $subjectRoute, SI se ha generado con exito <br>");
      }elseif($exists == false){
        print_r("La ruta $subjectRoute, NO se ha generado con exito <br>");
      }
      
}
# Create the folder of the problem, by default with 0777 permission
$exists = mkdir($problemRoute, 0777);

if($exists == true){
  print_r("La ruta problemRoute =  $problemRoute SI se ha generado con exito");
}
elseif($exists == false){
  print_r("La ruta problemRoute = $problemRoute NO se ha generado con exito");
}

$solutionRoute = $problemRoute . '/teacherSolution';
mkdir($solutionRoute);

try {

    uploadFilesCopy($solutionRoute, $_FILES);
    uploadFilesCopy($problemRoute, $_FILES);

    $tmp_files = array_column($_FILES ,'tmp_name');
    $flattened_array = array_values($tmp_files[0]);
    foreach ($flattened_array as $tmp_file) {
        if (file_exists($tmp_file)) {
            unlink($tmp_file);
        }
    }

} catch (WrongFileExtension | FileTooLarge $e) {
    $_SESSION['error'] = $e->getMessage();
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
} catch (Exception) {
    $_SESSION['error'] = "Error desconegut al crear el problema";
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
}

addProblemTeacherSolutionRoute($problemId, $solutionRoute);
$directory = $solutionRoute;
$files_scanned_directory = array_diff(scandir($directory), array('..', '.','__pycache__'));
$file_text=[];
foreach ($files_scanned_directory as $file) {

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
$problemLines = count($file_text);
$str_file_text = implode($file_text);
$problemQualityInfo =[substr_count($str_file_text ,'if (') + substr_count($str_file_text ,'if('),
    "-", substr_count($str_file_text ,'for (')+ substr_count($str_file_text ,'for('),
    "-", substr_count($str_file_text ,'while (') + substr_count($str_file_text ,'while('),
    "-", substr_count($str_file_text ,'switch (') + substr_count($str_file_text ,'switch(')];

addProblemExtraData($problemId, $problemLines, implode($problemQualityInfo));

redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'created' => $problemId));
