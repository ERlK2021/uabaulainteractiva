<?php if (!isset($problem)) {
    redirectLocation();
} ?>
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
    <script src="/View/js/editor.js"></script>
    <script src="/View/js/global.js"></script>
</head>

<body class="d-flex flex-column min-vh-100">

<?php if (!empty($folder_route)) { ?>
    <p id="folder_route" hidden><?php echo $folder_route ?></p>
<?php } ?>

<div class="container-fluid">
    <?php if ((!empty($solution)) && ($solution["edited"] == 1)) { ?>
        <div class="alert alert-info " id="edition_msg">
            <p>Vols obtenir els canvis del professor?</p>
            <div class="alert-buttons">
                <button type="button" class="btn" data-toggle="modal" data-target="#get_changes_modal">Si</button>
                <button type="button" class="btn" data-dismiss="alert">No</button>
            </div>
        </div>
    <?php } ?>

    <?php if ($_SESSION['user_type'] == PROFESSOR && isset($_GET["edit"])) { ?>
    <p class="alert alert-warning" id="error_msg"><strong> Estas modificant el problema arrel. El problema arrel és la versió que els alumnes han d'omplir </strong>
        <?php } ?>

        <?php if (isset($_GET["uploaded"])) { ?>
        <?php $negation = $_GET["uploaded"]? "": "no"; ?>
    <p class="alert alert-warning"><strong> <?php echo "El problema $negation s'ha pujat a GitHub." ?> </strong>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </p>
<?php } ?>

    <p class="alert alert-warning" hidden id="root_modified"><strong>El professor està editant </strong></p>
    <p class="alert alert-danger" hidden id="error_msg_libraries"><strong>Les llibreries que s'estàn utilitzant no estàn
            soportades </strong>
        <button id="error_msg_libraries_btn" type="button" class="close">&times;</button>
    </p>
    <p class="text-center font-weight-bold problem-title"><?php echo $problem["title"]; ?></p>

    <?php if($_SESSION['user_type'] == STUDENT && $entregable == "on"){
        if($deadline != null){
            if ($currentDate > $deadline) {?>
                <button id="alert" class="btn" title="alert" style="margin:10px 0px 10px 0px">
                    <img class="icon" src="/View/images/alert.png">
                </button>
            <?php }
            echo "<br>";
            echo "La data limit d'aquest problema entregable és: &nbsp $deadline";
            echo "<br>";
            //If deliverable date has expired show alert
        }else {
            echo "Problema entregable,&nbsp no té data limit.";
        }
    } ?>
</div>

<div id="editor-container" class="container-fluid">
    <p id="programming_language" hidden><?php echo $problem["language"]; ?></p>

    <div class="editor-sub-container">
        <button id="execute" class="btn" onclick="executeCode('<?php echo "{$_SESSION['email']}"?>', <?php if(isset($_GET['session'])){echo $_GET['session'];} else{ ?> '<?php echo "NO"; ?>' <?php  }?>, <?php echo $_SESSION['user_type']?>, '<?php echo "{$_GET['user']}"?>', '<?php echo $entregable ?>','<?php echo $deadline ?>', '<?php echo $currentDate?>')" title="Executar">
            <img class="icon" src="/View/images/execute.png" alt="Executar">
        </button>
        <?php if($problem["description"]) { ?>
            <button id="show-description" type="button" class="btn">
                <img class="icon" src="/View/images/description.png" alt="Descripció">
            </button>
        <?php } ?>
        <button class="btn add-object" data-toggle="modal" data-target="#add_file_modal" title="Afegit fitxer">
            <img class="icon" src="/View/images/file.png" alt="Afegit fitxer">
        </button>
        <button id="github-add-file" class="btn github" data-toggle="modal" data-target="#github-form-modal"
                title="Afegir fitxer desde GitHub">
            <img class="icon" src="/View/images/file.png" alt="Afegir fitxer desde GitHub">
        </button>
        <button id="save" class="btn" onclick="save()" title="Guardar">
            <img class="icon" src="/View/images/save.png" alt="Guardar"/>
        </button>
        <button id="github-upload" class="btn github" data-toggle="modal" data-target="#github-form-modal"
                title="Pujar a GitHub">
            <img class="icon github" src="/View/images/save.png" alt="Pujar a GitHub">
        </button>

        <?php if ($_SESSION['user_type'] == PROFESSOR && !isset($_GET['edit'])) {?>

            <button type="button" class="btn" title="Editar problema arrel" onclick="window.location.href='<?php echo "/index.php?query=Editor Problemas&problem=".$_GET['problem']."&edit=1"; ?>'">
                <img class="icon" src="/View/images/edit-source.png" alt="editar codi arrel">
            </button>

        <?php } ?>

        <?php if (($_SESSION['user_type'] == STUDENT && $teacher_solution_visibility == 'Public') || $_SESSION['user_type'] == PROFESSOR) {?>

            <button type="button" class="btn" title="Veure solució" onclick="window.location.href='<?php if(isset($_GET['session'])){echo "/index.php?query=Solucio Problema&problem=".$_GET['problem']."&session=".$_GET['session'];}else{ echo "/index.php?query=Solucio Problema&problem=".$_GET['problem']; }?>'">
                <img class="icon" src="/View/images/view_solution.png" alt="veure solucio">
            </button>
        <?php } ?>

        <?php if ($entregable == 'on' && $_SESSION['user_type'] == STUDENT ) {?>

            <button type="button" class="btn" title="grade" id ="grade">
                <?php echo "Grade: $grade";?>
            </button>
        <?php } ?>

        <?php if($problem["description"]) { ?>
            <div class="content"><p><?php echo htmlspecialchars($problem["description"]); ?></p></div>
        <?php } ?>

        <div id="files" class="mt-1"></div>

        <div id="editor" onclick="
        <?php

        ?>


