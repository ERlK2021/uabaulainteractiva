<?php
$language = strtolower($_POST['language']);
$code = $_POST['code'];
$route = $_POST['route'];
$fileToExecute = $_POST['file_to_execute'];

$filePath = $route . "/" . $fileToExecute;
$previus_content = null;
$previus_filePath = $filePath;
if(isset($_POST['solution'])&& isset($_POST['userType'])){
    if($_POST['userType'] == 1){ //STUDENT

        $programFile = fopen($filePath, "r");
        $previus_content = fread($programFile, filesize($filePath));
        fclose($programFile);
    }
}

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
    //print_r("Directorio: $dir \n");
    $files = scandir($dir);

    $filePath = "";
    foreach ($files as $file) {
        if ($file === '.' || $file === '..') {
            continue;
        } else {
            //print_r("Files: $file");
            $path = $dir . '/' . $file;
            if (is_file($path) && isset(pathinfo($path)['extension'])) {
                $ext = pathinfo($path)['extension'];
                if ($ext === "cpp") {
                    $filePath = $filePath . " " .  escapeshellarg($path);
                }
            }
        }
    }
    
    $outputFile = escapeshellarg("$dir/result");
    shell_exec("g++ $filePath -O3 -Wall -o $outputFile 2>&1"); 
    
    $dir = str_replace(" ", "\ ",$dir);//Si algun directorio en la ruta tiene espacios da problemas, lo corregimos con esta instrucci�n.
    
    //$pwd = shell_exec("pwd"); print_r("Pwd es: $pwd");
    $result = shell_exec("cd $dir && $outputFile && rm $outputFile"); //Nos movemos al directorio $dir, ya que es donde se ha generado el ejecutable y estan los .txt necesarios, ejecutamos el ejecutable($outputFile) y una vez ejecutado su output se guarda en $result y ya podemos borrarlo para que no estorbe en el directorio.
    
    if(is_null($result)){//Si se han producido errores en el codigo $result devuelve null.
      exec("g++ $filePath -O3 -Wall -o $outputFile 2>&1", $result);
    }
    echo "<pre>";
    if(is_string($result)){
      print_r($result);
    }
    else {
      foreach ($result as $item_result) {
        print_r($item_result);
        echo "<br>";
      }
    }
  echo "</pre>";
}
if(isset($_POST['solution'])&& isset($_POST['userType'])){
    if($_POST['userType'] == 1){ //STUDENT
        $programFile = fopen($previus_filePath, "w");
        fwrite($programFile, $previus_content);
        fclose($programFile);
    }
}