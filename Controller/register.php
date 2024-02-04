<?php
session_start();

include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/register.php";

$name = $_POST["first_name"];
$surname = $_POST["last_name"];
$email = $_POST["email"];
$password = $_POST["password"];
$password2 = $_POST["password_confirmation"];
$hash_password = password_hash($password, PASSWORD_DEFAULT);
$token = $_POST["token"];
$NIU = intval($_POST["NIU"]);

$taken = isEmailTaken($email);
if($taken) {
    $params['error'] = 1;
    if (isset($token)) {
        $params['token'] = $token;
    }
    redirectLocation(query: VIEW_REGISTER_FORM, params: $params);
    return;
}

$student = 1;
if (isset($token)) {
    $token_marked = setTokenUsed($token);
    if (!$token_marked) {
        redirectLocation(query: VIEW_REGISTER_FORM, params: array("error" => 2));
        return;
    }

    $registered = registerProfessor($name, $surname, $email, $hash_password);
} else {
    $registered = registerStudent($name, $surname, $email, $hash_password, $NIU);
}

if (!$registered) {
    redirectLocation(query: VIEW_REGISTER_FORM, params: array("error" => 3));
    return;
}

redirectLocation(params:array('registered' => 1));