">

        </div>

        <div id="notebook"></div>
        <div id="answer"></div>
    </div>

    <!-- Código de mensajes reemplazado con este comentario -->

    <?php if ($_SESSION['user_type'] == PROFESSOR) { ?>
        <div class="students-list">
            <ul>
                <?php if (!empty($students)) { ?>
                    <div class="students-list-header">
                        <?php if (isset($_GET["view-mode"])) { ?>
                            <a class="btn"
                               href="<?php echo"/index.php?query=Editor Problemas&problem=".$_GET['problem']."&session=".$_GET['session']?>">
                                &#8592;
                            </a>
                        <?php } ?>
                        <h4>Estudiants</h4>
                    </div>
                    <?php foreach ($_students as $student) { ?>
                        <li <?php echo $_GET['user'] === $student['user']? "class='selected'": "" ?>>
                            <a href="<?php echo "/index.php?query=Editor Problemas&problem=".$_GET['problem']."&view-mode=1&user=".
                                $student["user"]."&session=".$_GET['session'] ?>"
                               class="btn email" id ="btn-eamail"><?php echo $student["user"] ?></a>
                            <a href="<?php echo "/index.php?query=Editor Problemas&problem=".$_GET['problem']."&view-mode=2&user=".
                                $student["user"]."&session=".$_GET['session'] ?>"
                               class="btn view" title="Veure"><i class="fas fa-eye"></i></a>
                            <a class = "btn showPro" id ="showPro" title="Informació extra"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/> </svg></a>
                        </li>
                        <h6 class="follow_up_student_info">
                            <li class = "executed_count">Execucions alumne:  <?php echo $student["executed_times_count"]?></li><hr />
                            <li class = "teacher_executed_count">Execucions tutor:  <?php echo $student["teacher_executed_times_count"]?></li><hr />
                            <li class = "solution_lines">Linies solució:  <?php echo !is_null($student["lines_percentage"])? $student["number_lines_file"] . " ≈ " . $student["lines_percentage"] . "%" : $student["number_lines_file"] ?></li><hr />
                            <li class = "table_code_quality">
                                <table>
                                    <caption style="caption-side:top; text-align: center; padding-top:0px; color:inherit">Qualitat del codi</caption>
                                    <tr>
                                        <th> Statements </th>
                                        <th> Estudiant </th>
                                        <th> Professor </th>
                                        <?php
                                        $statements_student = explode("-", $student["solution_quality"]);
                                        $statements_teacher = explode("-", $official_solution_quality);
                                        ?>
                                    </tr>
                                    <tr>
                                        <td>If</td>
                                        <td class="if_student"><?php echo $statements_student[0]?></td>
                                        <td><?php echo $statements_teacher[0]?></td>
                                    </tr>
                                    <tr>
                                        <td>For</td>
                                        <td class="for_student"><?php echo $statements_student[1]?></td>
                                        <td><?php echo $statements_teacher[1]?></td>
                                    </tr>
                                    <tr>
                                        <td>While</td>
                                        <td class="while_student"><?php echo $statements_student[2]?></td>
                                        <td><?php echo $statements_teacher[2]?></td>

                                    </tr>
                                    <tr>
                                        <td>Switch</td>
                                        <td class="switch_student"><?php echo $statements_student[3]?></td>
                                        <td><?php echo $statements_teacher[3]?></td>

                                    </tr>
                                </table>
                            </li><hr />
                            <p>Output:</p> <span class= "extra"> <?php echo $student["output"]?></span>
                        </h6>
                    <?php }
                } ?>
            </ul>
        </div>

        <script>
            window.setInterval(refreshListOnlineStudents, 4000);// To update teachers' sesion page if a new student has join the session.
        </script>
    <?php } ?>
    <?php if($_SESSION['user_type'] == STUDENT && $entregable == "on"){?>
        <script>
            /*TODO: el alumno podrá editar ya que parece que hay un error con esta funcionalidad (se bloquea aunque no se esté editando),
            asi que provisionalmente se ha modificado*/
          window.setInterval(doNotEditMain,2000);
        </script>
    <?php } ?>
</div>

<!--  MODALS -->
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
<div id="add_file_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Importa o crea un nou fitxer</h4>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <div class="modal-buttons">
                    <form id="2" action="/Controller/addFileFromPC.php" method="post" enctype="multipart/form-data">
                        <button type="submit" onclick="receiveFile2()" class="btn" data-dismiss="modal">
                            Importar
                        </button>
                        <input id="new_file2" type="file" name="file[]" hidden multiple>
                        <input type="hidden" name="solution_path" value="<?php echo $folder_route?? ""; ?>"/>
                    </form>
                    <button type="button" class="btn" onclick="newFile()" data-dismiss="modal">
                        Crear nou
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="get_changes_modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
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
                        <button type="button" class="btn" id="<?php echo $_GET['problem']; ?>"
                                onclick="acceptChanges(this.id)" data-dismiss="modal">
                            Obtenir canvis
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="github-form-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="modal-header">
                    <h4 id="github-from-modal-title" class="modal-title"></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="github-form" method="post" action="/Controller/githubAddOrUploadFiles.php">
                        <div class="input-container">
                            <input id="repo_link" class="input" type="url" name="repo_link" placeholder=" " required/>
                            <div class="cut"></div>
                            <label for="repo_link" class="placeholder ">Link del repositori GitHub</label>
                        </div>
                        <div class="modal-buttons">
                            <button type="button" class="btn" data-dismiss="modal">
                                Cancelar
                            </button>
                            <input id="github-form-submit-input" class="btn" type="submit"/>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

</body>