let editor;
let currentDocumentPath = "";
let programmingLanguage;
let folderRoute;
let checkChangesInterval;
let readOnly = false;
let toDeleteFileRoute;
let editing = 0;
let problemId;
let sessionId;
let userType;
let viewMode = null;
let containerPort;


$(document).ready(function () {

    $(".showPro").click(function () {

        var more =$(this).parent().next();
        if(more.css("display") === "none"){
            more.css("display","block");
            var button = $(this);
            button.children().replaceWith('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-minus" viewBox="0 0 16 16"><path d="M11 8H4V7H11V8Z"/> </svg>');

            let email = $(this).siblings('a').first().text();
            let std_executions = $(this).parent().next('.follow_up_student_info').children(".executed_count");
            let std_output = $(this).parent().next('.follow_up_student_info').find(".extra");
            //console.log(std_output);
            let number_lines = $(this).parent().next('.follow_up_student_info').children(".solution_lines");
            let table_code_quality = $(this).parent().next('.follow_up_student_info').children('.table_code_quality');

            let if_student = table_code_quality.find(".if_student");
            let for_student = table_code_quality.find(".for_student");
            let while_student = table_code_quality.find(".while_student");
            let switch_student = table_code_quality.find(".switch_student");

            $.ajax({
                url: "/Controller/online_v_i_show_Ajax.php",
                method: "POST",
                data: {
                    email: email,
                    id: sessionId,
                    problemId: problemId,
                },
                success: function (response) {

                    let json = JSON.parse(response);

                    let number_lines_file = json['number_lines_file'];
                    let solution_quality = json['solution_quality'];
                    let student_lines_percentage = json['student_lines_percentage'];
                    let output_student = json['student_output'];
                    let student_executions= json['student_executions'];


                    if (student_lines_percentage === null){
                        number_lines.text("Linies solució: " + number_lines_file);
                    }
                    else {
                        number_lines.text("Linies solució: " + number_lines_file + " ≈ " + student_lines_percentage + "%");
                    }

                    std_executions.text("Execucions alumne: " + student_executions);
                    std_output.text('');
                    std_output.append(output_student);

                    if_student.text(solution_quality[0]);
                    for_student.text(solution_quality[1]);
                    while_student.text(solution_quality[2]);
                    switch_student.text(solution_quality[3]);


                }
            });

        }else{
            more.css("display","none");
            var button = $(this);
            button.children().replaceWith('<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-plus" viewBox="0 0 16 16"> <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/> </svg>');
        }

    });

    if (ace === undefined) {
        return false;
    }
    // Customize the editor theme
    editor = ace.edit("editor", {
        enableBasicAutocompletion: true,
        enableSnippets: true,
        enableLiveAutocompletion: false,
    });
    editor.setFontSize(16);
    const observer = new MutationObserver((mutationsList) => {
        for (const mutation of mutationsList) {
            if (mutation.type !== "attributes" || mutation.attributeName !== "class") {
                return
            }
            let theme = document.body.classList.contains('dark-theme')? 'one_dark': 'chrome';
            editor.setTheme(`ace/theme/${theme}`);
        }
    });

    observer.observe(document.body, { attributes: true });

    const queryString = window.location.search;
    const urlParams = new URLSearchParams(queryString);
    problemId = urlParams.get('problem');
    sessionId = urlParams.get('session');
    editing = (urlParams.get('edit') !== null);

    let keys = {};
    window.addEventListener("keydown", function (event) {
        keys[event.code] = true;
        if ((event.ctrlKey || event.metaKey) && keys["KeyS"]) {
            event.preventDefault();
            console.log(addEventListener());
            save();
        }
    });

    // Load the files from the disk
    folderRoute = document.getElementById("folder_route").innerText;
    openFolder(folderRoute);

    // Get the session data
    $.ajax({
        url: "/Model/getUserSession.php",
        async: false,
        success: function (response) {
            let json = JSON.parse(response);
            userType = json['userType'];
            //containerPort = json['containerPort'];
        }
    })
    // Set the auto check options depending on the user and his actions
    // User type 1 is student and 0 professor
    if (userType === 1) {
        setInterval(checkChanges, 2000);
    } else if (userType === 0) {
        viewMode = urlParams.get('view-mode');
        // View mode 1 is edit mode and 2 read only
        if (viewMode === "1") {
            setInterval(save, 3000);
        } else if (viewMode === "2") {
            editor.setReadOnly(true);
        }
    }

    // Set the programming language
    let language = document.getElementById("programming_language").innerText;
    if (language === 'C++') {
        programmingLanguage = "cpp";
        editor.session.setMode("ace/mode/c_cpp");
    } else if (language === 'Python') {
        programmingLanguage = "python";
        editor.session.setMode("ace/mode/python");
    }

    // Customize the form modal depending on the button opening it
    $("#github-form-modal").on('shown.bs.modal', function (event) {
        let title, submitText, action;
        if (event.relatedTarget.id === 'github-upload') {
            title = "Pujar a GitHub";
            submitText = "Pujar";
        } else {
            title = "Afegir fitxers desde GitHub";
            submitText = "Afegir";
        }
        $('#github-from-modal-title').text(title);
        $('#github-form-submit-input').attr('value', submitText);
    });
    // Add additional fields to the upload files to GitHub form
    $("#github-form").submit( function(eventObj) {
        save();
        $("<input />").attr("type", "hidden")
            .attr("name", "solution_path")
            .attr("value", folderRoute)
            .appendTo(this);
        $("<input />").attr("type", "hidden")
            .attr("name", "problem_id")
            .attr("value", problemId)
            .appendTo(this);
        let uploadFiles = $('#github-form-submit-input').attr('value') === "Pujar";
        $("<input />").attr("type", "hidden")
            .attr("name", "upload_files")
            .attr("value", uploadFiles)
            .appendTo(this);
        return true;
    });

    // Set the to delete file when clicking the delete file cross
    $('#delete_file_modal').on('show.bs.modal', function (e) {
        let invoker = $(e.relatedTarget);
        toDeleteFileRoute = invoker.attr('name');
    });

    if ('onbeforeunload' in window) {
        window.addEventListener('beforeunload', exitFunction, false);
    } else if ('onpagehide' in window) {
        window.addEventListener('pagehide', exitFunction, false);
    } else {
        window.addEventListener('unload', exitFunction, false);
    }

    $('#show-description').on('click', function () {
        let content = $(this).siblings('.content')[0];
        if (content.style.display === "block") {
            content.style.display = "none";
        } else {
            content.style.display = "block";
        }
    })

    $('#error_msg_libraries_btn').on('click', function () {
        let alert = document.getElementById('error_msg_libraries');
        $(alert).attr("hidden", "");
    })
    //Alert message when a deliverable problem deadline has expired
    $('#alert').on('click', function () {

        if($('#alertMessage').length){
            $('#alertMessage').fadeOut(300, function(){ $(this).remove();});
        }
        else{
            $('<p id="alertMessage">Entrega caducada</p>').hide().appendTo($(this).closest(".btn")).fadeIn(300);
        }
    })
});

