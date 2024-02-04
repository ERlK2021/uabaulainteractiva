<?php
session_start();
include_once __DIR__ . "/var/www/html/Model/connection.php";
include_once __DIR__ . "/var/www/html/Model/problemsGet.php";

$filename = $_POST['filename'];
$edited = $_POST['edited'];
$problem = $_POST['problema'];
$dir = str_replace('\\', '/', realpath(urldecode($_POST['dir'])));

echo $dir;

if ($edited == 1) {
    setSolutionAsEdited($problem);
}

$dst = $dir . '/' . $filename;
//Check if it should be a folder or file
echo $filename;
if (strpos($filename, '.') == 1) {
    if (touch($dst)) {
        echo true;
    } else {
        echo false;
    }
}

echo false;