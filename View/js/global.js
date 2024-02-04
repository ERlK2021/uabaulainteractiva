function generateToken() {
    $.ajax({
        url: "/Model/tokenGenerator.php",
        success: function (response) {
            document.getElementById("invitation_link").value = "localhost/index.php?query=3&token=" + response;
        }
    })
}

function copyInvitationLink() {
    let invitationLink = document.getElementById("invitation_link").value;

    navigator.clipboard.writeText(invitationLink).then(
        $(".message").text("Link copiat!")
    );
}

$(document).ready(function () {
    if (localStorage.getItem('theme') === 'dark-theme' &&
        document.body.getAttribute('dark-theme') === '') {
        document.body.classList.toggle("dark-theme");
    }

    // Add event listener to the collapsible items
    let collapsible = document.getElementsByClassName("collapsible");
    for (let i = 0; i < collapsible.length; i++) {
        collapsible[i].addEventListener("click", function () {
            this.classList.toggle("active");
            let content = this.nextElementSibling;
            if (content.style.display === "block") {
                content.style.display = "none";
            } else {
                content.style.display = "block";
            }
        });
    }

    $('#dark-theme-switch').change(function () {
        let toggled = document.body.classList.toggle("dark-theme");
        let theme = toggled? 'dark-theme': 'light-theme';
        localStorage.setItem('theme', theme);
        $.ajax({
            url: "/Model/savePreferences.php",
            method: "POST",
            data: {
                theme: theme,
            }
        })
    })

    let formInputs = $('.input-container .input');
    for (let i = 0; i < formInputs.length; i++) {
        let formInput = $(formInputs[i]);
        let inputCut = formInput.siblings('.cut');
        let inputLabel = formInput.siblings('label');
        let clone = inputLabel.clone();
        clone.css('visibility', 'hidden');
        $('body').append(clone);
        inputCut.width(clone.width() + 10);
        clone.remove();
    }
})