function doNotEditMain(){
    const lastItem = currentDocumentPath.substring(currentDocumentPath.lastIndexOf('/') + 1)

 /*   if( lastItem.includes("main") || lastItem.includes(".txt")){
        //disableEdit();
        document.querySelector("textarea").setAttribute("disabled", "disabled");
    }
    else{
        document.querySelector("textarea").removeAttribute("disabled");
    }*/
}


function refreshListOnlineStudents(){
    $.ajax({
        url: "/Controller/updateStudentsAjaxOnlineList.php",
        method: "POST",
        data: {
            problemId: problemId,
            sessionId: sessionId,
        },
        success: function (response) {
            let students_connected = JSON.parse(response);//Estudiantes que hayan entrado a la sesión
            let current_students_showing = $("*").find("a#btn-eamail.btn.email").text(); // Estudiantes que aparezcan al tutor en la lista de estudiante activos. Si no actualiza la página no aparecen los nuevos alumnos conectados

            for (let i=0; i< students_connected.length; i++){
                if(current_students_showing.search(students_connected[i]['user']) == -1){ // Estudiante se encuentra en sesión, pero aun no aparece en la lista de estudiantes activos en la sesión.
                    location.reload();// Forzamos actualización de pantalla para que aparezcan en la lista de alumnos conectados los nuevos estudiantes conectados.
                }
            }
            //https://www.tutorialrepublic.com/faq/how-to-add-li-in-an-existing-ul-using-jquery.php
        }
    })

}
function setSolutionEditingFalse() {
    // Set the solution's editing field as false before leaving the page
    $.ajax({
        url: "/Model/solutionSetEditingFalse.php",
        method: "POST",
        data: {
            folder: folderRoute,
            user_type: userType
        }
    })
}

