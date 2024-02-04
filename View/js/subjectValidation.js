function validateSubject() {
    let title = document.getElementById("title").value;
    let description = document.getElementById("description").value;
    let course = document.getElementById("course").value;
    let error = document.getElementById("error_msg");

    if (title === "" || description === "" || course === "") {
        if ($(error).is(':hidden')) {
            error.toggleAttribute('hidden');
        }
        error.innerHTML = "Hi ha camps buits.";
        return false;
    }
    return true;
}
