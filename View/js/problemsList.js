let toDeleteProblem;
let editing = 0;

$(document).ready(function () {
    $('#delete_problem_modal').on('show.bs.modal', function (e) {
        let invoker = $(e.relatedTarget);
        toDeleteProblem = parseInt(invoker.closest('.card').attr('id'));
    });

    $('.change_visibility').on('click', function () {
        let image = $(this).children('img').attr('src');
        // Find in the shown image name if it's visible or not and toggle it
        let newVisibility = image.search('not-visible') === -1? 'private': 'public';
        let problemId = $(this).closest('.card').attr('id');
        $.ajax({
            url: "/Model/changeVisibility.php",
            method: "POST",
            data: {
                problemId: parseInt(problemId),
                newVisibility: newVisibility,
            },
            success: function () {
                location.reload();
            }
        })
    });
})

function deleteProblem() {
    $.ajax({
        url: "/Model/problemDelete.php",
        method: "POST",
        data: {
            id: toDeleteProblem,
        },
        success: function () {
            location.reload();
        }
    })
}

function receiveFile() {

    let problemId = $(this).closest('.card').attr('id');
    let control = document.getElementById('new_file');
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