function rmJupyterDocker() {
    $.ajax({
        url: "/Controller/removeJupyterDocker.php",
    })
}

function exitFunction () {
    //console.log("EXIT FUNCTION");
    if( userType === 0) {
        setSolutionEditingFalse();
    }
    //rmJupyterDocker();
}

function post(url, data, callback) {
    let xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function () {
        if (this.readyState === 4 && this.status === 200) {
            callback(this.responseText);
        }
    };
    xhr.open("POST", 'Model/' + url, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    if (typeof data === "object") {
        let newObj = "";
        for (let i in data) {
            newObj += i + '=' + data[i];
            if (Object.keys(data).indexOf(i) !== Object.keys(data).length - 1) {
                newObj += "&";
            }
        }
        data = newObj;
    }
    xhr.send(encodeURI(data));
}

function openFiler() {
    if (currentDocumentPath !== "") {
        openFile(currentDocumentPath);
    }
}

function checkChanges() {
    $.ajax({
        url: "/Model/checkStudent.php",
        method: "POST",
        data: {
            route: folderRoute,
        },
        success: function (response) {
            // Check if the professor is editing the solution
            if (response === "1") {
                if (readOnly === false) {
                    readOnly = true;
                    checkChangesInterval = setInterval(openFiler, 4000);
                    $('#root_modified').removeAttr("hidden");

                }
            } else {
                if($('#root_modified').attr("hidden") === undefined) {
                    $('#root_modified').attr("hidden", true);
                    location.reload();
                }
                readOnly = false;
                clearInterval(checkChangesInterval);
            }
            editor.setReadOnly(readOnly);
        }
    })
}

function executeCode(email, session_id, userType, usuario_visualizado, entregable, deadline, currentDate) {

    let text = editor.getSession().getValue();
    let answer = document.getElementById("answer");
    if (text.includes("import os") || text.includes("import sys")) {
        document.getElementById("error_msg_libraries").removeAttribute("hidden");
        return false;
    }
    let currentDocumentName = currentDocumentPath.split('/').pop();
    if (currentDocumentName === "") {
        $("#output").text("Selecciona el fitxer per executar");
        return false;
    }
    $.ajax({
        url: "/app/compiler.php",
        method: "POST",
        data: {
            language: programmingLanguage,
            code: editor.getSession().getValue(),
            route: folderRoute,
            file_to_execute: currentDocumentName
        },
        success: function (response) {

            Validation2(email, session_id, userType, response, usuario_visualizado);

            if(entregable === "on"){

                let index = response.lastIndexOf(":=>>");
                let grade = response[index + 5];
                if (response[index + 6] !== " "){
                    grade = grade.concat(response[index + 6]);
                }
                if(deadline > currentDate || deadline.length == 0){ //Se actualizará la nota si el alumno se encuentra en el plazo de entrega, o si es un problema entregable sin deadline.

                    if(isNaN(grade)){
                        grade = 0;
                    }// returns true if the variable does NOT contain a valid number

                    $("#grade").html("Grade: " + grade);
                    UpdateStudentProblemGrade(email, problemId, grade);
                }
            }

            answer.innerHTML = response;
        }
    })
    const Validation2 = (email, session_id, userType, response, usuario_visualizado) => {

        $.ajax({
            url: "/Controller/online_visualization_improvements.php",
            method: "POST",
            data: {
                email: email,
                id: session_id,
                userType: userType,
                output: response,
                usuario_visualizado: usuario_visualizado,
                problemId:problemId,
                route:folderRoute
            }
        });
    }
    const UpdateStudentProblemGrade = (email, problemId, grade) => {

        $.ajax({
            url: "/Controller/uploadEntregableGradeAjax.php",
            method: "POST",
            data: {
                email: email,
                problemId: problemId,
                grade: grade
            }
        });
    }
}

function updateStudentQualityCode(email, session_id, userType, usuario_visualizado) {
    //This function saves students' new problem files and gets the quality code.
    $.ajax({
        url: "/Model/checkStudent.php",
        method: "POST",
        data: {
            route: folderRoute,
        },
        success: function (response) {
            // Check if the professor is editing the solution
            if (response === "1") {

            } else {
                save();
                let response = null;
                $.ajax({
                    url: "/Controller/online_visualization_improvements.php",
                    method: "POST",
                    data: {
                        email: email,
                        id: session_id,
                        userType: userType,
                        output: response,
                        usuario_visualizado: usuario_visualizado,
                        problemId: problemId,
                        route: folderRoute
                    },
                });

            }

        }
    })
}

function openFile(fileName) {
    if (fileName !== "" && fileName !== currentDocumentPath) {
        // Set the previous file as not selected, the first time it will be ""
        if (currentDocumentPath !== "") {
            save();
            document.getElementById(currentDocumentPath).style.color = 'black';
            document.getElementById(currentDocumentPath).style.fontWeight = 'normal';
        }
        currentDocumentPath = fileName;
        // Set the new file as selected
        document.getElementById(currentDocumentPath).style.color = 'grey';
        document.getElementById(currentDocumentPath).style.fontWeight = 'bold';

        let fileExtension = currentDocumentPath.split('.').pop();
        // If the file is a notebook embed an iframe, otherwise get the file content from the backend
        let notebookContainer = document.getElementById("notebook");
        let editorContainer = document.getElementById("editor");
        let outputContainer = document.getElementById("answer");
        let executeButton = document.getElementById("execute");

        // Clear the notebooks container
        if (notebookContainer.hasChildNodes()) {
            notebookContainer.removeChild(notebookContainer.lastElementChild);
        }

        if (fileExtension === 'ipynb') {
            executeButton.setAttribute("hidden", "");
            // Remove the editor and the output views
            editorContainer.style.display = "none";
            outputContainer.style.display = "none";
            // Create a new iframe with the src of the file and append it to its container
            let iframe = document.createElement("iframe");
            let fileLocation = fileName.split("/").slice(-3).join("/");
            iframe.setAttribute("src", `http://localhost:${containerPort}/tree/${fileLocation}`);
            iframe.setAttribute("height", "800px");
            iframe.setAttribute("width", "100%");
            notebookContainer.appendChild(iframe);
        } else {
            executeButton.removeAttribute("hidden");
            editorContainer.style.display = "block";
            outputContainer.style.display = "block";
            if (fileExtension === "cpp") {
                editor.session.setMode("ace/mode/c_cpp");
            } else {
                editor.session.setMode("ace/mode/python");
            }
            post("../../Model/getFileContent.php", {file: encodeURIComponent(fileName)}, function (data) {
                editor.setValue(data, -1);
                // Clear undo history when changing a file
                editor.session.setUndoManager(new ace.UndoManager());
            });
        }
    }
}

function openFolder() {
    post("../../Model/dir.php", {folder: encodeURIComponent(folderRoute)}, function (data) {
        document.getElementById('files').innerHTML = data;
        //console.log($('*').find('.files'));
        // Open the first file of the folder's available files
        let container = document.querySelector('#files');
        let matches = container.querySelectorAll('ul > li');
        if (matches.length !== 0) {
            openFile(matches[0].id);
        }
    });
}

function newFile() {
    let edited = (userType === 0 && editing);

    let filename = prompt("Nom del fitxer");
    if (filename) {
        post("/var/www/html/Model/newFile.php", {filename, dir: encodeURIComponent(folderRoute), edited, problemId}, function (data) {
            if (data === "1") {
                openFolder(folderRoute);
            }
        });
    }
}

function save() {
    if (currentDocumentPath === undefined || currentDocumentPath.split('.').pop() === "ipynb") {
        return false;
    }
    $.ajax({
        url: "/Model/save.php",
        method: "POST",
        data: {
            file: currentDocumentPath,
            code: editor.getSession().getValue(),
            editing: editing,
            problem: problemId,
        },
        success: function (response) {
            if (response === 'true') {
                editor.getSession().getValue();
            }
        }
    })
}

function deleteFile() {
    $.ajax({
        url: "/Model/fileDelete.php",
        method: "POST",
        data: {
            id: toDeleteFileRoute,
        },
        success: function () {
            location.reload();
        }
    })
}
function receiveFile2() {

    let control = document.getElementById('new_file2');
    control.click();
    control.onchange = function (event) {
        let fileList = control.files;
        if (fileList.length === 0) {
            return false;
        }
        let fileLength = control.files.length;
        if (fileLength === 0) {
            alert("Selecciona els arxius del problema");
            return false;
        }
        let allowedExtensionsRegx = /(\.cpp|\.h|\.py|\.python|\.txt|\.ipynb)$/i;
        for (let i = 0; i < control.files.length; i++) {
            let file = control.files[i];
            let FileName = file.name;
            let FileExt = FileName.substr(FileName.lastIndexOf('.'));
            let isAllowed = allowedExtensionsRegx.test(FileExt);
            if (!isAllowed) {
                return false;
            }
        }

        // Set additional fields
        $("<input />").attr("type", "hidden")
            .attr("name", "root_edited")
            .attr("value", editing)
            .appendTo(this.form);
        $("<input />").attr("type", "hidden")
            .attr("name", "problem")
            .attr("value", problemId)
            .appendTo(this.form);
        this.form.submit();
    };
}

function acceptChanges(id) {
    $.ajax({
        url: "/Controller/acceptChanges.php",
        method: "POST",
        data: {
            id: id,
        },
        success: function (response) {
            location.reload();
        }
    })
}

function refreshMessagesStudent(){
    let outgoing_email= document.getElementById("o_mail").value;
    let sessionId = document.getElementById("sessionId").value;
    let problemId = document.getElementById("problem").value;

    $.ajax({
        url: "/Controller/UpdateChatsAjaxStudent.php",
        method: "POST",
        data:{
            outgoing_email: outgoing_email,
            sessionId: sessionId,
            problemId: problemId,
        },
        success: function(response) {
            let messages_aux= JSON.parse(response);
            let messages= [];
            for(let i=0; i < messages_aux.length; i++){
                messages.push(messages_aux[i]['msg']);
            }
            //console.log(messages);
            var text = $.trim($('.messages-child').text());//to remove the leading and trailing whitespace only

            screenMessages = text.split('\n ');
            let trim_screenMessages =[];
            for(let i=0; i < screenMessages.length; i++){
                trim_screenMessages.push(screenMessages[i].trimStart());
            }
            //console.log(trim_screenMessages);//Mensajes en la pantalla
            //console.log(messages);//Mensajes en la BD.
            //messages.push("PRUEBA");
            let difference = messages.filter(x => !trim_screenMessages.includes(x));//https://stackoverflow.com/questions/1187518/how-to-get-the-difference-between-two-arrays-in-javascript
            //console.log("Difernecias:" + difference);
            messages_n= [];
            repeated_messages={};

            trim_screenMessages_n=[];
            repeated_trim_screenMessages={};

            trim_screenMessages.push(difference);

            if(trim_screenMessages==0 &&difference.length > 0){
                $('.messages-child').append('<div class="self"><p>\n '+difference+'</p></div>');
                $('.form-inline').append('<input placeholder="Type message.." type="text" id="message" name="message" required="">');

            }

            if(difference.length > 0) {
                $('.messages-child').append('<div class="self"><p>\n '+difference+'</p></div>');
            }

            for(let i = 0; i < messages.length; i++){

                if(messages_n.includes(messages[i]))
                {
                    if(Object.keys(repeated_messages).includes(messages[i])){

                        repeated_messages[messages[i]] = repeated_messages[messages[i]] + 1;
                    }
                    else{
                        repeated_messages[messages[i]] = 1;
                    }
                }
                else{ messages_n.push(messages[i]); }
            }

            for(let i = 0; i < trim_screenMessages.length; i++){

                if(trim_screenMessages_n.includes(trim_screenMessages[i]))
                {
                    if(Object.keys(repeated_trim_screenMessages).includes(trim_screenMessages[i])){

                        repeated_trim_screenMessages[trim_screenMessages[i]] = repeated_trim_screenMessages[trim_screenMessages[i]] + 1;

                    }
                    else{
                        repeated_trim_screenMessages[trim_screenMessages[i]] = 1;
                    }
                }
                else{ trim_screenMessages_n.push(trim_screenMessages[i]); }
            }

            for (const [key, value] of Object.entries(repeated_messages)) {
                //console.log(key, value);
                if (Object.keys(repeated_trim_screenMessages).includes(key)){
                    if(repeated_messages[key] === repeated_trim_screenMessages[key]){
                        //Todo correcto
                    }
                    else if ( repeated_messages[key] >repeated_trim_screenMessages[key]){
                        $('.messages-child').append('<div class="self"><p>\n '+key+'</p></div>');
                    }
                }
                else{
                    //Repetida en la BD, pero no en la Screen
                    //console.log("Mensaje repetido: "+ repeated_messages[key]);

                    $('.messages-child').append('<div class="self"><p>\n '+key+'</p></div>');
                }
            }
        },
    })
}

function disableEdit(){ //This function will remove from students the avility to edit in the editor. It will be activated when users enter to a deactivated session.

    document.querySelector("textarea").setAttribute("disabled", "disabled");

}
