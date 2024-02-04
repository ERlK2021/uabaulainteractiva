<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/subject.php";

$subjectId = $_POST['id'];

$deleted = deleteSubject(subjectId: $subjectId);

echo $deleted? $subjectId: -1;