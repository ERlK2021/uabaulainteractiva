<?php
require_once __DIR__ . '/connection.php';

function registerStudent($name, $surname, $email, $hash_password, $NIU) : bool
{
    $created = false;
    try {
        $connection = connectDB();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Mail not valid";
            return false;
        }

        $statement = $connection->prepare(
            "INSERT INTO student(name, surname, email, NIU, password) 
            VALUES (:student_name, :surname, :email, :NIU, :password)"
        );

        $statement->execute(array(":student_name" => $name, ":surname" => $surname, ":email" => $email,
            ":NIU"=>$NIU, ":password" => $hash_password));

        $statement->closeCursor();
        $connection = null;
        $created = true;
    } catch (Exception $e) {
        echo "Student not created: " . $e->getMessage();
    }
    return $created;
}

function setTokenUsed($token) : bool
{
    $valid = false;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM tokens AS t WHERE t.value=:token and t.usage=0");
        $statement->execute(array(":token" => $token));
        $token_row = $statement->fetchColumn();
        // The token doesn't exist, or it's no longer available
        if (!$token_row) {
            return false;
        }

        $stmt = $connection->prepare("UPDATE tokens AS t SET t.usage=1 WHERE t.value=:token and t.usage=0");
        $stmt->execute(array(":token" => $token));

        $valid = true;
        $connection = null;
    } catch (Exception $e) {
        echo "Couldn't set the token as used: " . $e->getMessage();
    }
    return $valid;
}

function isEmailTaken($email) : bool
{
    $taken = true;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT COUNT(*) FROM student WHERE email= :email");
        $statement->execute(array(":email" => $email));
        $students_count = $statement->fetchColumn();

        $statement = $connection->prepare("SELECT COUNT(*) FROM professor WHERE email= :email");
        $statement->execute(array(":email" => $email));
        $professors_count = $statement->fetchColumn();

        $taken = ($students_count + $professors_count) != 0;
    } catch (Exception $e) {
        echo "Linea del error:" . $e->getLine();
    }
    return $taken;
}

function registerProfessor($name, $surname, $email, $hash_password) : bool
{
    $created = false;
    try {
        $connection = connectDB();

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "Mail not valid";
            return false;
        }

        $statement = $connection->prepare(
            "INSERT INTO professor(name, surname, email, password)
            VALUES (:professor_name, :surname, :email, :password)");

        $statement->execute(array(":professor_name" => $name, ":surname" => $surname, ":email" => $email,
            ":password" => $hash_password));

        $statement->closeCursor();
        $connection = null;
        $created = true;
    } catch (Exception $e) {
        echo "Professor not created: " . $e->getMessage();
    }
    return $created;
}
