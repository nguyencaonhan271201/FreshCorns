let getForm = document.querySelector("#create-form");

getForm.addEventListener("submit", function(e) {
    e.preventDefault();
    
    //Get input
    let displayName = getForm.querySelector("input[name = 'create-display'").value;
    let email = getForm.querySelector("input[name = 'create-email'").value;
    let username = getForm.querySelector("input[name = 'create-username'").value;
    let password1 = getForm.querySelector("input[name = 'create-password1'").value;
    let password2 = getForm.querySelector("input[name = 'create-password2'").value;
    let gender = getForm.querySelector("input[name = 'create-gender'").value;
    let dob = getForm.querySelector("input[name = 'create-dob'").value;
    let csrf = getForm.querySelector("input[name = 'csrf'").value;

    //Get error display p
    let errorDisplays = [getForm.querySelector("#create-display-err"), getForm.querySelector("#create-email-err"),
    getForm.querySelector("#create-username-err"), getForm.querySelector("#create-password-err"), getForm.querySelector("#create-dob-err"),
    getForm.querySelector("#create-gender-err"), getForm.querySelector("#create-execute-err")];

    errorDisplays.forEach(error => {
        error.innerHTML = "";
    })

    if (!dob) {
        errorDisplays[4].innerHTML = "Date of birth is not valid!";
        return;
    }
    
    //ajax
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/mainRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let errors = JSON.parse(this.responseText);
            if (errors.length != 0 || !dob) {
                if (errors['display']) {
                    errorDisplays[0].innerHTML = errors['display'];
                }
                if (errors['email']) {
                    errorDisplays[1].innerHTML = errors['email'];
                }
                if (errors['username']) {
                    errorDisplays[2].innerHTML = errors['username'];
                }
                if (errors['password1'] || errors['password2']) {
                    errorDisplays[3].innerHTML = errors['password1']? errors['password1'] : errors['password2'];
                }
                if (errors['gender']) {
                    errorDisplays[5].innerHTML = errors['gender'];
                }
                if (errors['dob'] || !dob) {
                    errorDisplays[4].innerHTML = "Date of birth is not valid!";
                }
                if (errors['execute_err']) {
                    errorDisplays[6].innerHTML = errors['execute_err'];
                }
            } else {
                window.location.href = 'feeds.php';
            }
        }
    }
    xhr.send(`create_account&display=${displayName}&email=${email}&username=${username}&password1=${password1}&password2=${password2}&gender=${gender}&dob=${dob}
    &csrf=${csrf}`);
})