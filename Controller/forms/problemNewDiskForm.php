<?php
require_once __DIR__ . '/problemForm.php';

$formPage['action'] = '/Controller/problemNew.php';
$formPage['onSubmit'] = 'validateProblemAndFiles()';
$formPage['subtitle'] = "Crea un problema indicant els fitxers des de la teva màquina";

// Insert the title and description fields at the start of the form
$titleField = array('type' => 'text', 'id' => 'title', 'placeholder' => 'Títol del problema', 'required' => 'required');
$descriptionField = array('type' => 'textarea', 'id' => 'description', 'placeholder' => 'Descripció del problema',
    'required' => 'required', 'rows' => 3);
array_unshift($formPage['fields'], $titleField, $descriptionField);

// Insert the file field after all the fields
$formPage['fields'][] = array('type' => 'file', 'id' => 'file', 'placeholder' => 'Selecciona fitxers',
    'required' => 'required');

require_once __DIR__ . '/../../View/html/genericForm.php';
