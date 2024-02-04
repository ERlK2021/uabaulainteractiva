<?php
require_once __DIR__ . '/problemForm.php';

$formPage['action'] = '/Controller/githubProblemNew.php';
$formPage['onSubmit'] = 'validateProblem()';
$formPage['subtitle'] = "Crea un problema indicant un link a una carpeta de GitHub";

// Insert the url field at the beginning
$urlField = array('type' => 'url', 'id' => 'repo_link', 'placeholder' => 'Url del repositori',
    'required' => 'required');
array_unshift($formPage['fields'], $urlField);

require_once __DIR__ . '/../../View/html/genericForm.php';