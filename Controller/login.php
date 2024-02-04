<?php
session_start();
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/login.php";

$email = $_POST['email'];
$password = $_POST['password'];

$loggedIn = logInStudent($email, $password);

if (!$loggedIn) {
    $loggedIn = logInProfessor($email, $password);
    if (!$loggedIn) {
        redirectLocation(VIEW_LOGIN_FORM, array('error' => '1'));
        return;
    }
}

redirectLocation(); # redirectLocation(params:array('logged' => 1))
