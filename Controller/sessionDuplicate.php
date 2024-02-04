<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/session.php";

$sessionId = $_POST['session_id'];
$sessionName = $_POST['session_name'];

duplicateSession(sessionName: $sessionName, sessionId: $sessionId);
