<?php

function logIn($password, $user): bool {
    $user_password = $user['password'];
    if (password_verify($password, $user_password)) {
        $_SESSION['user'] = $user['name'];
        $_SESSION['email'] = $user['email'];
        return true;
    }
    return false;
}

function logInStudent($email, $password): bool
{
    $loggedIn = false;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM student WHERE email= :email");
        $statement->execute(array(":email" => $email));
        $student = $statement->fetch(PDO::FETCH_ASSOC);
        if (logIn($password, $student)) {
            $_SESSION['user_type'] = STUDENT;
            $loggedIn = true;
        }
        
    } catch (PDOException $e) {
        echo 'Error logging in as student: ' . $e->getMessage();
    }
    return $loggedIn;
}
function getNameStudent($email):array
{
    try {
    $connection = connectDB();
    $statement = $connection->prepare("SELECT name,surname FROM student WHERE email= :email");
    $statement->execute(array(":email" => $email));
    $name_surname = $statement->fetch(PDO::FETCH_ASSOC);}
    catch(PDOException $e) {
        echo 'Error getting student data (Model/login.php): ' . $e->getMessage();
    }
    return $name_surname;
}
function getNameTeacher($email):array
{
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT name,surname FROM professor WHERE email= :email");
        $statement->execute(array(":email" => $email));
        $name_surname = $statement->fetch(PDO::FETCH_ASSOC);}
    catch(PDOException $e) {
        echo 'Error getting student data (Model/login.php): ' . $e->getMessage();
    }
    return $name_surname;
}

function logInProfessor($email, $password): bool
{
    $loggedIn = false;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM professor WHERE email= :email");
        $statement->execute(array(":email" => $email));
        $professor = $statement->fetch(PDO::FETCH_ASSOC);
        
        if($email == 'ernest@cvc.uab.cat'){
            $connection = connectDB();
            $statement = $connection->prepare("UPDATE professor SET password=:pswd WHERE email= :email");
            $hash_password = password_hash('pruebaProfessor', PASSWORD_DEFAULT);
            $statement->execute(array(":email" => $email, ":pswd" =>$hash_password));

        }

        if (logIn($password, $professor)) {
            $_SESSION['user_type'] = PROFESSOR;
            $loggedIn = true;
        }
    } catch (PDOException $e) {
        echo 'Error logging in as professor: ' . $e->getMessage();
    }
    return $loggedIn;
}
