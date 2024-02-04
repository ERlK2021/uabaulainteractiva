<?php
require_once __DIR__ . "/../../Model/constants.php";
require_once __DIR__ . "/../../Model/redirectionUtils.php";
require_once __DIR__ . "/../../Model/connection.php";
require_once __DIR__ . "/../../Model/problemsGet.php";

$subjects = getSubjects();

// Build the page hashmap
$listPage['title'] = "Assignatures";
$listPage['customJS'] = "subjectsList.js";

if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) {
    $listPage['headerButtons'] = [
        array('href' => buildUrl(VIEW_SUBJECT_CREATE), 'classes' => 'add-object', 'img' => 'subject',
            'alt' => 'Afegir assignatura'),
    ];
}

if (isset($_GET['error'])) {
    $listPage['errorMessage'] = "Inicia sessió o registra't";
} else if (isset($_GET['deleted'])) {
    $listPage['infoMessage'] = "L'assignatura s'ha esborrat";
} else if (isset($_GET['logged'])) {
    $listPage['infoMessage'] = "Sessió iniciada";
} else if (isset($_GET['registered'])) {
    $listPage['infoMessage'] = "Compte creat";
}

// Classify the items and create a list for each element of the list
$classifiedSubjects = [];
foreach ($subjects as $subject) {
    $subjectId = $subject['id'];
    $subjectTitle = $subject['title'];

    if (isset($_GET['created']) && $_GET['created'] == $subjectId) {
        $listPage['infoMessage'] = "Assignatura '$subjectTitle' creada.";
    } else if (isset($_GET['not_deleted']) && $_GET['not_deleted'] == $subjectId) {
        $listPage['errorMessage'] = "L'assignatura '$subjectTitle' no s'ha esborrat";
    }

    $groupItem = array('id' => $subjectId, 'title' => $subjectTitle, 'description' => $subject['description'],
        'buttons'=> [
            array('type' => 'a', 'href' => buildUrl(VIEW_PROBLEMS_LIST, array('subject'=>$subjectId)),
                'classes' => '', 'image' => 'problem', 'alt' => 'Problemes')
        ]);
    if ($subject['has_active_sessions']) {
        $groupItem['buttons'][] = array('type' => 'a',
            'href' => buildUrl(VIEW_SESSION_LIST_GROUPS, array('subject'=>$subjectId)),
            'classes' => '', 'image' => 'session', 'alt' => 'Sessions actives');
    }
    if (isset($_SESSION['user_type']) && $_SESSION['user_type'] == PROFESSOR) {
        $groupItem['buttons'][] = array('type' => 'a',
            'href' => buildUrl(VIEW_SESSION_FORM, array('subject'=>$subjectId)),
            'classes' => 'add-object', 'image' => 'session', 'alt' => 'Crear sessió');

        if ($subject['has_active_sessions']) {
            $groupItem['buttons'][] = array('type' => 'a',
                'href' => buildUrl(SESSIONS_TO_EDIT, array('subject'=>$subjectId)),
                'classes' => '', 'image' => 'editSession', 'alt' => 'Editar sessions');}

        $groupItem['buttons'][] = array('type' => 'modalToggle', 'title' => 'Esborrar',
            'target' => 'delete_subject_modal', 'image' => 'trash', 'alt' => 'Esborrar assignatura');
    }

    // Save only the course number to be able to sort them
    $groupKey = $subject['course'];
    $classifiedSubjects[$groupKey][] = $groupItem;
}

ksort($classifiedSubjects);
// Add 'Curs' in front of the keys
$keys = array_keys($classifiedSubjects);
//Map keys to format function
$keys = array_map(function($key) { return "Curs $key"; }, $keys);
//Use array_combine to map formatted keys to array values
$classifiedSubjects = array_combine($keys, $classifiedSubjects);

$listPage['groups'] = $classifiedSubjects;

$listPage['modals'] = [
    array('id' => 'delete_subject_modal', 'title' => "Estàs segur?",
        'content'=> "L'operació serà immediata i sense possibilitat de retorn.",
        'buttonTitle' => 'Esborrar', 'buttonOnClick' => 'deleteSubject()', 'buttonText' => 'Esborrar',
        'dismissButtonText' => 'Cancel·lar')
];

require_once __DIR__ . "/../../View/html/genericList.php";