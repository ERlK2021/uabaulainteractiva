<?php
include_once __DIR__ . "/../Model/problemsGet.php"; //Used for sendComunMessage()

function addMessagetoChat(string $incoming_mail, string $outgoing_mail, int $session_id, int $problem_id, string $msg, string $date):bool
{
    try {
        // Create a transaction so if any insertion fails no object will be created
        $connection = connectDB();
        $connection->beginTransaction();


        $statement = $connection->prepare("INSERT INTO messages(incoming_mail_id, outgoing_mail_id, session_id, problem_id, msg, date) 
            VALUES(:incoming_mail_id, :outgoing_mail_id, :session_id, :problem_id, :msg, :date)");
        $statement->execute(array(":incoming_mail_id" => $incoming_mail, ":outgoing_mail_id"=>$outgoing_mail, ":session_id"=>$session_id, ":problem_id"=>$problem_id,":msg" => $msg, ":date"=>$date));
        //$sessionId = $connection->lastInsertId();

        // Commit the changes
        $connection->commit();
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error making the chat: ' . $e->getMessage();
        return false;
    }
    return true;
}
function viewchats(string $incoming_mail, string $outgoing_mail, int $session_id, int $problem_id):array
{
    try{
        $connection = connectDB();

        $statement = $connection->prepare("SELECT msg,date,incoming_mail_id FROM messages WHERE ((outgoing_mail_id=:outgoing_mail_id AND incoming_mail_id=:incoming_mail_id) OR (outgoing_mail_id=:incoming_mail_id AND incoming_mail_id=:outgoing_mail_id)) AND session_id=:session_id AND problem_id=:problem_id");
        $statement->execute(array(":outgoing_mail_id"=> $outgoing_mail, ":incoming_mail_id"=> $incoming_mail, ":session_id"=> $session_id, ":problem_id"=>$problem_id));
        $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

        $connection = null;
    }catch(PDOException $e){
        echo 'Error showing the chats: ' . $e->getMessage();
        return [null];
    }
    return $messages;
}
function viewchatsAsStudent(string $student_mail, int $session_id, int $problem_id):array
{
    try{
        $connection = connectDB();

        $statement = $connection->prepare("SELECT msg, date, incoming_mail_id FROM messages WHERE (incoming_mail_id=:incoming_mail_id OR outgoing_mail_id=:outgoing_mail_id) AND session_id=:session_id AND problem_id=:problem_id");
        $statement->execute(array(":incoming_mail_id"=> $student_mail,":outgoing_mail_id"=> $student_mail, ":session_id"=> $session_id, ":problem_id"=>$problem_id));
        $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

        $connection = null;
    }catch(PDOException $e){
        echo 'Error showing the chats: ' . $e->getMessage();
        return [null];
    }
    return $messages;
}
function getTeacherEmailFromChat(string $student_mail, int $session_id, int $problem_id):string
{
    try{
        $connection = connectDB();

        $statement = $connection->prepare("SELECT outgoing_mail_id FROM messages WHERE incoming_mail_id=:incoming_mail_id AND session_id=:session_id AND problem_id=:problem_id");
        $statement->execute(array(":incoming_mail_id"=> $student_mail, ":session_id"=> $session_id, ":problem_id"=>$problem_id));
        $emails = $statement->fetchAll(PDO::FETCH_ASSOC);
        $teacher_email=$emails[0]['outgoing_mail_id'];

        $connection = null;
    }catch(PDOException $e){
        echo 'Error getting teacher email: ' . $e->getMessage();
    }
    return $teacher_email;
}
function unviwedStudentsChat(int $session_id, int $problem_id):array
{
    try{
        $connection = connectDB();

        $statement = $connection->prepare("SELECT outgoing_mail_id FROM messages WHERE viewed=:cero AND session_id=:session_id AND problem_id=:problem_id");
        $statement->execute(array(":cero"=>'0', ":session_id"=> $session_id, ":problem_id"=>$problem_id));
        $emails = $statement->fetchAll(PDO::FETCH_ASSOC);

        $cleaned_emails =[];
        foreach ($emails as $e){array_push($cleaned_emails,$e['outgoing_mail_id']);}
        $cleaned_emails = array_unique($cleaned_emails); //Removes duplicate values from an array
    }catch (PDOException $e){
        echo 'Error getting emails of users with unviwed chats by the teacher: ' . $e->getMessage();
    }
    return $cleaned_emails;
}

function changeViewedChatStatus(string $mail1):bool
{
    try{
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE messages SET viewed=:one WHERE outgoing_mail_id=:mail1");
        $statement->execute(array(":one"=>'1', ":mail1"=>$mail1));

    }catch (PDOException $e){
        echo 'Error changing chats viewed status: ' . $e->getMessage();
        return false;
    }
    return true;
}
function sendComunMessage(string $teacher_outgoing_mail, int $session_id, int $problem_id, string $msg, string $date):bool
{
    try{

        $active_students = getStudentsWithSessionAndProblem($session_id, $problem_id); //Estudiantes activos en la sesion y el problema .

        $connection = connectDB();

        foreach ($active_students as $student) {
            $statement = $connection->prepare("INSERT INTO messages(incoming_mail_id, outgoing_mail_id, session_id, problem_id, msg, date, comun) 
            VALUES(:incoming_mail_id, :outgoing_mail_id, :session_id, :problem_id, :msg, :date, :comun)");
            $statement->execute(array(":incoming_mail_id" => $student['user'], ":outgoing_mail_id" => $teacher_outgoing_mail, ":session_id" => $session_id, ":problem_id" => $problem_id, ":msg" => $msg, ":date" => $date, ":comun" => 1));
        }

    }catch (PDOException $e){
        echo 'Error sending comun message: ' . $e->getMessage();
        return false;
    }
    return true;
}
function sendComunMessageToNewStudent(string $incoming_mail_id, string $outgoing_mail_id, int $session_id, int $problem_id, string $msg, string $date){
    try{
        $connection = connectDB();
        $statement = $connection->prepare("INSERT INTO messages(incoming_mail_id, outgoing_mail_id, session_id, problem_id, msg, date, comun) 
        VALUES(:incoming_mail_id, :outgoing_mail_id, :session_id, :problem_id, :msg, :date, :comun)");
            $statement->execute(array(":incoming_mail_id" =>  $incoming_mail_id,":outgoing_mail_id" => $outgoing_mail_id, ":session_id" => $session_id, ":problem_id" => $problem_id, ":msg" => $msg, ":date" => $date, ":comun" => 1));

    }catch (PDOException $e){
        echo 'Error sending comun message to new Student: ' . $e->getMessage();
        return false;
    }
    return true;
}

function viewComunChats($outgoing_mail, $sessionId, $problemId):array
{

    try{
        $connection = connectDB();
        $statement = $connection->prepare("SELECT msg, date, outgoing_mail_id FROM messages WHERE outgoing_mail_id=:outgoing_mail_id AND session_id=:session_id AND problem_id=:problem_id AND comun=:one");
        $statement->execute(array(":outgoing_mail_id"=> $outgoing_mail, ":session_id"=> $sessionId, ":problem_id"=>$problemId, ":one"=>1));
        $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

        $cleaned_messages = array_unique($messages, SORT_REGULAR); // Los mensajes comunes estan repetidos en la BD (Tantas veces como estudiantes activos hayan), por tanto aqui los limpiamos gracias a la fecha que invluye la hora el minuto y el segundo en que el proefor envió el mensaje.

    }
    catch(PDOException $e){
        echo 'Error sending comun message: ' . $e->getMessage();
        return [false];
    }
    return $cleaned_messages;
}
function checkAllStudentsRecivedComunMessage(array $students, int $session_id,int $problem_id):bool{

    $connection = connectDB();
    $statement = $connection->prepare("SELECT msg, date, incoming_mail_id, outgoing_mail_id FROM messages WHERE session_id=:session_id AND problem_id=:problem_id AND comun=:one");
    $statement->execute(array(":session_id"=> $session_id, ":problem_id"=>$problem_id, ":one"=>1));
    $messages = $statement->fetchAll(PDO::FETCH_ASSOC);

    $students_comun_messages_count= array();
    foreach ($messages as $message){
        foreach ($students as $student) {
            if($message['incoming_mail_id'] == $student['user']){
                if(array_key_exists($student['user'], $students_comun_messages_count)){

                    $array_aux =[];
                    array_push($array_aux, $students_comun_messages_count[$student['user']]);
                    array_push($array_aux, $message);
                    $students_comun_messages_count[$student['user']] = $array_aux;
                }
                else{
                    $students_comun_messages_count[$student['user']] = $message;
                }
            }
        }
    }
    //print_r($students_comun_messages_count); //Diccionario con los alumnos que han recibido algun mensaje comun y los mensajes que han reibido, indicando la fecha el enviante..
    $max_com_messages = 0;
    $counter = array();
    $max_com_messages_student="";

    foreach ($students as $student){
        if(is_array($students_comun_messages_count[$student['user']][0])){ //Estudiante con más de un mensaje comun

            $counter[$student['user']] = count($students_comun_messages_count[$student['user']]);//Contamos el num de mensajes del estudiante
        }
        else{
            if(array_key_exists($student['user'], $students_comun_messages_count)){ //SOLO TIENE 1 MENSAJE COMUN
                $counter[$student['user']] = 1;
            }
            else{ //Tiene 0 mensajes comunes
                $counter[$student['user']] = 0;
            }
        }
        if($max_com_messages < $counter[$student['user']]){
            $max_com_messages = $counter[$student['user']];
            $max_com_messages_student = $student['user'];
        };
    }

    //print_r($counter); //print_r($max_com_messages);

    foreach ($students as $student) {
        if($counter[$student['user']] < $max_com_messages){ //A este alumno le faltan mensajes comunes por recibir que otros alumnos SI han recibido.
            if( $max_com_messages == 1){

                $message = $students_comun_messages_count[$max_com_messages_student];
                if(!array_key_exists($student['user'], $students_comun_messages_count)){

                    sendComunMessageToNewStudent($student['user'], $message['outgoing_mail_id'], $session_id, $problem_id,$message['msg'],$message['date']);
                }
            }else {
                foreach ($students_comun_messages_count[$max_com_messages_student] as $message){
                    if(!array_key_exists($student['user'], $students_comun_messages_count)){

                        sendComunMessageToNewStudent($student['user'], $message['outgoing_mail_id'], $session_id, $problem_id,$message['msg'],$message['date']);
                    }
                }
            }
        }
    }
    return true;
}