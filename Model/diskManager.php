<?php
include_once __DIR__ . '/exceptions.php';
include_once __DIR__ . '/constants.php';

function createProblemDirectory(int $subjectId, string $directoryName): string {
    $subjectDirectory = "./../app/problemes/$subjectId";
    $directory = "$subjectDirectory/$directoryName";
    if (!file_exists($subjectDirectory)) {
        mkdir($subjectDirectory);
    }
    if (file_exists($directory)) {
        throw new DirectoryAlreadyExists($directory);
    }
    mkdir($directory);
    return $directory;
}

function createProblemFile(string $directory, string $fileName, string $content) {
    $extension = pathinfo($fileName, PATHINFO_EXTENSION);
    if (!in_array($extension, ALLOWED_FILE_EXTENSIONS)) {
        throw new WrongFileExtension($fileName);
    }

    $filePath = "$directory/$fileName";
    $file = fopen($filePath, 'w');
    fwrite($file, base64_decode($content));
    fclose($file);
}

function getDirectoryFiles(string $directory): array {
    // Get all items
    $realDirectory = str_replace("\n", "", shell_exec("realpath " . escapeshellarg($directory)));
    $directoryItems = scandir($realDirectory);
    // Remove the . and ..
    $dotsRemovedItems = array_diff($directoryItems, array('..', '.'));
    // Remove the directories
    return array_filter($dotsRemovedItems, fn($item) => !is_dir(realpath("$directory/$item")));
}
