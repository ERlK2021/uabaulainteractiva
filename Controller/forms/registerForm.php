<?php
require_once __DIR__ . '/../../Model/constants.php';

$error = $_GET['error'];
if (isset($error)) {
    if ($error == 1) {
        $message = "L'email ja està sent utilitzat";
    } else if ($error == 2) {
        $message = "El token ja ha sigut utilitzat o no existeix";
    } else {
        $message = "Error desconegut";
    }
    $formPage['error'] = $message;
}

$formPage['validationJS'] = 'userManagement.js';
$formPage['action'] = '/Controller/register.php';
$formPage['onSubmit'] = 'validateRegister()';
$formPage['title'] = "Registra't";
$formPage['subtitle'] = "Crea un compte nou. Si ets professor demana a un professor que t'enviï el link referent.";
$formPage['fields'] = [
    array('type' => 'row', 'fields' => [
        array('type' => 'text', 'id' => 'first_name', 'placeholder' => 'Nom', 'required' => 'required'),
        array('type' => 'text', 'id' => 'last_name', 'placeholder' => 'Cognoms', 'required' => 'required'),
    ]),
    array('type' => 'email', 'id' => 'email', 'placeholder' => 'Email', 'required' => 'required'),
    array('type' => 'text', 'id' => 'NIU', 'placeholder' => 'NIU (obligatori alumnes)', 'required' => 'required'),
    array('type' => 'row', 'fields' => [
        array('type' => 'password', 'id' => 'password', 'placeholder' => 'Contrasenya', 'required' => 'required',
            'minlength' => 8, 'maxlength' => 24),
        array('type' => 'password', 'id' => 'password_confirmation', 'placeholder' => 'Confirmació contrasenya',
            'required' => 'required', 'minlength' => 8, 'maxlength' => 24)
    ]),
];
$formPage['submitText'] = 'Registre';
$formPage['extraOptions'] = [
    array('href' => '/index.php?query='.VIEW_LOGIN_FORM, 'optionText' => "Iniciar sessió")
];

require_once __DIR__ . '/../../View/html/genericForm.php';
