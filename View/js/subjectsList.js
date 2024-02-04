let toDeleteSubject;

$(document).ready(function () {
    $('#delete_subject_modal').on('show.bs.modal', function (e) {
        let invoker = $(e.relatedTarget);
        toDeleteSubject = parseInt(invoker.closest('.card').attr('id'));
    });
})

function deleteSubject() {
    $.ajax({
        url: "/Controller/subjectDelete.php",
        method: "POST",
        data: {
            id: toDeleteSubject,
        },
        success: function (response) {
            let url = new URL(window.location.href);
            let query = 'deleted';
            let deleteQuery = 'not_deleted';
            if (response === '-1') {
                [query, deleteQuery] = [deleteQuery, query];
            }

            url.searchParams.delete(deleteQuery);
            url.searchParams.set(query, toDeleteSubject);
            window.location.href = url.href;
        }
    })
}
