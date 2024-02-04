<?php
require_once __DIR__ . '/../../Model/constants.php';
require_once __DIR__ . '/../../Model/redirectionUtils.php';
require_once __DIR__ . "/../../Model/connection.php";
require_once __DIR__ . "/../../Model/session.php";

$subjectId = $_GET['subject'];
$groups = getGroupsActiveSessions(subjectId: $subjectId);

$listPage['title'] = 'Grups amb sessions actives';
$listPage['customJS'] = 'session.js';

// Classify the items and create a list for each element of the list
foreach ($groups as $group) {


    $item = array('id' => $group,
        'href' => buildUrl(VIEW_SESSION_LIST, array('subject'=>$subjectId, 'group'=>$group)),
        'title' => $group);
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) {

        /*$item['buttons'][] = array('type' => 'modalToggle', 'title' => 'Esborrar',
            'target' => 'borrar_grup_sessions', 'image' => 'trash', 'alt' => 'Esborrar Grup');*/
        $item['buttons'][] = array('type' => 'js', 'title' => 'Esborrar', 'onClick' => "deleteGroupSessions($group)",
            'image' => 'trash', 'alt' => 'Esborrar Grup');
    }
    $listPage['items'][] = $item;
}
/*$listPage['modals'] = [
    array('id' => 'borrar_grup_sessions', 'title' => 'Esborrar',
        'field' => array('type' => 'text', 'id' => 'class_group_delete', 'placeholder' => '???',
            'required' => 'required'),
        'buttonTitle' => 'Esborrar', 'buttonOnClick' => 'deleteGroupSessions_2()', 'buttonText' => 'Esborrar', 'dismissButtonText' => 'Cancel·lar')
];
*/
require_once __DIR__ . "/../../View/html/genericList.php";
