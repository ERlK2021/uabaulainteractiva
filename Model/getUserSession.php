<?php
session_start();

echo json_encode(array('userType' => $_SESSION['user_type']));