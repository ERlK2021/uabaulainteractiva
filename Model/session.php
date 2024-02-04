<?php

function createSession(string $name, int $professorId, int $subjectId, array $problemIds, string $class_group) : int
{
    $sessionId = 0;
    try {
        // Create a transaction so if any insertion fails no object will be created
        $connection = connectDB();
        $connection->beginTransaction();

        // Create the session and get its id
        $statement = $connection->prepare("INSERT INTO session(name, class_group, status, professor_id, subject_id) 
            VALUES(:name, :class_group, :status, :professor_id, :subject_id)");
        $statement->execute(array(":name" => $name, "class_group"=>$class_group, "status"=>'activated', ":professor_id" => $professorId, ":subject_id" => $subjectId));
        $sessionId = $connection->lastInsertId();

        // Create a relation between each selected problem and the session
        foreach ($problemIds as $problem_id) {
            $connection->exec("INSERT INTO session_problems(session_id, problem_id)
                                        VALUES($sessionId, $problem_id)");
        }

        // Commit the changes
        $connection->commit();
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error creating the session: ' . $e->getMessage();
    }
    return $sessionId;
}
function deleteSession(int $sessionId) : bool
{
    $deleted = false;
    try {
        $connection = connectDB();
        // Since the relation between Session and SessionProblems is CASCADE, the relation will be deleted as well
        $statement = $connection->prepare("DELETE FROM session WHERE id=:session_id");
        $statement->execute(array(":session_id" => $sessionId));

        $connection = null;
        $deleted = True;
    } catch (PDOException $e) {
        echo 'Error deleting the session: ' . $e->getMessage();
    }
    return $deleted;
}
function deleteGroupSessions(string $class_group): bool
{
    $deleted = false;
    try {
        $connection = connectDB();
        // Since the relation between Session and SessionProblems is CASCADE, the relation will be deleted as well
        $statement = $connection->prepare("DELETE FROM session WHERE class_group=:class_group");
        $statement->execute(array(":class_group" =>  $class_group));

        $connection = null;
        $deleted = True;
    } catch (PDOException $e) {
        echo 'Error deleting the session: ' . $e->getMessage();
    }
    return $deleted;
}
function getActiveSessionsFromGroup(int $subjectId, string $class_group) : array
{
    $sessions = [];
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM session WHERE subject_id= :subject_id AND class_group= :class_group");
        $statement->execute(array(":subject_id" => $subjectId, ":class_group"=>$class_group));
        $sessions = $statement->fetchAll(PDO::FETCH_ASSOC);

        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the sessions: ' . $e->getMessage();
    }
    return $sessions;
}
function getGroupsActiveSessions(int $subjectId): array
{
    try{
        $connection = connectDB();
        $statement = $connection->prepare("SELECT `class_group` FROM session WHERE subject_id= :subject_id");
        $statement->execute(array(":subject_id" => $subjectId));
        $groups = $statement->fetchAll(PDO::FETCH_ASSOC);

        $cleaned_groups=[];
        foreach ($groups as $group){ array_push($cleaned_groups,$group["class_group"]); }
        $connection = null;

    }catch (PDOException $e){
        echo 'Error retrieving sessions groups: ' . $e->getMessage();

    }
    return array_unique($cleaned_groups);
}
function getSessionStatus(int $sessionId): string
{
    try{
        $connection = connectDB();
        $statement = $connection->prepare("SELECT status FROM session WHERE id=:session_id");
        $statement->execute(array(":session_id" => $sessionId));
        $sessionStatus= $statement->fetchAll(PDO::FETCH_ASSOC);


    }catch (PDOException $e){
        echo 'Error retrieving sessions groups: ' . $e->getMessage();

    }
    /*var_dump($sessionStatus);*/
    return $sessionStatus[0]['status'];

}
function getTeacherCreatedSession(int $sessionId):array
{
    try{
        $connection = connectDB();
        $statement = $connection->prepare("SELECT professor_id FROM session WHERE id=:session_id");
        $statement->execute(array(":session_id" => $sessionId));
        $professorId = $statement->fetchAll(PDO::FETCH_ASSOC);

        print_r($professorId);

        $statement = $connection->prepare("SELECT name, email, surname FROM professor WHERE id=:professorId");
        $statement->execute(array(":professorId" => $professorId[0]['professor_id']));
        $professorData = $statement->fetchAll(PDO::FETCH_ASSOC);

    }catch (PDOException $e){
        echo 'Error getting teacher id of the session ' . $e->getMessage();

    }
    return $professorData;
}
function addStudentToSession(int $sessionId, string $email) : bool
{
    $added = false;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE student SET session_id=:session_id WHERE email=:email");
        $statement->execute(array(":session_id" => $sessionId, ":email" => $email));

        $added = true;
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the sessions: ' . $e->getMessage();
    }
    return $added;
}
function duplicateSession(string $sessionName, int $sessionId): bool
{
    $duplicated = false;
    try {
        $connection = connectDB();

        // Get the data of the session that we want to duplicate
        $statement = $connection->prepare("SELECT professor_id, subject_id, class_group FROM session WHERE id=:session_id");
        $statement->execute(array(":session_id" => $sessionId));
        $session = $statement->fetch();

        $statement = $connection->prepare("SELECT problem_id FROM session_problems WHERE session_id=:session_id");
        $statement->execute(array(":session_id" => $sessionId));
        $problemIds = $statement->fetchAll(PDO::FETCH_COLUMN);

        // Use the data to create the new session
        createSession($sessionName, $session["professor_id"], $session["subject_id"], $problemIds, $session["class_group"]);

        $connection = null;
        $duplicated = True;
    } catch (PDOException $e) {
        echo 'Error duplicating the session: ' . $e->getMessage();
    }
    return $duplicated;
}
function getActiveSessionsFromSubject($subjectId):array
{
    $sessions = [];
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM session WHERE subject_id= :subject_id");
        $statement->execute(array(":subject_id" => $subjectId));
        $sessions = $statement->fetchAll(PDO::FETCH_ASSOC);

        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the sessions: ' . $e->getMessage();
    }
    return $sessions;
}
function getSessionsAndItsGroups($subjectId):array //MEJORAR CÓDIGO
{
    $all_sessions = getActiveSessionsFromSubject($subjectId);
    $sessions_groups_dict=[];

    foreach ($all_sessions as $session) {
        if(array_key_exists($session['name'], $sessions_groups_dict)){

            array_push( $sessions_groups_dict[$session['name']], $session['class_group']);
        }
        else{
            $sessions_groups_dict[$session['name']] = array($session['class_group']);
        }
    }
    $session_name=""; $session_groups=""; $lastId="";
    foreach ($all_sessions as &$session) {

        if(array_key_exists($session['name'], $sessions_groups_dict)){

            if($session_name == $session['name'] && $session_groups == $sessions_groups_dict[$session['name']]){
                $session['id'] = $lastId;
            }
            else{
                $lastId = $session['id'];
            }
            $session['class_group'] = $sessions_groups_dict[$session['name']];
            $session_name = $session['name'];
            $session_groups = $sessions_groups_dict[$session['name']];
        }
    }

    foreach ($all_sessions as &$session) {//Eliminamos status para que funcione bien el array_unique de después.
        unset($session["status"]);
    }
    return array_map("unserialize", array_unique(array_map("serialize", $all_sessions)));
}
function getSessionSubject($session_name):int
{
    try {

        $connection = connectDB();
        $statement = $connection->prepare("SELECT subject_id FROM session WHERE name=:session_name");
        $statement->execute(array(":session_name" => $session_name));
        $subject_id = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;


    } catch (PDOException $e) {
        echo 'Error getting sessions subject: ' . $e->getMessage();
    }
    return $subject_id['subject_id'];
}
function getSessionGroups($sessionName):array
{
    $sessions = [];
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT class_group FROM session WHERE name=:sesionName");
        $statement->execute(array(":sesionName" => $sessionName));
        $groups = $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error retrieving the sessions: ' . $e->getMessage();
    }
    return array_column($groups , "class_group");
}
function updateSession($sessionId, $new_session_name, $problemIds):bool
{
    try {
        // Create a transaction so if any insertion fails no object will be created
        $connection = connectDB();
        $connection->beginTransaction();

        if($new_session_name!=null) {
            $statement = $connection->prepare("UPDATE session SET name=:new_session_name  WHERE id=:sessionId");
            $statement->execute(array(":new_session_name"=>$new_session_name, ":sessionId" => $sessionId));
        }
        if($problemIds!=null) {
            foreach ($problemIds as $problem_id) {
                $connection->exec("INSERT INTO session_problems(session_id, problem_id)VALUES($sessionId, $problem_id)");
            }
        }
        $connection->commit();
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error creating the session: ' . $e->getMessage();
        return false;
    }
    return true;
}
function getSessionId($session_name):int
{
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT id FROM session WHERE name=:session_name");
        $statement->execute(array(":session_name" => $session_name));
        $session_id = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (PDOException $e) {
        echo 'Error getting sessions subject: ' . $e->getMessage();
    }
    return $session_id['id'];
}
function getSessionIdPlus($session_name, $group): int
{
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT id FROM session WHERE name =:session_name AND class_group =:class_group");
        $statement->execute(array(':session_name' => $session_name, ':class_group'=> $group));
        $session_id = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (PDOException $e) {
        echo 'Error getting sessions subject: ' . $e->getMessage();
        return 0;
    } catch (Exception $e) {
        echo $e->getMessage();
        return 0;
    }
    return $session_id['id'];
}
function sessionExists($sessionName):bool
{
    $exists = True;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT COUNT(*) FROM session WHERE name= :name");
        $statement->execute(array(":name" => $sessionName));
        $count = $statement->fetchColumn();
        $connection = null;
        $exists = $count != 0;
    } catch (PDOException $e) {
        echo 'Error looking for the session with the specified title: ' . $e->getMessage();
    }
    return $exists;
}
function getSessionName($sessionId):string
{
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT name FROM session WHERE id=:sessionId");
        $statement->execute(array(":sessionId" => $sessionId));
        $session_id = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (PDOException $e) {
        echo 'Error getting session name by Id: ' . $e->getMessage();
    }
    return $session_id['name'];
}
function deleteAllSessionsByName($sessionName):bool
{
    $deleted = false;
    try {
        $connection = connectDB();
        // Since the relation between Session and SessionProblems is CASCADE, the relation will be deleted as well
        $statement = $connection->prepare("DELETE FROM session WHERE name=:sessionName");
        $statement->execute(array(":sessionName" => $sessionName));
        $connection = null;
        $deleted = True;
    } catch (PDOException $e) {
        echo 'Error deleting the session: ' . $e->getMessage();
    }
    return $deleted;
}