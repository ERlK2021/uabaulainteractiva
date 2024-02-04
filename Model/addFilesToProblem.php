<?php

include_once __DIR__ . '/exceptions.php';
include_once __DIR__ . '/constants.php';

function uploadFile(string $fileExtension, int $i, string $route, mixed $tmpFilePath, array $files): void
{
    if (!in_array($fileExtension, ALLOWED_FILE_EXTENSIONS)) {
        throw new WrongFileExtension($files['file']['name'][$i]);
    } else if ($files["file"]["size"][$i] > 500000) {
        throw new FileTooLarge($files['file']['name'][$i], $files["file"]["size"][$i],500000);
    }

    //Set up our new file path
    $newFilePath = $route . "/" . $files['file']['name'][$i];
    //File is uploaded to temp dir
    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
        echo "The file " . htmlspecialchars(basename($files["file"]["name"][$i])) . " has been uploaded.";
    }
    else {
        // file was not moved
        echo "Error: File " . htmlspecialchars(basename($files["file"]["name"][$i])) . " could not be uploaded.";
    }
}

function uploadFileCopy(string $fileExtension, int $i, string $route, mixed $tmpFilePath, array $files): void
{
    if (!in_array($fileExtension, ALLOWED_FILE_EXTENSIONS)) {
        throw new WrongFileExtension($files['file']['name'][$i]);
    } else if ($files["file"]["size"][$i] > 500000) {
        throw new FileTooLarge($files['file']['name'][$i], $files["file"]["size"][$i],500000);
    }

    //Set up our new file path
    $newFilePath = $route . "/" . $files['file']['name'][$i];
    //File is uploaded to temp dir
    if (copy($tmpFilePath, $newFilePath)) {
        //echo "The file " . htmlspecialchars(basename($files["file"]["name"][$i])) . " has been uploaded.";
    }
}

function uploadFiles(string $route, array $files)
{
    # Count the number of uploaded files in array
    $files_count = count($files['file']['name']);

    for ($i = 0; $i < $files_count; $i++) {
        //The temp file path is obtained
        $tmpFilePath = $files['file']['tmp_name'][$i];
        //A file path needs to be present
        $tmpFileName = basename($files["file"]["name"][$i]);
        $fileExtension = strtolower(pathinfo($tmpFileName, PATHINFO_EXTENSION));
        uploadFile($fileExtension, $i, $route, $tmpFilePath, $files);
    }
}

function uploadFilesCopy(string $route, array $files)
{
    # Count the number of uploaded files in array
    $files_count = count($files['file']['name']);

    for ($i = 0; $i < $files_count; $i++) {
        //The temp file path is obtained
        $tmpFilePath = $files['file']['tmp_name'][$i];
        //A file path needs to be present
        $tmpFileName = basename($files["file"]["name"][$i]);
        $fileExtension = strtolower(pathinfo($tmpFileName, PATHINFO_EXTENSION));
        uploadFileCopy($fileExtension, $i, $route, $tmpFilePath, $files);
    }
}