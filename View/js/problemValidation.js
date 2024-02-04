$(document).ready(function () {
    $('.custom-file-input').on('change', function(){
        let names = [];
        let files = $(this).get(0).files;
        for (let i = 0; i < files.length; ++i) {
            names.push(files[i].name);
        }

        let fieldValue;
        if (names.length === 0) {
            fieldValue = 'Selecciona fitxers';
        } else if (names.length < 4) {
            fieldValue = names.join(", ");
        } else {
            fieldValue = `${names.length} seleccionats.`;
        }

        $(this).siblings('label').text(fieldValue);
    })

    $('form').on('submit', function () {
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        let subjectId = urlParams.get('subject');
        let problemId = urlParams.get('problem');

        $("<input />").attr("type", "hidden")
            .attr("name", "subject")
            .attr("value", subjectId)
            .appendTo(this);
        $("<input />").attr("type", "hidden")
            .attr("name", "problem")
            .attr("value", problemId)
            .appendTo(this);
        return true;
    })

    // Set the value of the selector
    let languageSelector = $("#language");
    let programingLanguage = languageSelector.attr('value');
    languageSelector.val(programingLanguage).change();

    if ($('#entregable').is(":checked")) //Si estamos editando los datos de un problema y es entregable, que aparezca por defecto la casilla de entregable seleccionada y con la fecha si tiene deadline.
    {
        let deadline = "Data (Opcional)";
        if($('#hiddenDeadline').length){
            deadline = $('#hiddenDeadline').text();
        }
        $('#entregable').closest(".input-container").append('<input class="input" id="datepicker" name="datepicker" placeholder='+ deadline +'>');
    }

    $('#entregable, #entregableLabel').mousedown(function() {

        if($('#datepicker').length){
            $('#datepicker').remove();
        }
        else{
            $(this).closest(".input-container").append('<input class="input" id="datepicker" name="datepicker" placeholder="Data (Opcional)">');
        }
    });

    $(document).on('mousedown', '#datepicker', function(){
        $( "#datepicker" ).datepicker();
    });
})

function validateProblem() {
    let title_element = document.getElementById("title");
    // Initial value set to the title to avoid the validation error when edition the problem
    let title = "Test value";
    if (title_element) {
        title = title_element.value;
    }
    let description = document.getElementById("description").value;
    let error = document.getElementById("error_msg");

    if (title === "" || description === "") {
        if ($(error).is(':hidden')) {
            error.toggleAttribute('hidden');
        }
        error.innerHTML = "Hi ha camps buits";
        return false;
    } else if (title.length < 3 || title.length > 80) {
        if ($(error).is(':hidden')) {
            error.toggleAttribute('hidden');
        }
        error.innerHTML = "Títol massa curt";
        return false;
    } else if (description.length < 3) {
        if ($(error).is(':hidden')) {
            error.toggleAttribute('hidden');
        }
        error.innerHTML = "Descripció massa curta";
        return false;
    }
    return true;
}

function validateProblemAndFiles() {
    if (!validateProblem()) {
        return false;
    }

    let error = document.getElementById("error_msg");
    let customFile = document.getElementById("file");
    let fileLength = customFile.files.length;
    if (fileLength === 0) {
        if ($(error).is(':hidden')) {
            error.toggleAttribute('hidden');
        }
        error.innerHTML = "Selecciona els arxius del problema";
        return false;
    }

    // Check if all the files extensions ar allowed
    let allowedExtensionsRegx = /(\.cpp|\.h|\.py|\.python|\.txt|\.ipynb)$/i;
    for (let i = 0; i < customFile.files.length; i++) {
        let fileName = customFile.files[i].name;
        let fileExt = fileName.substr(fileName.lastIndexOf('.'));

        if (!allowedExtensionsRegx.test(fileExt)) {
            if ($(error).is(':hidden')) {
                error.toggleAttribute('hidden');
            }
            error.innerHTML = `La extensió del fitxer ${fileName} és incorrecte.`;
            return false;
        }
    }
    return true;
}