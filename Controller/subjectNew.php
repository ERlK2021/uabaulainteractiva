<?php
session_start();
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/subject.php";

$title = $_POST["title"];
$description = $_POST["description"];
$course = $_POST["course"];

$subjectId = createSubject($title, $description, $course);
if ($subjectId == -1) {
    $_SESSION['error'] = "Assignatura '$title' no creada";
    redirectLocation(params: array('error' => 1));
}

redirectLocation(params: array('created' => $subjectId));
