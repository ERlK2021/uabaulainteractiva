<?php
function getStudentFullNameByNiu($NIU) : array
{

    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT name, surname FROM student WHERE NIU= :NIU");
        $statement->bindParam(":NIU", $NIU);
        $statement->execute();
        $student_full_name = $statement->fetch(PDO::FETCH_ASSOC);
        //$student_full_name = $student_full_name["surname"] . ', ' . $student_full_name["name"];
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining student full name: ' . $e->getMessage();
    }
    return $student_full_name;
}
function getNIUStudent($email) : string
{
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT NIU FROM student WHERE email= :email");
        $statement->bindParam(":email", $email);
        $statement->execute();
        $student_NIU = $statement->fetch(PDO::FETCH_ASSOC);

        $connection = null;
    } catch (PDOException $e) {
        echo 'Error getiing student NIU by its email: ' . $e->getMessage();
    }
    return $student_NIU['NIU'];
}