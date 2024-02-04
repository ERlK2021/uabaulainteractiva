<?php
require_once __DIR__ . '/../../Model/connection.php';
require_once __DIR__ . '/../../Model/session.php';
require_once __DIR__ . "/../../Model/problemsGet.php";

$formPage = [
    'action' => '/Controller/sessionUpdate.php',
    'submitText' => 'Editar',
    'title' => "Editar sessión",
    'customJS' => ['external/bootstrap-multiselect.js', 'multiselect.js'],
    'validationJS' => 'session.js',
    'subtitle' => "Puedes editar el nombre de la sesión, los grupos que tendrán acceso y agregarle problemas."
];

$sessionName = $_GET['sessio'];
$sessionGroups =  getSessionGroups($sessionName);

$problems_session = getProblemsWithSession(getSessionId($sessionName));
$problems_session_string = implode(', ', array_column($problems_session, 'title'));//$problems_session_string es un array de strings que contiene los títulos de los problemas de la sesión, separados por comas.

//Fill selectorOptions with only the left problems of the subject (the ones that are not currenty being useid in the edited session)
$problems_subject = getProblemsWithSubject($_GET['subject']);
$selectorOptions = [];
foreach ($problems_subject as $problem_subject) {

    $problem_in_session = false;
    foreach($problems_session as $problem_session) {

        if($problem_session['title'] == $problem_subject['title']){ $problem_in_session = true; }
    }
    if(!$problem_in_session){
        $selectorOptions[] = array('id' => $problem_subject['id'], 'title' => $problem_subject['title']);
    }
}

$formPage['fields'][0] = array('type' => 'textarea', 'id' => 'name', 'required' => 'required', 'rows' => 1, 'value' => $sessionName); //Session Name
foreach ($sessionGroups as $group){
    $item['buttons'][] = array('type' => 'js', 'classes' => 'group',  'value'=> $group); //Groups that share the session
}
$item['buttons'][] = array('type' => 'js', 'classes' => 'add_group', 'value'=> '+', ); //Add new group to session
$formPage['fields'][1] = array('type' => 'selector', 'id' => 'multiple-checkboxes', 'name' => 'problems[]', 'placeholder' => 'Problemes',
    'options' => $selectorOptions, 'multiple' => 'multiple'); //Add new problem to session

require_once __DIR__ . '/../../View/html/genericForm.php';
