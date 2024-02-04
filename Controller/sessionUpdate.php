<?php
session_start();
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/session.php";
include_once __DIR__ . "/../Model/professor.php";
require_once __DIR__ . "/../Model/problemsGet.php";

$oldSessionName= $_POST['old_session_name'];
$newSessionName = $_POST['name'];//$_POST['session_name'];
$newProblemIds = $_POST['problems'];
$groups = $_POST['input_new_group'];

if( $newProblemIds != null ){
    $newProblemIds = array_map('intval', $newProblemIds);
}
$currentGroups = getSessionGroups($oldSessionName);

$sessionId = getSessionId($oldSessionName);//??mal en realidad
$currentProblems = array_column(getProblemsWithSession($sessionId),'id'); //array(1) { [0]=> array(2) { ["id"]=> int(279) ["title"]=> string(9) "Racionals" }
$subjectId = getSessionSubject($oldSessionName);

if(!is_null($groups)) {
    foreach ($groups as $group) {
        if (in_array($group, $currentGroups)) { //Grupo ya existe

            if($oldSessionName != $newSessionName || !is_null($newProblemIds)){//Entra 41 y 42

                $sessionId = getSessionIdPlus($oldSessionName, $group);
                updateSession($sessionId, $newSessionName, $newProblemIds);

            }

        } else {   //Grupo no existe
            $problemsIds = ($newProblemIds == null)? $currentProblems :  array_merge($currentProblems, $newProblemIds);
            $professor = getProfessor($_SESSION['email']);
            createSession($newSessionName, $professor['id'], $subjectId, $problemsIds, $group);//Return Id of the new created session.
        }
    }
}
foreach ($currentGroups as $currentGroup) {
    if (!is_null($groups)) {
        if (!in_array($currentGroup, $groups)) { //User has deselected this group, wants it remove from the session

            $sessionId = getSessionIdPlus($oldSessionName, $currentGroup);
            deleteSession($sessionId);
        }
    }
    else{
        $sessionId = getSessionIdPlus($oldSessionName, $currentGroup);
        deleteSession($sessionId);
    }
}
redirectLocation();//home