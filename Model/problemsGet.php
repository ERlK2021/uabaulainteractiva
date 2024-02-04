<?php




function getUserID($email) #MOODLE DONE
{
    $userID = null;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT id FROM mdl_user WHERE email= :email");
        $statement->execute(array(":email" => $email));
        $userID = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the problem: ' . $e->getMessage();
    }
    return $userID["id"];

}

function setSolutionAsEditing($problem_id, $student_id, $editing_before, $editing_after) : void
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "UPDATE mdl_uab_interactive_solucio SET editing= :editing_after 
            WHERE problem_id= :problem_id and editing = :editing_before and user_id = :user_id"
        );
        $statement->execute(array(":problem_id" => $problem_id, "editing_after"=> $editing_after,
            ":editing_before" => $editing_before, ":user_id" => $student_id));
        $statement->fetch(PDO::FETCH_ASSOC);

        $connection = null;
    } catch (PDOException $e) {
        echo "Couldn't set the solution as being edited: " . $e->getMessage();
    }
}

function setSolutionAsNotEditing($problem_id, $student_id, $editing_before, $editing_after) : void
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "UPDATE mdl_uab_interactive_solucio SET editing= :editing_after 
            WHERE problem_id= :problem_id and editing = :editing_before and user_id = :user_id"
        );
        $statement->execute(array(":problem_id" => $problem_id, "editing_after"=> $editing_after,
            ":editing_before" => $editing_before, ":user_id" => $student_id));
        $statement->fetch(PDO::FETCH_ASSOC);

        $connection = null;
    } catch (PDOException $e) {
        echo "Couldn't set the solution as being edited: " . $e->getMessage();
    }
}

function setSolutionAsEdited($problem_id): bool
{
    $result = False;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE solution SET edited=1 WHERE problem_id= :problem_id");
        $statement->execute(array(":problem_id" => $problem_id));
        $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

        $result = True;
    } catch (PDOException $e) {
        echo 'Error setting the solutions as edited: ' . $e->getMessage();
    }
    return $result;
}

function getProblemInstance($instance_id) #MOODLE DONE
{
    $problem = null;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT * FROM mdl_course_modules WHERE id= :problem_id");
        $statement->execute(array(":problem_id" => $instance_id));
        $problem = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the problem: ' . $e->getMessage();
    }
    return $problem;

}

function getProblemWithId($problem_id) #MOODLE DONE
{
    $problem = null;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT * FROM mdl_uab_interactive_problem WHERE id= :problem_id");
        $statement->execute(array(":problem_id" => $problem_id));
        $problem = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the problem: ' . $e->getMessage();
    }
    return $problem;

}

function getStudentsWithSessionAndProblem(int $session_id, int $problem_id): array //TODO, por revisar
{
    $students = [];
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "SELECT * FROM mdl_uab_interactive_solucio WHERE problem_id= :problem_id"
        );
        $statement->execute(array(":problem_id" => $problem_id));
        $students = $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the students: ' . $e->getMessage();
    }

    //https://stackoverflow.com/questions/24138034/array-unique-showing-error-array-to-string-conversion
    return array_unique($students, SORT_REGULAR);
}

function getProblemExtraData(int $problemId):array
{
    $extraData=[];
    try{

        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT number_lines_file, solution_quality FROM mdl_uab_interactive_problem WHERE id=:problemId");
        $statement->execute(array(':problemId'=>$problemId));
        $extraData = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (PDOException $e) {
        echo 'Error updating the problem: ' . $e->getMessage();
    }
    return $extraData;
}


function getUserEmail($instance_id) #MOODLE DONE
{
    $email = null;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT email FROM mdl_user WHERE id= :problem_id");
        $statement->execute(array(":problem_id" => $instance_id));
        $email = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the problem: ' . $e->getMessage();
    }
    return $email;

}

function getProblemsWithSubject($subject_id): array
{
    $problems = [];
    try {
        $connection = connectDB();
        $statement =
            $connection->prepare("SELECT id, title, visibility FROM problem WHERE subject_id=:subject_id");
        $statement->bindParam(":subject_id", $subject_id);
        $statement->execute();
        $problems = $statement->fetchAll();
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the problems of the subject: ' . $e->getMessage();
    }
    return $problems;
}

function getProblemsWithSession(int $session_id): array
{
    $problems = [];
    try {
        $connection = connectDB();
        // Get all the problem ids of the session
        $statement = $connection->prepare("SELECT problem_id FROM session_problems WHERE session_id=:session_id");
        $statement->bindParam(":session_id", $session_id);
        $statement->execute();
        $problem_ids = $statement->fetchAll(PDO::FETCH_COLUMN);

        // Get all the data of the problems
        $statement = $connection->prepare("SELECT id, title FROM problem WHERE id=:problem_id");
        foreach ($problem_ids as $problem_id) {
            $statement->bindParam(":problem_id", $problem_id);
            $statement->execute();
            $problems[] = $statement->fetch(PDO::FETCH_ASSOC);
        }
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the problems of the session: ' . $e->getMessage();
    }
    return $problems;
}



