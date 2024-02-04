<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/html">
<head>
    <meta charset="UTF-8">
    <title>TFG - Editor</title>

    <link rel="shortcut icon" href="/View/images/favicon.png">
    <link rel="stylesheet" href="/View/css/external/w3.css">
    <link rel="stylesheet" href="/View/css/external/bootstrap.min.css">
    <link rel="stylesheet" href="/View/css/external/bootstrap-toggle.min.css">
    <link rel="stylesheet" href="/View/css/external/all.css">
    <link rel="stylesheet" href="/View/css/forms.css"/>
    <link rel="stylesheet" href="/View/css/style.css"/>
    <link rel="stylesheet" href="/View/css/editor.css"/>

    <script src="/View/js/external/jquery.min.js"></script>
    <script src="/View/js/external/popper.min.js"></script>
    <script src="/View/js/external/bootstrap.min.js"></script>
    <script src="/View/js/external/bootstrap-toggle.min.js"></script>
    <script src="/View/js/external/all.min.js"></script>
    <script src="/View/js/external/editor/ace.js"></script>
    <script src="/View/js/external/editor/theme-monokai.js"></script>
    <script src="/View/js/editor2.js"></script>
    <script src="/View/js/global.js"></script>
</head>

<body class="d-flex flex-column min-vh-100" <?php echo $_SESSION['theme'] ?>>

<?php if (!empty($folder_route)) { ?>
    <p id="folder_route" hidden><?php echo $folder_route ?></p>
<?php } ?>
<div class="container-fluid">
    <p class="text-center font-weight-bold problem-title"><?php echo $problem["title"] . " - Solució"; ?></p>
</div>

<div id="editor-container" class="container-fluid">
    <p id="programming_language" hidden><?php echo $problem["language"]; ?></p>

    <div class="editor-sub-container">
        <button id="back" class="btn" onclick="window.location.href='<?php if(isset($_GET['session'])){ echo "/index.php?query=Editor Problemas&session=".$_GET['session']."&problem=".$_GET['problem'];} else{ echo "/index.php?query=Editor Problemas&problem=".$_GET['problem'];}?>'">
            <img class="icon" src="/View/images/back.png" alt="Enrere">
        </button>
        <button id="execute" class="btn" onclick="executeCode('<?php echo "{$_SESSION['email']}"?>', <?php if(isset($_GET['session'])){echo $_GET['session'];} else{ ?> '<?php echo "NO"; ?> ' <?php  }?>, <?php echo $_SESSION['user_type']?>, '<?php echo "{$_GET['user']}"?>')" title="Executar">
            <img class="icon" src="/View/images/execute.png" alt="Executar">
        </button>
        <?php if($problem["description"]) { ?>
            <button id="show-description" type="button" class="btn" >
                <img class="icon" src="/View/images/description.png" alt="Descripció">
            </button>
        <?php } ?>
        <?php if ($_SESSION['user_type'] == PROFESSOR) { ?>

            <button id="save" class="btn" onclick="save()" title="Guardar">
                <img class="icon" src="/View/images/save.png" alt="Guardar"/>
            </button>

            <button class="btn add_solution" data-toggle="modal" data-target="#PS" title="Importar fitxers a Solució">
                <img class="icon" src="/View/images/file-add.png" alt="Afegit fitxer">
            </button>
            <button class="btn change_visibility" type="button" data-placement="top" title="Canviar visibilitat">
                <img class="icon" src="<?php if($visibility == "Public"){echo "/View/images/visible.png";} else{ echo "/View/images/not-visible.png"; } ?>" alt="Canviar visibilitat" title="Canviar visibilitat">
            </button>
        <?php } ?>
        <?php if($problem["description"]) { ?>
            <div class="content"><p><?php echo htmlspecialchars($problem["description"]); ?></p></div>
        <?php } ?>

        <div id="files" class="mt-1"></div>
        <div id="editor" onclick="<?php if($_SESSION['user_type'] == STUDENT){ echo "disableEdit()";}?>"></div>
        <div id="notebook"></div>
        <div id="answer"></div>
    </div>
</div>

<!--  MODALS -->
<!-- NUEVO Start -->
<div id="PS" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Importa Solució Problema</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <form id="1" action="/Controller/UploadSolutionFile.php" method="post" enctype="multipart/form-data">
                    <button type="submit" onclick="receiveFile1()" class="btn" data-dismiss="modal">
                        Importar
                    </button>
                    <input id="new_file1" type="file" name="file[]" hidden multiple>
                    <input type="hidden" name="solution_path" value="<?php echo $folder_route?? ""; ?>"/>
                    <input type="hidden" name="query" value="<?php echo $_GET['query'] ?>"/>
                    <input type="hidden" name="session" value="<?php if(isset($_GET['session'])){echo $_GET['session'];} else { echo NULL;}?>"/>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- NUEVO End -->
<?php if ($_SESSION['user_type'] == PROFESSOR) { ?>
    <div id="delete_file_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="modal-header">
                        <h4 class="modal-title">Estàs segur?</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>
                    <div class="modal-body">
                        <p>L'operació serà immediata i sense possibilitat de retorn.</p>
                        <div class="modal-buttons">
                            <button type="button" class="btn" data-dismiss="modal">
                                Cancelar
                            </button>

                            <button type="button" class="btn" onclick="deleteFile()" data-dismiss="modal">
                                Esborrar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php } ?>

</body>
