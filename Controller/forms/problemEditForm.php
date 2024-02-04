<?php
require_once __DIR__ . '/problemForm.php';
require_once __DIR__ . '/../../Model/connection.php';
require_once __DIR__ . '/../../Model/problemsGet.php';

$formPage['action'] = '/Controller/problemUpdate.php';
$formPage['onSubmit'] = 'validateProblem()';
$formPage['title'] = "Editar problema";
$formPage['subtitle'] = "Pots modificar totes les dades del problema menys el títol";
$formPage['submitText'] = 'Editar';

// Remove the visibility and language fields
unset($formPage['fields'][2]);

// Add the description field
$descriptionField = array('type' => 'textarea', 'id' => 'description', 'placeholder' => 'Descripció del problema',
    'required' => 'required', 'rows' => 3);
array_unshift($formPage['fields'], $descriptionField);

$problem = getProblemWithId(intval($_GET['problem']));
$formValues = array($problem['description'], $problem['time'], $problem['memory'], $problem['language'], $problem['entregable']);

// Set all the field values
$formPage['fields'][0]['value'] = $formValues[0];
$formPage['fields'][1]['value'] = $formValues[1];
$formPage['fields'][2]['value'] = $formValues[2];
$formPage['fields'][3]['value'] = $formValues[3];
$formPage['fields'][4]['value'] = $formValues[4];
require_once __DIR__ . '/../../View/html/genericForm.php';