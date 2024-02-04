<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/session.php";

if(isset($_POST['sessionId'])){
    deleteSession($_POST['sessionId']);
}
if(isset($_POST['session_name'])){
    deleteAllSessionsByName(urldecode($_POST['session_name']));
}