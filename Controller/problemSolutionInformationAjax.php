<?php
include_once __DIR__ . "/../Model/connection.php";
include_once __DIR__ . "/../Model/problemNew.php";

$problemId = $_POST['problemId'];
$directory = $_POST['route'];

print_r($problemId);
print_r($directory);

$files_scanned_directory = array_diff(scandir($directory), array('..', '.','__pycache__'));
$file_text=[];

foreach ($files_scanned_directory as $file) {
    if(!str_contains($file,'.txt')) { //Avoid .txt files
      $file_text_dict = file($directory . "/" . $file); //Returns the file in an array. Each element of the array corresponds to a line in the file. Upon failure, file() returns false.
  
      if($file_text_dict) {
          $trim_file_text =  array_values(array_filter($file_text_dict , "trim"));
          foreach ($trim_file_text as $k => $v) {
              array_push($file_text, $v);
          }
      }
      else{
          echo "Error leyendo el fichero: " . $file;
      }
    }
}
$problemLines = count($file_text);
$str_file_text = implode($file_text);
$problemQualityInfo = [substr_count($str_file_text ,'if ') + substr_count($str_file_text ,'if(')
    , substr_count($str_file_text ,'for ')+ substr_count($str_file_text ,'for('),
    substr_count($str_file_text ,'while ') + substr_count($str_file_text ,'while('),
    substr_count($str_file_text ,'switch ') + substr_count($str_file_text ,'switch(')];

print_r($problemId);
print_r($problemLines);
print_r(implode($problemQualityInfo));

addProblemExtraData($problemId, $problemLines, implode($problemQualityInfo));//Visibility by default = Private
