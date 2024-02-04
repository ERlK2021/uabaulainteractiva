<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/session.php";

$class_group = $_POST['class_group'];
deleteGroupSessions(class_group:$class_group);
