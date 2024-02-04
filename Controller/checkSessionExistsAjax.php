<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/session.php";

$sessionName = $_POST['session_name'];
return  sessionExists($sessionName);