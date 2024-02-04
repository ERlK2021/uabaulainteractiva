<?php
require_once __DIR__ . '/diskManager.php';

$solutionDir = urldecode($_POST['folder']);
$files = getDirectoryFiles($solutionDir);

echo '<nav class="navbar">';
echo '<ul class="navbar-nav">';
foreach ($files as $file) {
    $path = "$solutionDir/$file" ;
    $fileExtension = pathinfo($path)['extension'] ?? "";

    $navItem = "
    <li id='$path' onclick='openFile(this.id)' class='list-group-item file'>
        <div class='file'>
            $file
            <div class='file_extension'>
                <img src='/View/images/extensions/$fileExtension.svg' alt>
            </div>
            <button class='close_button' name='$path' data-toggle='modal' data-target='#delete_file_modal'>
                &times;
            </button>
        </div>
    </li>
    ";
    echo $navItem;
}
echo '</nav>';
