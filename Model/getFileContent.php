<?php
$file = urldecode($_POST['file']);
echo file_get_contents($file);
