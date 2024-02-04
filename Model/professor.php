<?php

function getProfessor(string $professorEmail) : array
{
    $professor = array();
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM professor WHERE email=:professor_email");
        $statement->bindParam(":professor_email", $professorEmail);
        $statement->execute();
        $professor = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;
    } catch (PDOException $e) {
        echo 'Error obtaining the professor data: ' . $e->getMessage();
    }
    return $professor;
}
