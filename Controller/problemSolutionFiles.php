<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/session.php";
include_once __DIR__ . "/../Model/dockerUtils.php";
include_once __DIR__ . "/../Model/diskManager.php";
include_once __DIR__ . "/../Model/problemsGet.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/online_visualization.php";
include_once __DIR__ . "/../Model/Messages.php";


$problem_id = $_GET["problem"];
$problem = getProblemWithId($problem_id);
$session_id = null;

$subject = $problem["subject_id"];
$solution_problem_route = $problem["route_solution"];

$folder_route = ($_SESSION['user_type'] == PROFESSOR && isset($_GET["edit"]))?
    $solution_problem_route: $solution_problem_route;

$visibility = getProblemSolutionVisibility($problem_id); /*TODO CHECK*/

include_once __DIR__ . "/../View/html/problemSolutionFiles.php";