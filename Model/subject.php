<?php
function createSubject($title, $description, $course) : int
{
    $subjectId = -1;
    try {
        $connection = connectDB();

        $statement = $connection->prepare(
            "INSERT INTO subject (title, description, course) VALUES (:title, :description,:course)"
        );
        $statement->execute(array(":title" => $title, ":description" => $description, ":course" => $course));

        $subjectId = $connection->lastInsertId();
        $connection = null;
    } catch (Exception $e) {
        echo "Error creating the subject: " . $e->getMessage();
    }
    return $subjectId;
}

function deleteSubject($subjectId) : bool
{
    try {
        $connection = connectDB();
        $statement1 =$connection->prepare("DELETE FROM session WHERE subject_id=:subjectId");
        $statement1->execute(array(":subjectId" => $subjectId));
        $statement2 =$connection->prepare("DELETE FROM problem WHERE subject_id=:subjectId");
        $statement2->execute(array(":subjectId" => $subjectId));
        $statement3 = $connection->prepare("DELETE FROM subject WHERE id=:subjectId");
        $statement3->execute(array(":subjectId" => $subjectId));

        $deleted = true;
        $connection = null;
    } catch (Exception $e) {
        $deleted = false;
    }
    return $deleted;
}