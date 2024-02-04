$(document).ready(function() {
    $('#multiple-checkboxes').multiselect({
      includeSelectAllOption: true,
    });

    $('.multiselect-container li').on('click', function (e) {
        e.preventDefault();
        return true;
    })
});
