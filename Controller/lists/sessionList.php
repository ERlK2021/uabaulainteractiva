<?php
require_once __DIR__ . '/../../Model/constants.php';
require_once __DIR__ . '/../../Model/redirectionUtils.php';
require_once __DIR__ . "/../../Model/connection.php";
require_once __DIR__ . "/../../Model/session.php";

$subjectId = $_GET['subject'];
$group = $_GET['group'];
$sessions = getActiveSessionsFromGroup(subjectId: $subjectId, class_group: $group);

$listPage['title'] = 'Sessions disponibles';
$listPage['customJS'] = 'session.js';

// Classify the items and create a list for each element of the list
foreach ($sessions as $session) {
    $sessionId = $session['id'];


    $item = array('id' => $sessionId,
        'href' => buildUrl(VIEW_SESSION_PROBLEMS_LIST, array('session' => $sessionId)),
        'title' => $session['name']);

    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == STUDENT){
        $editStatus = $session['status'] == 'deactivated' ? 'no-edit' : 'edit';
        $item['buttons'][] = array('image' => $editStatus, 'alt' => $editStatus);
    }
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) {

        $visibilityImage = $session['status'] == 'deactivated' ? 'deactivated' : 'activated';
        $item['buttons'][] = array('type' => 'js', 'classes' => 'change_visibility', 'title' => 'Canviar visibilitat',
            'image' => $visibilityImage, 'alt' => 'Canviar visibilitat');

        $item['buttons'][] = array('type' => 'modalToggle', 'title' => 'Duplicar',
            'target' => 'duplicate_session_modal', 'image' => 'clone', 'alt' => 'Duplicar Sessió');
        $item['buttons'][] = array('type' => 'js', 'title' => 'Esborrar', 'onClick' => "deleteSession($sessionId)",
            'image' => 'trash', 'alt' => 'Esborrar Sessió');

    }
    $listPage['items'][] = $item;
}

$listPage['modals'] = [
    array('id' => 'duplicate_session_modal', 'title' => 'Duplicar Sessió',
        'field' => array('type' => 'text', 'id' => 'new_session_name', 'placeholder' => 'Nom de la sessió',
            'required' => 'required'),
        'buttonTitle' => 'Duplicar', 'buttonOnClick' => 'duplicateSession()', 'buttonText' => 'Duplicar')
];

require_once __DIR__ . "/../../View/html/genericList.php";