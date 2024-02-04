<?php
session_start();
include_once __DIR__ . "/connection.php";

function remove_dir($route, $problem_title, $subject_id) {
    $dir = opendir($route);
    while (false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            $full = $route . '/' . $file;
            if (is_dir($full)) {
                remove_dir($full);
            } else {
                unlink($full);
            }
        }
    }
    closedir($dir);
    rmdir($route);


    $solutions_base = "./../app/solucions";
    $solutions_base_dir = opendir($solutions_base);

    while (false !== ($emails = readdir($solutions_base_dir))) {
        if (($emails != '.') && ($emails != '..')) {

            $solution_mail_subject_pTitle = $solutions_base . '/' . $emails . '/'. $subject_id . '/' . $problem_title;
            print_r($solution_mail_subject_pTitle);
            echo "<br>";

            if(is_dir($solution_mail_subject_pTitle)) { //True if the filename exists and is directory

                $dir = opendir($solution_mail_subject_pTitle);
                while (false !== ($file = readdir($dir))) {
                    if (($file != '.') && ($file != '..')) {
                        $full = $solution_mail_subject_pTitle . '/' . $file;
                        if (is_dir($full)) {
                            remove_dir($full);
                        } else {
                            unlink($full);
                        }
                    }
                }
                closedir($dir);
                rmdir($solution_mail_subject_pTitle);
            }


        }
    }

}

$connection = connectDB();
$problem_id = $_POST['id'];
try {

    $statement = $connection->prepare("SELECT * FROM problem WHERE id= :problem_id");
    $statement->execute(array(":problem_id" => $problem_id));
    $problem = $statement->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo 'Error deleting the problem ' . $e->getMessage();
}

try {

    $statement = $connection->prepare('DELETE FROM session_problems WHERE problem_id = :problem_id');
    $statement->execute(array(":problem_id" => $problem_id));
} catch (PDOException $e) {
    echo 'Error deleting the problem ' . $e->getMessage();
}
try {

    $statement = $connection->prepare('DELETE FROM problem WHERE id = :problem_id');
    $statement->execute(array(":problem_id" => $problem_id));
    $connection = null;
} catch (PDOException $e) {
    echo 'Error deleting the problem ' . $e->getMessage();
}

$route = $problem['route'];
if (is_dir($route)) {
    remove_dir($route, $problem['title'], $problem['subject_id']);
}
