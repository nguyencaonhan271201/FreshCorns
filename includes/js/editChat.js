let nameInput = document.querySelector("#edit-chat-form").querySelector("#roomname");
let imageInput = document.querySelector("#edit-chat-form").querySelector("#roomname");
let currentImage = "";

function getRoomInfo() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            uploadInfo(JSON.parse(this.responseText));  
        }
    }
    xhr.send(`room_edit_get=true&room_ID=${roomID}&csrf=${document.querySelector("#edit-chat-form").querySelector('input[name="csrf"]').value}`);
}

document.querySelector("#edit-chat-dropdown").addEventListener("click", function() {
    getRoomInfo();
})

function uploadInfo(result) {
    nameInput.value = result["room_name"];
    currentImage = result["thumbnail"];
    document.querySelector("#review-image-edit").src = currentImage;
}

document.querySelector("#btn-chat-room-edit-cancel").addEventListener("click", function() {
    editChatResetModal();
})

document.querySelector("#btn-chat-room-edit-submit").addEventListener("click", function(e) {
    e.preventDefault();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.onload = function() {
        if(this.status == 200) {
            executeEditResult(JSON.parse(this.responseText));
        }
    }

    //Gather form input
    let getForm = document.querySelector("#edit-chat-form");
    let fileInput = getForm.querySelector("#image-choose");
    let roomName = getForm.querySelector("#roomname").value;

    let formData = new FormData();
    formData.append("edit_chat_room", true);
    formData.append("room_name", roomName);
    formData.append("old_thumbnail", currentImage);
    formData.append("room_ID", roomID);
    formData.append("csrf", document.querySelector("#edit-chat-form").querySelector('input[name="csrf"]').value)
    if (fileInput.value != "") {
        formData.append("thumbnail", fileInput.files[0]);
    }
    xhr.send(formData);
})

function executeEditResult(results) {
    //Check for errors
    errors = results.errors;

    if ('roomname' in errors) {
        document.querySelector("#edit-chat-form").querySelector(".err-roomname").innerText = errors['roomname'];
    }

    if ('thumbnail' in errors) {
        document.querySelector("#edit-chat-form").querySelector(".err-thumbnail").innerText = errors['thumbnail'];
    }

    if ('execute_err' in errors) {
        document.querySelector("#edit-chat-form").querySelector(".err-execute").innerText = errors['execute_err'];
    }

    if (Object.keys(errors).length === 0) {
        ajaxRoomsList(2);
        editChatResetModal();
    }
}

function editChatResetModal() {
    document.querySelector(".err-roomname").innerText = "";
    document.querySelector(".err-thumbnail").innerText = "";
    document.querySelector(".err-execute").innerText = "";
    document.getElementById("edit-chat-form").reset();
    $("[data-dismiss=modal]").trigger({ type: "click" });
}


