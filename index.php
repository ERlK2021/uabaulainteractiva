<?php
session_start();
include_once __DIR__ . "/Model/constants.php";
include_once __DIR__ . "/Model/redirectionUtils.php";
require_once __DIR__ . "/Controller/breadcrumb.php";

error_reporting(E_ERROR | E_PARSE);

$query = $_GET["query"] ?? VIEW_SUBJECT_LIST;


if (isset($_SESSION["email"])) {
    # If the user is a Student the views are restricted
    if ($_SESSION['user_type'] == STUDENT && !in_array($query, STUDENT_VIEWS)) {
        header("Location:/index.php");
    }
} else {
    # If the user is anonymous he can only log in or register
    if (!in_array($query, ANONYMOUS_USER_VIEWS)) {
        header("Location:/index.php?error=1");
    }
}

switch ($query) {
    case VIEW_PROBLEMS_LIST: //1
        include __DIR__ . "/Controller/lists/problemList.php";
        break;
    case VIEW_LOGIN_FORM: //2
        include __DIR__ . "/Controller/forms/loginForm.php";
        break;
    case VIEW_REGISTER_FORM: //3
        include __DIR__ . "/Controller/forms/registerForm.php";
        break;
    case VIEW_PROBLEM_CREATE: //4
        include __DIR__ . "/Controller/forms/problemNewDiskForm.php";
        break;
    case VIEW_EDITOR: //7
        include __DIR__ . "/Controller/editor.php";  // TODO
        break;
    case VIEW_SUBJECT_CREATE: //10
        include __DIR__ . "/Controller/forms/newSubjectForm.php";
        break;
    case VIEW_SOMETHING: //11
        include __DIR__ . "/Model/zipFolder.php";
        break;
    case VIEW_PROBLEM_EDIT: //12
        include __DIR__ . "/Controller/forms/problemEditForm.php";
        break;
    case VIEW_SESSION_FORM: //13
        include __DIR__ . "/Controller/forms/sessionCreationForm.php";
        break;
    case VIEW_SESSION_LIST_GROUPS: //19
        include __DIR__ . "/Controller/lists/sessionGroups.php";
        break;
    case VIEW_SESSION_LIST: //14
        include __DIR__ . "/Controller/lists/sessionList.php";
        break;
    case VIEW_SESSION_PROBLEMS_LIST: //15
        include __DIR__ . "/Controller/lists/sessionProblemsList.php";
        break;
    case VIEW_PROBLEM_CREATE_GIT: //16
        include __DIR__ . "/Controller/forms/problemNewGithubForm.php";
        break;
    case VIEW_PROBLEM_SOLUTION:/*SOLUCION*/
        include __DIR__ . "/Controller/problemSolutionFiles.php";
        break;
    case VIEW_PROBLEM_SOLUTION_UPLOAD:
        include __DIR__ . "/Controller/forms/problemSolutionUploadForm.php";
        break;
    case VIEW_MESSAGES_CHAT:
        include __DIR__ . "/Controller/messages.php";
        break;
    case VIEW_COMUN_MESSAGES:
        include __DIR__ . "/Controller/messagesComun.php";
        break;
    case CREATE_CSV:
        include __DIR__ . "/Controller/createCSV_Entregables.php";
        break;
    case SESSIONS_TO_EDIT:
        include __DIR__ . "/Controller/lists/sessionsToEdit.php";
        break;
    case EDIT_SESSION:
        include __DIR__ . "/Controller/forms/editSession.php";
        break;
    default: //0
        include __DIR__ . "/Controller/lists/subjectList.php";
        break;
}