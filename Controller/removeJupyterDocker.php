<?php
include_once __DIR__ . "/../Model/dockerUtils.php";
session_start();

$containerId = $_SESSION['containerId'];
$_SESSION['containerUsages'] -= 1;

if ($_SESSION['containerUsages'] == 0) {
    rmJupyterDocker($containerId);
    unset($_SESSION['containerId']);
    unset($_SESSION['containerPort']);
    unset($_SESSION['containerUsages']);
}
