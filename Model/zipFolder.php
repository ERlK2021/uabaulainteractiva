<?php


// WARNING
// This code should NOT be used as is. It is vulnerable to path traversal. https://www.owasp.org/index.php/Path_Traversal
// You should sanitize $_GET['directtozip']
// For tips to get started see http://stackoverflow.com/questions/4205141/preventing-directory-traversal-in-php-but-allowing-paths
session_start();
include_once __DIR__ . "/connection.php";
$connexio = connectDB();

$id = $_POST['id'];
try {
    $stmt = $connexio->prepare("SELECT * FROM problem WHERE id= :mail");
    $stmt->execute(array(":mail" => $id));
    $data = $stmt->fetch(PDO::FETCH_ASSOC); //guardamos en la variable data

    $stmt = $connexio->prepare('DELETE FROM problem WHERE id = :mail');
    $stmt->execute(array(":mail" => $id));

    $connexio = null;
} catch (PDOException $e) {
    echo 'Error al fer log-in' . $e->getMessage();
}

//Get the directory to zip
// Get real path for our folder
$rootPath = realpath($data['Ruta']);

// Initialize archive object
$zip = new ZipArchive();
$zip->open('file.zip', ZipArchive::CREATE | ZipArchive::OVERWRITE);

// Create recursive directory iterator
/** @var SplFileInfo[] $files */
$files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($rootPath),
    RecursiveIteratorIterator::LEAVES_ONLY
);

foreach ($files as $name => $file) {
    // Skip directories (they would be added automatically)
    if (!$file->isDir()) {
        // Get real and relative path for current file
        $filePath = $file->getRealPath();
        $relativePath = substr($filePath, strlen($rootPath) + 1);

        // Add current file to archive
        $zip->addFile($filePath, $relativePath);
    }
}

// Zip archive will be created only after closing object
$zip->close();
$filename = "file.zip";
if (file_exists($filename)) {
    //Define header information
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header("Cache-Control: no-cache, must-revalidate");
    header("Expires: 0");
    header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
    header('Content-Length: ' . filesize($filename));
    header('Pragma: public');

    //Clear system output buffer
    flush();

    //Read the size of the file
    readfile($filename);

    //Terminate from the script
    die();
}
