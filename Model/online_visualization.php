<?php

function getStudentSessionRelation():array
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT * FROM mdl_uab_interactive_stusession");
        $statement->execute();
        $array_result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (PDOException $e) {
        echo 'Error geting student_sessions_relation array: ' . $e->getMessage();
    }
    return $array_result;
}

function createStudentSessionRelation(string $email, int $session_id, string $output, int $problemId, int $problemLines, string $problemQualityInfo):bool
{
    $created = false;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "INSERT INTO mdl_uab_interactive_stusession(student_email, session_id, problem_id, output, number_lines_file, solution_quality, executed_times_count, teacher_executed_times_count) 
            VALUES (:student_email, :session_id, :problem_id, :output, :number_lines_file, :solution_quality, :executed_times_count, :teacher_executed_times_count )"
        );

        $statement->execute(array(":student_email" => $email, ":session_id" => $session_id, ":problem_id" => $problemId, ":output" => $output,
            ":number_lines_file"=> $problemLines,  ":solution_quality" => $problemQualityInfo, ":executed_times_count" => 1, ":teacher_executed_times_count"=> 0));
        $statement->closeCursor();
        $connection = null;
        $created = true;

    } catch (Exception $e) {
        echo "Student_Session not created: " . $e->getMessage();
    }
    return $created;
}
function createStudentSessionRelationNoOutput(string $email, int $session_id, int $problemId, int $problemLines, string $problemQualityInfo):bool
{
    $created = false;
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare(
            "INSERT INTO mdl_uab_interactive_stusession(student_email, session_id, problem_id, number_lines_file, solution_quality, executed_times_count, teacher_executed_times_count) 
            VALUES (:student_email, :session_id, :problem_id, :number_lines_file, :solution_quality, :executed_times_count, :teacher_executed_times_count )"
        );

        $statement->execute(array(":student_email" => $email, ":session_id" => $session_id, ":problem_id" => $problemId,
            ":number_lines_file"=> $problemLines,  ":solution_quality" => $problemQualityInfo, ":executed_times_count" => 0, ":teacher_executed_times_count"=> 0));
        $statement->closeCursor();
        $connection = null;
        $created = true;

    } catch (Exception $e) {
        echo "Student_Session not created: " . $e->getMessage();
    }
    return $created;
}

function updateData(string $email, int $session_id, string $output, int $problemId, int $problemLines, string $problemQualityInfo):void
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("UPDATE mdl_uab_interactive_stusession SET executed_times_count = executed_times_count + 1  WHERE student_email=:student_email and session_id=:session_id and problem_id=:problem_id");
        $statement->execute(array(":student_email" => $email, ":session_id" => $session_id, ":problem_id"=>$problemId));

        $statement = $connection->prepare("UPDATE mdl_uab_interactive_stusession SET output =:output, number_lines_file=:number_lines_file, solution_quality=:solution_quality WHERE student_email=:student_email and session_id=:session_id and problem_id=:problem_id");
        $statement->execute(array(":student_email" => $email, ":session_id" => $session_id, ":output"=>$output,":number_lines_file"=> $problemLines, ":solution_quality" => $problemQualityInfo, ":problem_id"=>$problemId));
        $statement->closeCursor();
        $connection = null;
    } catch (Exception $e) {
        echo "Student_Session not created: " . $e->getMessage();
    }
}
function updateDataNoOutput(string $email, int $session_id, int $problemId, int $problemLines, string $problemQualityInfo):void
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("UPDATE mdl_uab_interactive_stusession SET number_lines_file=:number_lines_file, solution_quality=:solution_quality WHERE student_email=:student_email and session_id=:session_id and problem_id=:problem_id");
        $statement->execute(array(":student_email" => $email, ":session_id" => $session_id,":number_lines_file"=> $problemLines, ":solution_quality" => $problemQualityInfo, ":problem_id"=>$problemId));
        $statement->closeCursor();
        $connection = null;
    } catch (Exception $e) {
        echo "Student_Session not created: " . $e->getMessage();
    }
}
function teacherUpdatesStudentCode(string $email, int $session_id, string $output, int $problemId, int $problemLines, string $problemQualityInfo):void
{
    $connection = connectDBMoodle();
    $statement = $connection->prepare("UPDATE mdl_uab_interactive_stusession SET teacher_executed_times_count = teacher_executed_times_count + 1, output = :output, number_lines_file=:number_lines_file, solution_quality=:solution_quality WHERE student_email=:student_email and session_id=:session_id and problem_id=:problem_id");
    $statement->execute(array(":output"=>$output, ":student_email" => $email, ":session_id" => $session_id, ":output"=>$output,":number_lines_file"=> $problemLines, ":solution_quality" => $problemQualityInfo, ":problem_id"=>$problemId));
    $statement->closeCursor();
    $connection = null;
}
function getStudentsSessionExtraData(int $session_id, int $problemId):array
{
    try {
        $connection = connectDBMoodle();
        $statement = $connection->prepare("SELECT student_email, output, executed_times_count, teacher_executed_times_count, number_lines_file, solution_quality  FROM mdl_uab_interactive_stusession WHERE session_id=:session_id and problem_id=:problem_id");
        $statement->execute(array(":session_id" => $session_id, ":problem_id" => $problemId));
        $array_result = $statement->fetchAll(PDO::FETCH_ASSOC);
        $connection = null;

    } catch (PDOException $e) {
        echo 'Error en getStudentsSessionExtraData() : ' . $e->getMessage();
    }
    return $array_result;
}