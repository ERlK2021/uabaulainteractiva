<?php
session_start();

$id = $_POST['id'];
if (is_dir($id)) {
    echo -1;
} else {
    unlink($id);
}
