<?php

function problemTitleExists($title): bool
{
    $exists = True;
    try {
        $connection = connectDB();
        $statement = $connection->prepare("SELECT COUNT(*) FROM problem WHERE title= :title");
        $statement->execute(array(":title" => $title));
        $count = $statement->fetchColumn();
        $connection = null;

        $exists = $count != 0;
    } catch (PDOException $e) {
        echo 'Error looking for the problem with the specified title: ' . $e->getMessage();
    }

    return $exists;
}

function createProblem($route, $title, $description, $max_memory_usage, $visibility, $max_execution_time,
                       $language, $subject, $entregable, $deadline) : int
{
    $problemId = -1;
    try {
        $connection = connectDB();

        $statement = $connection->prepare(
            "INSERT INTO problem (route, title, description, visibility, memory, time, language, subject_id,
                     edited, entregable, deadline)
            VALUES (:route, :title, :description, :visibility, :max_memory_usage, :max_execution_time,
                    :programming_language, :subject, :edited, :entregable, :deadline)"
        );

        $statement->execute(array(":route" => $route, ":title" => $title, ":description" => $description,
                ":visibility" => $visibility, ":max_memory_usage" => $max_memory_usage, ":edited" => 0,
                ":max_execution_time" => $max_execution_time, ":programming_language" => $language, ":subject" => $subject, ":entregable" => $entregable, ":deadline" => $deadline)
        );

        $problemId = $connection->lastInsertId();
        $connection = null;
    } catch (Exception $e) {
        echo "Error creating the problem: " . $e->getMessage();
    }
    return $problemId;
}
function addProblemTeacherSolutionRoute(int $problemId, string $route):bool
{
    try{

        $connection = connectDB();
        $statement = $connection->prepare("UPDATE problem SET route_solution=:route_solution WHERE id=:problemId");
        $statement->execute(array(":route_solution" => $route, ":problemId" => $problemId));

    }catch (Exception $e) {
        echo "Error uploading the solution of the problem: " . $e->getMessage();
        return false;
    }
    return true;
}

function addProblemExtraData(int $problemId, int $numerLines, string $quality): bool
{
    try{
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE problem SET solution_lines=:solution_lines, solution_quality=:solution_quality, visibility=:private WHERE id=:problemId");
        $statement->execute(array(":solution_lines" => $numerLines, ":solution_quality" => $quality, ":private"=>'private', ":problemId"=>$problemId));

    }catch (Exception $e) {
        echo "Error uploading the solution of the problem: " . $e->getMessage();
        return false;
    }
    return true;
}
function changeSolutionVisibility(int $problemId, string $visibility):bool
{
    try{
        $connection = connectDB();
        $statement = $connection->prepare("UPDATE problem SET solution_visibility=:visibility WHERE id=:problemId");
        $statement->execute(array(":visibility"=> $visibility, ":problemId"=>$problemId));

    }catch (Exception $e) {
        echo "Error changing the visibility of the problem: " . $e->getMessage();
        return false;
    }
    return true;
}