function getSubjects(): array
{
    $subjects = [];
    try {
        $connection = connectDB();
        // Get all the subjects and append add a field to know whether it has active sessions or not
        $statement = $connection->prepare("
            SELECT id, title, course, description, 
                   (SELECT EXISTS(SELECT * from session WHERE subject_id=subject.id)) as has_active_sessions
            FROM subject");
        $statement->execute();
        $subjects = $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the subjects: ' . $e->getMessage();
    }
    return $subjects;
}

function createSolution($problem_route, $problem_id, $subject_id, $user_email) : bool
{
    $created = false;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "INSERT INTO mdl_uab_interactive_solucio(route, problem_id, subject_id, user_id, editing, edited)
            VALUES (:route, :problem_id, :subject_id, :user_email, 0, 0)"
        );

        $statement->execute(array(":route" => $problem_route, ":problem_id" => $problem_id,
            ":subject_id" => $subject_id, ":user_email" => $user_email));

        $connection = null;
        $statement->closeCursor();
        $created = true;
    } catch (Exception $e) {
        echo "Error creating the solution: " . $e->getMessage();
    }
    return $created;
}



function createSolutionGroupRelation($solutionID, $group) : bool
{
    $created = false;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "INSERT INTO mdl_uab_solucio_grup_session(solucio_id, grup) VALUES (:solucio_id, :grup)");

        $statement->execute(array(":solucio_id" => $solutionID, ":grup" => $group));
        $connection = null;
        $statement->closeCursor();
        $created = true;
    } catch (Exception $e) {
        echo "Error creating the solution: " . $e->getMessage();
    }
    return $created;
}

function updateSolutionGroupRelation($solutionID, $group) : bool
{
    $updated = False;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("UPDATE mdl_uab_solucio_grup_session SET grup=:grup WHERE solucio_id= :solucio_id");
        $statement->execute(array(":solucio_id" => $solutionID, ":grup" => $group));
        $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
        $updated = True;
    } catch (PDOException $e) {

        echo 'Error setting the relation' . $e->getMessage();
    }
    return $updated;
}

function getRelation($solutionID) #MOODLE DONE, antes era el correo, ahora el user ID
{
    $solution = null;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "SELECT * FROM mdl_uab_solucio_grup_session WHERE solucio_id= :solucio_id");
        $statement->execute(array(":solucio_id" => $solutionID));
        $solution = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the solution: ' . $e->getMessage();
    }
    return $solution;
}

function getSolution($problem_id, $user_id) #MOODLE DONE, antes era el correo, ahora el user ID
{
    $solution = null;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "SELECT * FROM mdl_uab_interactive_solucio WHERE problem_id= :problem_id and user_id= :user_id"
        );
        $statement->execute(array(":problem_id" => $problem_id, ":user_id" => $user_id));
        $solution = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the solution: ' . $e->getMessage();
    }
    return $solution;
}

function unsetSolutionEdited($id, $mail) : bool
{
    $updated = False;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE solution SET edited=0 WHERE problem_id= :id and user= :mail");
        $statement->execute(array(":id" => $id, ":mail" => $mail));
        $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
        $updated = True;
    } catch (PDOException $e) {

        echo 'Error setting the solution as not edited' . $e->getMessage();
    }
    return $updated;
}

function updateProblem($problem_id, $description, $max_memory_usage, $max_execution_time, $programming_language, $entregable, $deadline) : bool
{
    $updated = false;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE problem SET description=:description, memory=:max_memory_usage,
                   time=:max_execution_time, language=:programming_language, entregable= :entregable, deadline= :deadline WHERE id= :problem_id");

        $statement->execute(array(':description'=>$description, ':max_memory_usage'=>$max_memory_usage,
            ':max_execution_time'=>$max_execution_time, ':programming_language' => $programming_language,
            ':entregable' => $entregable , ':deadline' => $deadline , ':problem_id'=>$problem_id));
        $statement->fetch();
        $connection = null;

        $updated = true;
    } catch (PDOException $e) {
        echo 'Error updating the problem: ' . $e->getMessage();
    }
    return $updated;
}

function getProblemSolutionVisibility(int $problemId ):string
{
    try{
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT solution_visibility FROM mdl_uab_interactive_problem WHERE id=:problemId");
        $statement->execute(array(':problemId'=>$problemId));
        $visibility = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    }catch (PDOException $e) {
        echo 'Error updating the problem: ' . $e->getMessage();
    }
    return $visibility['solution_visibility'];
}

function getIfProblemIsEntregable(int $problemId):string
{
    try{
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT entregable FROM mdl_uab_interactive_problem WHERE id=:problemId");
        $statement->execute(array(':problemId' => $problemId));
        $IsEntregable = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    }catch (PDOException $e) {
        echo 'Error updating the problem: ' . $e->getMessage();
    }
    return $IsEntregable['entregable'];
}
function getEntregableDeadline($problemId){
    try{
        $connection = connectDB();
        $statement = $connection->prepare("SELECT deadline FROM problem WHERE id=:problemId");
        $statement->execute(array(':problemId' => $problemId));
        $date = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    }catch (PDOException $e) {
        echo 'Error getting Problem Entregable deadline: ' . $e->getMessage();
    }
    return $date['deadline'];

}