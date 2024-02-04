<?php
$language = strtolower($_POST['language']);
$code = $_POST['code'];
$route = $_POST['route'];
$fileToExecute = $_POST['file_to_execute'];

$filePath = $route . "/" . $fileToExecute;
$programFile = fopen($filePath, "w");
fwrite($programFile, $code);
fclose($programFile);
$filePath = '"' . $route . "/" . $fileToExecute . '"';


if ($language == "python") {
    $output = shell_exec("python3 $filePath 2>&1");
    echo "<pre>";
    print_r($output);
    echo "</pre>";
} else if ($language == "cpp") {
    $dir = str_replace('\\', '/', realpath($route));
    print_r("Directorio: $dir \n");
    $files = scandir($dir);

    $filePath = "";
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        } else {
            print_r("Files: $file");
            $path = $dir . '/' . $file;
            if (is_file($path) && isset(pathinfo($path)['extension'])) {
                $ext = pathinfo($path)['extension'];
                if ($ext === "cpp") {
                    $filePath = $filePath . " " .  escapeshellarg($path);
                }
            }
        }
    }
    $random = substr(md5(mt_rand()), 0, 7);
    $outputFile = escapeshellarg("$dir/$random");
    shell_exec("g++ $filePath -O3 -Wall -o $outputFile");
    $result = shell_exec("$outputFile");
    unlink("$dir/$random");

    if(is_string($result)){
      echo "<pre>";
      print_r($result);
      echo "</pre>";
  }
  else {
    foreach ($result as $item_result) {

      print_r($item_result);
      echo "<br>";
      }
  }
}
