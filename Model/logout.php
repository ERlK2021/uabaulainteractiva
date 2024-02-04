<?php
session_start();
session_destroy();
include_once __DIR__ . "/../Model/redirectionUtils.php";

redirectLocation();
