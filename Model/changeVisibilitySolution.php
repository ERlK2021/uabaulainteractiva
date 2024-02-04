<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/problemNew.php";

$problemId = $_POST['problemId'];
$new_visibility = $_POST['newVisibility'];
changeSolutionVisibility($problemId, $new_visibility);