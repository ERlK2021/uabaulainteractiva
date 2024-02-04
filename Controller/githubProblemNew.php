<?php
session_start();
include_once __DIR__ . '/../Model/constants.php';
include_once __DIR__ . '/../Model/redirectionUtils.php';
include_once __DIR__ . '/../Model/exceptions.php';
include_once __DIR__ . '/../Model/connection.php';
include_once __DIR__ . '/../Model/problemNew.php';
include_once __DIR__ . '/../Model/githubAuthClient.php';
include_once __DIR__ . '/../Model/github.php';


if (!isset($_SESSION['access_token'])) {
    $_SESSION['prev_page'] = $_SERVER['PHP_SELF'];
    // If the access token is not set the data is moved to the session
    $_SESSION['repo_link'] = $_POST['repo_link'];
    $_SESSION['max_execution_time'] = $_POST['max_execution_time'];
    $_SESSION['max_memory_usage'] = $_POST['max_memory_usage'];
    $_SESSION['visibility'] = $_POST['visibility'];
    $_SESSION['language'] = $_POST['language'];
    $_SESSION['subject'] = $_POST['subject'];
    header('Location: /Model/githubAccessToken.php');
    return;
}

// Collect the data of the form
$lookupArray = isset($_SESSION['repo_link'])? $_SESSION: $_POST;
$repoLink = $lookupArray['repo_link'];
$maxExecutionTime = $lookupArray['max_execution_time'];
$maxMemoryUsage = $lookupArray['max_memory_usage'];
$visibility = $lookupArray['visibility'];
$language = $lookupArray['language'];
$subjectId = $lookupArray['subject'];

// Clear the session variables
if ($lookupArray == $_SESSION) {
    unset($_SESSION['repo_link']);
    unset($_SESSION['max_execution_time']);
    unset($_SESSION['max_memory_usage']);
    unset($_SESSION['visibility']);
    unset($_SESSION['language']);
    unset($_SESSION['subject']);
}

try {
    $client = authClient();
    $returnedData = downloadDirectoryFromGithub(client: $client, repoLink: $repoLink, subjectId:$subjectId);
} catch (GitHubFileDoesNotExist | SpecifiedUrlNotADirectory | DirectoryAlreadyExists $e){
    $_SESSION['error'] = $e->getMessage();
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
} catch (Exception $e){
    $_SESSION['error'] = "Error desconegut";
    redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'error' => 1));
    return;
}

$route = $returnedData['route'];
$title = $returnedData['title'];
$description = $returnedData['description'];

$problemId = createProblem(route: $route, title: $title, description:$description, max_memory_usage: $maxMemoryUsage,
    visibility:$visibility, max_execution_time: $maxExecutionTime, language: $language, subject: $subjectId);

redirectLocation(query: VIEW_PROBLEMS_LIST, params: array('subject' => $subjectId, 'created' => $problemId));
