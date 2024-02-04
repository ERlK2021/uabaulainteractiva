<?php
include_once __DIR__ . "/../../Model/constants.php";
include_once __DIR__ . "/../../Model/redirectionUtils.php";
include_once __DIR__ . "/../../Model/connection.php";
include_once __DIR__ . "/../../Model/problemsGet.php";

$subjectId = $_GET['subject'];
$problems = getProblemsWithSubject($subjectId);

$listPage['title'] = 'Problemes';
$listPage['customJS'] = 'problemsList.js';
if ($_SESSION['user_type'] == PROFESSOR) {
    $listPage['headerButtons'] = [
        array('href' => buildUrl(VIEW_PROBLEM_CREATE, array('subject' => $subjectId)), 'classes' => 'add-object',
            'img' => 'problem', 'alt' => 'Afegir problema'),
        array('href' => buildUrl(VIEW_PROBLEM_CREATE_GIT, array('subject' => $subjectId)), 'classes' => 'github',
            'img' => 'problem', 'alt' => 'Afegir problema GitHub'),
    ];
}

if (isset($_GET['error'])) {
    $listPage['errorMessage'] = $_SESSION['error'];
    unset($_SESSION['error']);
}

// Classify the items and create a list for each element of the list
foreach ($problems as $problem) {
    // If the problem is hidden and the user is a student skip the problem
    if ($_SESSION['user_type'] === STUDENT && $problem['visibility'] === "private") {
        continue;
    }

    $problemId = $problem['id'];
    $problemTitle = $problem['title'];

    $problem = getProblemWithId($problemId); //NUEVA
    $subject = $problem["subject_id"];
    $problem_route = $problem["route"];

    $cleaned_problem_route = str_replace('\\', '/', realpath(dirname(__DIR__ ). $problem_route));


    if (isset($_GET['updated']) && $problemId === intval($_GET['updated'])) {
        $listPage['infoMessage'] = "Problema '$problemTitle' actualitzat.";
    } else if (isset($_GET['created']) && $problemId === intval($_GET['created'])) {
        $listPage['infoMessage'] = "Problema '$problemTitle' creat.";
    }

    $item = array('id' => $problemId, 'href' => buildUrl(VIEW_EDITOR, array('problem' => $problemId)),
        'title' => $problemTitle);
    if ($_SESSION['user_type'] === PROFESSOR) {
        $item['buttons'][] = array('type' => 'a',
            'href' => buildUrl(VIEW_PROBLEM_EDIT, array('problem' => $problemId)),
            'image' => 'configure', 'alt' => 'Editar problema');
        $item['buttons'][] = array('type' => 'a',
            'href' => buildUrl(VIEW_EDITOR, array('problem' => $problemId, 'edit' => 1)),
            'image' => 'edit-source', 'alt' => 'Editar codi');

        if(getIfProblemIsEntregable($problemId) == "on") {
            $item['buttons'][] = array('type' => 'a',
                'href' => buildUrl(CREATE_CSV, array('problem' => $problemId)),
                'image' => 'CSV', 'alt' => 'Create CSV');
        }
        $visibilityImage = $problem['visibility'] == 'private'? 'not-visible': 'visible';
        $item['buttons'][] = array('type' => 'js', 'classes' => 'change_visibility','title' => 'Canviar visibilitat',
            'image' => $visibilityImage, 'alt' => 'Canviar visibilitat');
        $item['buttons'][] = array('type' => 'modalToggle', 'title' => 'Esborrar', 'target' => 'delete_problem_modal',
            'image' => 'trash', 'alt' => 'Esborrar problema');
    }
    $listPage['items'][] = $item;
}
$listPage['modals'] = [
    array('id' => 'delete_problem_modal', 'title' => "Estàs segur?",
        'content'=> "L'operació serà immediata i sense possibilitat de retorn.",
        'buttonTitle' => 'Esborrar', 'buttonOnClick' => 'deleteProblem()', 'buttonText' => 'Esborrar',
        'dismissButtonText' => 'Cancel·lar'),
];

require_once __DIR__ . "/../../View/html/genericList.php";