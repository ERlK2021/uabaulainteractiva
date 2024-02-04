<?php

//ERRORES PARA DEBUGGEAR WARNINGS
//error_reporting(E_ALL);
//ini_set('display_errors', '1');

include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/redirectionUtils.php";
include_once __DIR__ . "/../Model/session.php";
include_once __DIR__ . "/../Model/dockerUtils.php";
include_once __DIR__ . "/../Model/diskManager.php";
include_once __DIR__ . "/../Model/problemsGet.php";
include_once __DIR__ . "/../Model/constants.php";
include_once __DIR__ . "/../Model/online_visualization.php";
//include_once __DIR__ . "/../Model/Messages.php";
include_once __DIR__ . "/../Model/student.php";
include_once __DIR__ . "/../Model/entregable_problem.php";

//Si es la primera vez que ejecutamos la ventana, tenemos que settear user_type y email
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_GET['rol'])) {
    if ($_GET['rol'] <= 4) {
        $_SESSION['user_type'] = PROFESSOR;
    } else {
        $_SESSION['user_type'] = STUDENT;
    }
    $_SESSION['email'] = $_GET['email'];
}


# We get the problem data
$problem_id = $_GET["problem"];
$problem = getProblemWithId($problem_id);

// Get the session id if it's set
$session_id = null;
if (isset($_GET["session"])) {$session_id = $_GET["session"]; }

// Get the group id if it's set
//if(!isset($_SESSION['cursID']) or $_SESSION!=$_GET["curs"]) {$_SESSION['cursID'] = $_GET["curs"];}
if((!isset($_SESSION['cursID']) or $_SESSION!=$_GET["curs"]) and isset($_GET["curs"])) {$_SESSION['cursID'] = $_GET["curs"];}

# The email will be the user's, unless the user is a professor spectating a student
$email = $_SESSION["email"];


if (isset($_GET["view-mode"]) && isset($_GET["user"])) {
    # If the view_mode doesn't exist redirect to the homepage
    $view_mode = $_GET["view-mode"];
    if (!in_array($view_mode, [VIEW_MODE_EDIT, VIEW_MODE_READ_ONLY])) {
        redirectLocation();
    }

    $email = $_GET["user"];

    if ($view_mode == VIEW_MODE_EDIT) {
        $viewUserId = getUserID($_GET["user"]);
        setSolutionAsEditing(problem_id: $problem_id, student_id: $viewUserId, editing_before: 0, editing_after: 1);
    }
    else {
        $viewUserId = getUserID($_GET["user"]);
        setSolutionAsEditing(problem_id: $problem_id, student_id: $viewUserId, editing_before: 1, editing_after: 0);
    }

}

/* PARTE DE JUPYTER NOTEBOOK SIN IMPLEMENTAR
// Start a new jupyter container for the user if it's needed
if ($problem['language'] == 'Notebook') {
    if (isset($_SESSION['containerId'])) {
        $_SESSION['containerUsages'] += 1;
    } else {
        $containerData = runJupyterDocker($_SESSION['email']);
        $_SESSION['containerId'] = $containerData['containerId'];
        $_SESSION['containerPort'] = $containerData['containerPort'];
        $_SESSION['containerUsages'] = 1;
    }
}*/

# Get the problem files from the machine
$subject = $problem["subject_id"];
$problem_route = $problem["route"];
$cleaned_problem_route = $problem_route;

# Create the folder for the user if it doesn't already exist
$user_route = "/../app/solucions/$email";
if (!file_exists(__DIR__ . $user_route) && !mkdir(__DIR__ . $user_route)) {
    echo 'Failed to create folder';
}

$user_subject_route = "$user_route/$subject";
if (!file_exists(__DIR__ . $user_subject_route) && !mkdir(__DIR__ . $user_subject_route)) {
    echo 'Failed to create folder';
}

# Create the folder of the problem if it doesn't already exist
$problem_title = trim($problem["title"]);//$problem_title = $problem["title"];
$user_solution_route = "$user_subject_route/$problem_title";

