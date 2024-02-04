<?php
require_once __DIR__ . '/../../Model/constants.php';

if (isset($_GET['error'])) {
    $formPage['error'] = "L'email o la contrasenya no són correctes";
}

$formPage['validationJS'] = 'userManagement.js';
$formPage['action'] = '/Controller/login.php';
$formPage['onSubmit'] = 'validateLogin()';
$formPage['title'] = 'Iniciar sessió';
$formPage['subtitle'] = "Inicia sessió si tens compte, si no, registra't!";
$formPage['fields'] = [
    array('type' => 'email', 'id' => 'email', 'placeholder' => 'Email', 'required' => 'required'),
    array('type' => 'password', 'id' => 'password', 'placeholder' => 'Contrasenya', 'required' => 'required',
        'minlength' => 8, 'maxlength' => 24)
];
$formPage['submitText'] = 'Iniciar sessió';
$formPage['extraOptions'] = [
    array('href' => '/index.php?query='.VIEW_REGISTER_FORM, 'optionText' => "Registre")
];

require_once __DIR__ . '/../../View/html/genericForm.php';
