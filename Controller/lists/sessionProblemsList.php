<?php
session_start();
include_once __DIR__ . "/../../Model/constants.php";
include_once __DIR__ . "/../../Model/connection.php";
include_once __DIR__ . "/../../Model/problemsGet.php";
include_once __DIR__ . "/../../Model/session.php";

$sessionId = $_GET['session'];
$problems = getProblemsWithSession($sessionId);

$userType = $_SESSION['user_type'];
if ($userType == STUDENT) {
    $email = $_SESSION['email'];
    addStudentToSession(sessionId: $sessionId, email:$email);
}

$listPage['title'] = 'Problemes de la sessió';
$listPage['customJS'] = 'problemsList.js';

// Classify the items and create a list for each element of the list
foreach ($problems as $problem) {
    $problemId = $problem['id'];
    $item = array('id' => $problemId,
        'href' => buildUrl(VIEW_EDITOR, array('session' => $sessionId, 'problem' => $problemId)),
        'title' => $problem['title']);
    $listPage['items'][] = $item;
}

require_once __DIR__ . "/../../View/html/genericList.php";