if (!file_exists(__DIR__ . $user_solution_route)) {
    if (!mkdir(__DIR__ . $user_solution_route, 0777, true)) {
        $error = error_get_last();
        echo $error['message'];
        echo "<br>";
        echo 'Failed to create folder';
        return;
    }
    $cleaned_user_solution_route = str_replace('\\', '/', realpath(__DIR__ . $user_solution_route));
    # Create the files of the problem if the folder was created right now
    $problem_files = getDirectoryFiles($cleaned_problem_route);

    foreach ($problem_files as $file) {
        $origin = $cleaned_problem_route . '/' . $file;
        $destination = $cleaned_user_solution_route . '/' . $file;

        if(!copy($origin, $destination)){
            echo "COPY FALLA"; echo "<br>";
            $error = error_get_last();
            echo $error['message'];
        }
    }

    if ($_SESSION['user_type'] == STUDENT) {

        $created = createSolution($cleaned_user_solution_route, $problem_id, $subject, $_GET['userMoodleID']);
        if (!$created) {
            echo "Error creating the solution";
            return;
        }

        //We get the value of the solution
        if($_GET['userMoodleID']){$_SESSION['userMoodleID']=$_GET['userMoodleID'];}
        $solutionID=getSolution($problem_id,$_SESSION['userMoodleID'])['id'];

        //And then we create the relation between group ID and solution
        $created = createSolutionGroupRelation($solutionID, $_SESSION['cursID']);
        if (!$created) {
            echo "Error creating the relation";
            return;
        }

    }
}

//We get the value of the solution
if($_GET['userMoodleID']){$_SESSION['userMoodleID']=$_GET['userMoodleID'];}
$solutionID=getSolution($problem_id,$_SESSION['userMoodleID'])['id'];

//And here we update the relation in order to match the group
updateSolutionGroupRelation($solutionID, $_SESSION['cursID']);

$teacher_solution_visibility = getProblemSolutionVisibility($problem_id);

$cleaned_user_solution_route = str_replace('\\', '/', realpath(__DIR__ . $user_solution_route));


// If the professor is editing the root, set the route as the problem route
$folder_route = ($_SESSION['user_type'] == PROFESSOR && isset($_GET["edit"]))?
    $cleaned_problem_route: $cleaned_user_solution_route;

//******  Entregable *****


$entregable = getIfProblemIsEntregable($problem_id);


$deadline = null;

$currentDate = date("Y-m-d");

if($entregable && $_SESSION['user_type'] == STUDENT){
    $grade = getEntregableGrade($_SESSION["email"], $problem_id);
    $deadline = null;
    //$deadline = getEntregableDeadline($problem_id); TODO para el proximo TFG, se ha de integrar el deadline de moodle
}

//**********


if ($_SESSION['user_type'] == PROFESSOR && !is_null($session_id)) {

    $students = getStudentsWithSessionAndProblem(session_id: $session_id, problem_id: $problem_id);

    if(count($students) > 0){
        $students_solution_data = getStudentsSessionExtraData($session_id, $problem_id); //student_email, output, executed_times_count, teacher_executed_times_count, number_lines_file, solution_quality

        //TESTAR PARA LOS CASOS  EN QUE EL PROBLEMA NO TENGA SOLUCION SUBIDA POR EL PROFESOR.
        $extraData = getProblemExtraData($problem_id);
        $official_solution_quality = $extraData['solution_quality'];
        $official_solution_lines = $extraData['number_lines_file'];

        $_students = array();

        foreach ($students as $student){
            $studentGroup = getRelation($student['id'])["grup"];

            if($studentGroup!=$_SESSION["cursID"]){
                continue; //SKIP STUDENTS FROM ANOTHER GROUP
            }

            $appears = false;
            foreach ($students_solution_data as $student_solution_data){

                if($student['user'] == $student_solution_data['student_email']){

                    $appears=true;
                    $aux['user'] = $student['user'];
                    $aux['executed_times_count'] = $student_solution_data['executed_times_count'];
                    $aux['teacher_executed_times_count'] = $student_solution_data['teacher_executed_times_count'];

                    if ($official_solution_lines != 0){
                        $student_lines_percentage = intval((intval($student_solution_data['number_lines_file']) * 100) / $official_solution_lines);
                    }

                    $aux['lines_percentage'] = $student_lines_percentage;
                    $aux['number_lines_file'] = $student_solution_data['number_lines_file'];
                    $aux['solution_quality'] = $student_solution_data['solution_quality'];

                    $aux['output']= $student_solution_data['output'];
                    array_push($_students, $aux);
                }
            }
            if(!$appears){
                $aux['user'] = getUserEmail($student["user_id"])["email"];
                $aux['executed_times_count'] = 0;
                $aux['teacher_executed_times_count'] = 0;
                $aux['lines_percentage'] = 0;
                $aux['number_lines_file'] =0;
                $aux['solution_quality'] ="----";
                $aux['output'] = "";
                array_push($_students, $aux);
            }
        }
    }
}


$solution = getSolution($problem_id, $_SESSION['email']);//Rertorna False en Profesor, si es esrudiante retorna bien.

/*
echo"<hr>";
echo "SESSION: ". var_dump($_SESSION);
echo"<hr>";
echo "GET: ". var_dump($_GET);
echo"<hr>";*/

include_once __DIR__ . "/../View/html/editor.php";



