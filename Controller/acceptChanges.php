<?php
session_start();
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/problemsGet.php";
include_once __DIR__ . "/../Model/diskManager.php";

$email = $_SESSION['email'];
$problemId = $_POST["id"];
$problem = getProblemWithId($problemId);

$subjectId = $problem['subject_id'];
$solutionRoute = str_replace('\\', '/', realpath(__DIR__ . "/../app/solucions/$email/$subjectId/".$problem["title"]));//$solutionRoute = str_replace('\\', '/',
//realpath(__DIR__ . "./../app/solucions/$email/$subjectId/".$problem["title"]));

$problemRoute = str_replace('\\', '/', realpath(__DIR__ .trim("/".$problem["route"])));//$problemRoute = str_replace('\\', '/', realpath(__DIR__ . $problem["route"]));

$files = getDirectoryFiles($problemRoute);
foreach($files as $file) {
    $origin = "$problemRoute/$file";
    echo "Origen: " . $origin . "\n";
    $destination = "$solutionRoute/$file";
    echo "Destination: " . $destination . "\n";
    echo var_dump(copy($origin, $destination));
    echo "\n";
}

unsetSolutionEdited($problemId, $email);
