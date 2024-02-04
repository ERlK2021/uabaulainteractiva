<?php
require_once __DIR__ . '/../../Model/constants.php';

$formPage['validationJS'] = 'subjectValidation.js';
$formPage['action'] = '/Controller/subjectNew.php';
$formPage['onSubmit'] = 'validateSubject()';
$formPage['title'] = 'Crear una nova assignatura';
$formPage['subtitle'] = "Crea una nova assignatura indicant el títol, descripció i curs on es realitzarà";
$formPage['fields'] = [
    array('type' => 'text', 'id' => 'title', 'placeholder' => "Títol de l'assignatura", 'required' => 'required'),
    array('type' => 'number', 'id' => 'course', 'placeholder' => 'Curs on es realitzarà', 'min' => 1, 'max' => 10,
        'required' => 'required'),
    array('type' => 'textarea', 'id' => 'description', 'placeholder' => "Descripció de l'assignatura", 'rows' => 3,
        'required' => 'required')
];
$formPage['submitText'] = 'Crear';

require_once __DIR__ . '/../../View/html/genericForm.php';
