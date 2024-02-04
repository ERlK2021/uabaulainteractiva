<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/problemsGet.php";
include_once __DIR__ . "/../Model/addFilesToProblem.php";
session_start();

$files = array_filter($_FILES['file']['name']);

$route = $_POST['solution_path']; //$route = str_replace('\\', '/', realpath($_POST['solution_path']));
$problemId = $_POST['problem'];
$rootEdited = filter_var($_POST['root_edited'], FILTER_VALIDATE_BOOLEAN);

$problem = getProblemWithId($problemId);
$subjectId = $problem['subject_id'];
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

$params = array("problem" => $problemId);
if ($rootEdited) {
    # Set the solution as edited for the students
    setSolutionAsEdited($problemId);
    $params['edit'] = 1;
}

redirectLocation(query:VIEW_EDITOR, params: $params);
