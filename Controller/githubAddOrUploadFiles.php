<?php
session_start();
include_once __DIR__ . '/../Model/constants.php';
include_once __DIR__ . '/../Model/redirectionUtils.php';
include_once __DIR__ . '/../Model/githubAuthClient.php';
include_once __DIR__ . '/../Model/github.php';

if (!isset($_SESSION['access_token'])) {
    $_SESSION['prev_page'] = $_SERVER['PHP_SELF'];
    $_SESSION['repo_link'] = $_POST['repo_link'];
    $_SESSION['solution_path'] = $_POST['solution_path'];
    $_SESSION['problem_id'] = $_POST['problem_id'];
    $_SESSION['upload_files'] = $_POST['upload_files'];
    header('Location: /Model/githubAccessToken.php');
}

$client = authClient();
// Collect the data of the form
$lookupArray = isset($_SESSION['repo_link'])? $_SESSION: $_POST;
$repoLink = $lookupArray['repo_link'];
$solutionPath = $lookupArray['solution_path'];
$problemId = $lookupArray['problem_id'];
$uploadFiles = $lookupArray['upload_files'] == "true";

$userName = $_SESSION['user'];
$userEmail = $_SESSION['email'];

// Clear the session variables
if ($lookupArray == $_SESSION) {
    unset($_SESSION['repo_link']);
    unset($_SESSION['solution_path']);
    unset($_SESSION['problem_id']);
    unset($_SESSION['upload_files']);
}

$redirectParams = array("problem"=>$problemId);
try{
    if ($uploadFiles) {
        $operation = "uploaded";
        uploadDirectoryToGithub(client: $client, repoLink: $repoLink, userName: $userName, userEmail: $userEmail,
            directory: $solutionPath);
    } else {
        $operation = "files_added";
        addFilesFromGithub(client: $client, repoLink: $repoLink, route: $solutionPath);
    }
    $operationCompleted = 1;
}catch (Exception) {
    $operationCompleted = 0;
}
$redirectParams[$operation] = $operationCompleted;

redirectLocation(VIEW_EDITOR, $redirectParams);
