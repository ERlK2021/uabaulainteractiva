<?php
require_once __DIR__ . '/constants.php';
include_once __DIR__ . "/connection.php";


function runJupyterDocker($user): array {
    // Find a port that is not being used by any other container
    do {
        $port = rand(1024, 49151);
        exec("docker ps -la | findstr $port", $result);
    } while($result === "");

    // Create the container with the used and port values
    $scriptPath = realpath(__DIR__ . "/../jupyter/runJupyterDocker.bat");
    $handle = popen("start /B ". $scriptPath . ' ' . $user . ' ' . $port, "r");
    $containerId = fread($handle, 2096);
    pclose($handle);

    return array('containerId' => $containerId, 'containerPort' => $port);
}

function rmJupyterDocker($id) {
    $scriptPath = realpath(__DIR__ . "/../jupyter/rmJupyterDocker.bat");
    pclose(popen("start /B ". $scriptPath . ' ' . $id, "r"));
}
