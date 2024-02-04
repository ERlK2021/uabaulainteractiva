let toCloneSession;
let alreadyCalled = 0;

//Inicializa pluggin Tooltip Bottstrap
$(function () {
    $('[data-toggle="tooltip"]').tooltip({
        trigger:'hover',
        boundary:'window',
        template:
            '<div class="tooltip tooltip-custom" role="tooltip"><div class="arrow"></div><div class="tooltip-inner"></div></div>'
    })
})
/////
$(document).ready(function () {
    $('form').one('submit', function () {
        let name = document.getElementById("name").value;
        let problems = document.getElementById("multiple-checkboxes");
        problems = $(problems).val();
        let error = document.getElementById("error_msg");

        if (name === "" || problems.length === 0) {
            error.classList.remove('hide');
            error.innerHTML = "Hi ha camps buits.";
        }
        // Add the subject field to the form
        const queryString = window.location.search;
        const urlParams = new URLSearchParams(queryString);
        let subjectId = urlParams.get('subject');

        $("<input />").attr("type", "hidden")
            .attr("name", "subject")
            .attr("value", subjectId)
            .appendTo(this);
        return true;
    });

    $('#duplicate_session_modal').on('show.bs.modal', function (e) {
        let invoker = $(e.relatedTarget);
        toCloneSession = parseInt(invoker.closest('.card').attr('id'));
    });

    ///NUEVO
    $('#delete_problem_modal').on('show.bs.modal', function (e) {
        let invoker = $(e.relatedTarget);
        toDeleteProblem = parseInt(invoker.closest('.card').attr('id'));
    });

    $('.change_visibility').on('click', function () {
        let image = $(this).children('img').attr('src');
        // Find in the shown image name if it's visible or not and toggle it
        let newVisibility = image.search('deactivated') === -1? 'deactivated': 'activated';
        let sessionId = $(this).closest('.card').attr('id');
        $.ajax({
            url: "/Model/changeSessionStatus.php",
            method: "POST",
            data: {
                sessionId: parseInt(sessionId),
                newVisibility: newVisibility,
            },
            success: function () {
                location.reload();
            }
        })
    })
    let old_session_name = $('#name').text();
    $('.groups').append('<input type="text" id ="old_session_name" name ="old_session_name" value="' + old_session_name + '" hidden/>');

    //First we make hidden input of the current session groups
    let groups_session = $('.btn.group');
    for (let i = 0; i < groups_session .length; i++) {
        $('.groups').append('<input type="text" id ="input_' + groups_session [i].innerText + '" name ="input_new_group[]" value="' + groups_session [i].innerText + '" hidden/>')
    }
    $(document).on('click', '.group',function () {
        let group = $(this).text().trim();

        if($(this).css('opacity') === '1'){
            $(this).attr('style', ' opacity: 0.4;');
            console.log($('#input_'+ group).remove());
            //$('#input_'+ group).remove();
        }
        else{
            $(this).attr('style', ' opacity: 1;');
            $('.groups').append('<input type="text" id ="input_' + group  + '" name ="input_new_group[]" value="' + group  + '" hidden/>');
        }
    })

    $('.add_group').on('click', function () {
        if($('#new_group_input').length === 0) {
            $(this).text('');
            $(this).append('<input id="new_group_input" style="width: 35px"/>');
            $(this).append('<button  type="button" class="btn delete" style="font-size:0.8em; line-height:1; border:transparent!important; padding-right:1px!important; padding-left: 10px!important">x</button>');
        }
    })

    $(document).on('click', '.btn.delete',function() {
        $('#new_group_input').remove();
        $(this).remove();
        $('.add_group').text('+');
    });

    $(window).keydown(function(event){//En el caso de que se de a enter en algún campo vacio, esto evitará que se envie el formulario a sessionUpdate.php directamente
        if(event.keyCode === 13 && $('#new_group_input').val() === "") {
            event.preventDefault();
            return false;
        }
    });
    $(document).on('keypress', function (event) {
        if($(document).find('#new_group_input').val() !== "") { //Check if user has written something on the new group input.
            if (event.keyCode === 13) { //user presses enter
                event.preventDefault();


                //.If the new group that user is adding already exists, we will deny the user from inputting it again
                let new_added_group = $('*').find('#new_group_input').val();

                if (new_added_group.length >= 1) { //If necessary to avoid entering undefined elements.
                    let current_groups = [];
                    let groups = $('.btn.group');
                    for (let i = 0; i < groups.length; i++) {
                        current_groups.push(groups[i].innerText);
                    }
                    if (!current_groups.includes(new_added_group)) {

                        $('.btn.group').last().after('<button class="btn group" type="button">' + new_added_group + '</button>');
                        $('.groups').append('<input type="text" id ="input_' + new_added_group + '" name ="input_new_group[]" value="' + new_added_group + '" style="display:none">');
                        $('#new_group_input').remove();
                        $('.delete').remove();
                        $('.add_group').text('+');
                    }
                }
            }
        }
    });
})

function deleteSession(sessionId) {
    $.ajax({
        url: "/Controller/sessionDelete.php",
        method: "POST",
        data: {
            session_id: sessionId,
        },
        success: function () {
            location.reload();
        }
    })
}

function deleteAllSessionsByName(sessionName) {

    $.ajax({
        url: "/Controller/sessionDelete.php",
        method: "POST",
        data: {
            session_name: sessionName,
        },
        success: function (response) {
            location.reload();
        }
    })
}

function deleteGroupSessions(class_group){

    $.ajax({
        url: "/Controller/sessionGroupDelete.php",
        method: "POST",
        data: {
            class_group: class_group,
        },
        success: function () {
            location.reload();
        }
    })
}
function deleteGroupSessions_2(){

    if (alreadyCalled === 1) {
        return;
    }
    alreadyCalled = 1;
    let class_group = document.getElementById("class_group_delete").value;

    if (class_group !== "") {
        $.ajax({
            url: "/Controller/sessionGroupDelete.php",
            method: "POST",
            data: {
                class_group: class_group,
            },
            success: function () {
                location.reload();
            },
        })
    }
}
function duplicateSession() {
    if (alreadyCalled === 1) {
        return;
    }
    alreadyCalled = 1;
    let session_name = document.getElementById("new_session_name").value;
    if (session_name !== "") {
        $.ajax({
            url: "/Controller/sessionDuplicate.php",
            method: "POST",
            data: {
                session_name: session_name,
                session_id: toCloneSession,
            },
            success: function () {
                location.reload();
            },
        })
    }
}
