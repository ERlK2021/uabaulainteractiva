<?php
include_once __DIR__ . "/../../Model/constants.php";
include_once __DIR__ . "/../../Model/redirectionUtils.php";
include_once __DIR__ . "/../../Model/connection.php";
include_once __DIR__ . "/../../Model/problemsGet.php";

$subjectId = $_GET['subject'];
$problems = getProblemsWithSubject($subjectId);
$selectorOptions = [];
foreach ($problems as $problem) {
    $selectorOptions[] = array('id' => $problem['id'], 'title' => $problem['title']);
}

$formPage['title'] = 'Crear nova sessió';
if (empty($selectorOptions)) {
    $formPage['subtitle'] = "Per crear una sessió primer necessites crear problemes per l'assignatura";
    $formPage['emptyOptionsHref'] = buildUrl(VIEW_PROBLEM_CREATE, array('subject' => $subjectId));
} else {
    $formPage['subtitle'] = "Crea una nova sessió amb els problemes de l'assignatura";
    $formPage['customJS'] = array('external/bootstrap-multiselect.js', 'multiselect.js');
    $formPage['validationJS'] = 'session.js';
    $formPage['action'] = '/Controller/sessionNew.php';
    $formPage['onSubmit'] = 'validateSession()';
    $formPage['title'] = 'Crear nova sessió';
    $formPage['fields'] = [
        array('type' => 'text', 'id' => 'name', 'placeholder' => 'Nom', 'required' => 'required'),
        array('type'=>'int','id' => 'class_group', 'placeholder' => 'Grups de classe', 'required' => 'required'),
        array('type' => 'selector', 'id' => 'multiple-checkboxes', 'name' => 'problems[]', 'placeholder' => 'Problemes',
            'options' => $selectorOptions, 'multiple' => 'multiple', 'required' => 'required')
    ];

    $formPage['info']=[
        array('image'=>'icon-info-sign', 'span'=>'A little column extra info')
    ];
    $formPage['submitText'] = 'Crear sessió';
}

require_once __DIR__ . '/../../View/html/genericForm.php';
