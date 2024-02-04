<?php


function createEntregableStudentRelation($problemId, $student_email): bool
{
    try {

        $connection = connectDBMoodle();
        $statement = $connection->prepare("INSERT INTO mdl_uab_entregable_student_grade( problem_id, student_id, grade) VALUES (:problem_id, :student_id, :grade)");
        $statement->execute(array(":problem_id" => $problemId, ":student_id" => $student_email, ":grade" => 0));
        $connection = null;

    } catch (Exception $e) {
        echo "Error creating EntregableStudentRelation: " . $e->getMessage();
        return false;
    }
    return true;
}
function updateGradeEntregable($problemId, $student_email, $grade): bool
{
    try {

        $connection = connectDBMoodle();
        $statement = $connection->prepare("UPDATE mdl_uab_entregable_student_grade SET grade= :grade WHERE problem_id= :problem_id AND student_id= :student_id");
        $statement->execute(array(":grade" => $grade, ":problem_id" => $problemId, ":student_id" => $student_email));
        $connection = null;

    } catch (Exception $e) {
        echo "Error updating grade: " . $e->getMessage();
        return false;
    }
    return true;
}

function getEntregableGrade($student_email, $problemId):int
{
    try {

        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT grade FROM mdl_uab_entregable_student_grade WHERE problem_id= :problem_id AND student_id= :student_id");
        $statement->execute(array(":problem_id" => $problemId, ":student_id" => $student_email));
        $grade = $statement->fetch(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (Exception $e) {
        echo "Error getting student grade: " . $e->getMessage();
        return false;
    }
    if(is_null($grade['grade'])){$grade['grade']=0;}
    return $grade['grade'];
}
function checkIfEntregableStudentRelationExists($problemId, $student_email):bool
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT * FROM mdl_uab_entregable_student_grade WHERE problem_id= :problem_id AND student_id= :student_id");
        $statement->execute(array(":problem_id" => $problemId, ":student_id" => $student_email));
        $data = $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;

        $exists = count($data)>0? true : false;

    } catch (Exception $e) {
        echo "Error creating EntregableStudentRelation: " . $e->getMessage();
        return false;
    }
    return $exists;
}

function getEntregableData($problemId):array{
    try {

        $connection = connectDB();
        $statement = $connection->prepare("SELECT * FROM entregable_student_grade WHERE problem_id= :problem_id");
        $statement->execute(array(":problem_id" => $problemId));
        $problemData= $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (Exception $e) {
        echo "Error getting problem Users data to create CSV: " . $e->getMessage();
    }
    return $problemData;
}

