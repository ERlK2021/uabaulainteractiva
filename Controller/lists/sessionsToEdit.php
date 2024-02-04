<?php
require_once __DIR__ . '/../../Model/constants.php';
require_once __DIR__ . '/../../Model/redirectionUtils.php';
require_once __DIR__ . "/../../Model/connection.php";
require_once __DIR__ . "/../../Model/session.php";

$subjectId = $_GET['subject'];
$listPage['title'] = 'Sessions disponibles';
$listPage['customJS'] = 'session.js';
$sessionsNames = array_unique(array_column(getActiveSessionsFromSubject($subjectId), "name"));

foreach ($sessionsNames as $sessionName) {

    $item = array('id' => $sessionName,
        'href' => buildUrl(EDIT_SESSION, array('subject'=> $subjectId,'sessio' => $sessionName)),
        'title' => $sessionName);

    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) {

        //$visibilityImage = $session['status'] == 'deactivated' ? 'deactivated' : 'activated';

        $item['buttons'][] = array('type' => 'a',
            'href' =>  buildUrl(EDIT_SESSION, array('subject'=> $subjectId,'sessio' => $sessionName)),
            'image' => 'configure', 'alt' => 'Editar problema');
        /*$item['buttons'][] = array('type' => 'js', 'classes' => 'change_visibility', 'title' => 'Canviar visibilitat',
           'image' => $visibilityImage, 'alt' => 'Canviar visibilitat');*/

        $item['buttons'][] = array('type' => 'js', 'title' => 'Esborrar', 'onClick' => "deleteAllSessionsByName", 'parameter' => urlencode($sessionName),
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
