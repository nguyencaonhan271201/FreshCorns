let searchInput = document.querySelector("#member-search");
let memberSearchList = document.querySelector("#member-search-results");
let memberChosenList = document.querySelector("#member-chosen-list");
let chosenMembers = [];
let buttonSubmit = document.querySelector("#btn-chat-room-create");

searchInput.addEventListener("keyup", function(e) {
    e.preventDefault();
    let input = searchInput.value;
    ajaxSearchMembers(input);
})

searchInput.addEventListener("focus", function(e) {
    e.preventDefault();
    let input = searchInput.value;
    if (input == "") {
        ajaxSearchMembers(input);
    }
})

buttonSubmit.addEventListener("click", function(e) {
    e.preventDefault();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.onload = function() {
        if(this.status == 200) {
            let results = JSON.parse(this.responseText);
            executeCreateRoomResult(results);
        }
    }

    //Gather form input
    let getForm = document.querySelector("#create-chat-form");
    let fileInput = getForm.querySelector("#image-choose");
    let roomName = getForm.querySelector("#roomname").value;

    let formData = new FormData();
    formData.append("create_chat_room", true);
    formData.append("room_name", roomName);
    formData.append("members", JSON.stringify(chosenMembers));
    formData.append("csrf", getForm.querySelector('input[name="csrf"]').value)
    if (fileInput.value != "") {
        formData.append("thumbnail", fileInput.files[0]);
    }
    xhr.send(formData);
})

function executeCreateRoomResult(results) {
    //Check for errors
    errors = results.errors;
    if ('duplicate_room' in errors) {
        let getRoomID = results['roomID'];
        document.querySelectorAll(".chat-list-item").forEach(item => {
            if (item.getAttribute("data-id") == getRoomID) {
                document.querySelector(".selected").classList.remove("selected");
                item.classList.add("selected");
                roomID = getRoomID;         
                ajaxGetSingleRoomInfo(getRoomID);
                resetModal();
                return;
            }
        });
    } 

    if ('roomname' in errors) {
        document.querySelector(".err-roomname").innerText = errors['roomname'];
    }

    if ('thumbnail' in errors) {
        document.querySelector(".err-thumbnail").innerText = errors['thumbnail'];
    }

    if ('execute_err' in errors) {
        document.querySelector(".err-execute").innerText = errors['execute_err'];
    }

    if (Object.keys(errors).length === 0) {
        //No errors
        let getRoomID = results['roomID'];
        let message = `${my_name} started a new chat room.`;
        let getCurrentTime = Date.now();
        firebase.database().ref().child(getRoomID).set({
            "last_message": getCurrentTime
        })
        firebase.database().ref().child(`${getRoomID}/messages/${getCurrentTime}`).set({
            "active": 2,
            "content": `${message}`,
            "room": getRoomID,
            "timestamp": firebase.database.ServerValue.TIMESTAMP,
        })
        //Set timeout to wait for the trigger event of Firebase to be executed first
        ajaxRoomsList(3, getRoomID);
        resetModal();
    }
}

$('#createChatRoom').on('hidden.bs.modal', function () {
    createChatResetModal();
})

function createChatResetModal() {
    document.querySelector(".err-roomname").innerText = "";
    document.querySelector(".err-thumbnail").innerText = "";
    document.querySelector(".err-execute").innerText = "";
    if (!document.querySelector(".review-group").classList.contains("d-none")) {
        document.querySelector(".review-group").classList.add("d-none");
    }
    chosenMembers = [];
    numberOfMembers.innerHTML = chosenMembers.length.toString();
    memberChosenList.innerHTML = "";
    document.getElementById("create-chat-form").reset();
    $("[data-dismiss=modal]").trigger({ type: "click" });
}

function ajaxSearchMembers(query) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            outputSearchResults(JSON.parse(this.responseText));
        }
    }
    let getForm = document.querySelector("#create-chat-form");
    xhr.send(`member_search=true&q=${query}&csrf=${getForm.querySelector('input[name="csrf"]').value}`);
}

function outputSearchResults(results) {
    memberSearchList.innerHTML = "";
    results.forEach(result => {
        if (!chosenMembers.includes(result.ID))
        {
            let html = `<div class="member search-result d-flex flex-row justify-content-start align-items-center" data-id=${result.ID}>
                <img class="member-search-img rounded-circle mr-2 ml-2" src="${result.profile_image}">
                <div class="ml-2 d-flex flex-column align-items-center justify-content-start">
                    <div class="text-left">
                        <p class="mt-1 mb-0" id="search-display-name">${result.display_name}</p>
                        <p class="mt-1 mb-1 font-italic">${result.username}</p>
                    </div>
                </div>
            </div>`;
            memberSearchList.innerHTML += html;
        }
    })
    createEventListeners();
}

function createEventListeners() {
    document.querySelectorAll(".search-result").forEach(result => {
        result.addEventListener("click", function(e) {
            let getTarget = e.target.closest(".search-result");
            let getID = parseInt(getTarget.getAttribute("data-id"));
            if (!chosenMembers.includes(getID)) {
                chosenMembers.push(getID);
                let getName = getTarget.querySelector("#search-display-name").innerText;
                let getImage = getTarget.querySelector("img").src;
                memberChosenList.innerHTML += `
                    <div class="mdc-chip" role="row" data-id = ${getID}>
                        <div class="mdc-chip__ripple"></div>
                        <img class="mdc-chip__icon mdc-chip__icon--leading" src="${getImage}"></img>
                        <span role="gridcell">
                            <span role="button" tabindex="0" class="mdc-chip__primary-action">
                                <span class="mdc-chip__text">${getName}</span>
                            </span>
                        </span>
                        <span role="gridcell">
                            <i class="chip-delete material-icons mdc-chip__icon mdc-chip__icon--trailing" tabindex="-1" role="button"
                            onclick = "deleteSelectedItem(event)">cancel</i>
                        </span>
                    </div>
                `;
                searchInput.value = "";
                memberSearchList.innerHTML = "";
                searchInput.blur();
            }
            checkForm();
            numberOfMembers.innerHTML = chosenMembers.length.toString();
        });
    });

}

function deleteSelectedItem(e) {
    let getParent = e.target.closest(".mdc-chip");
    let getID = getParent.getAttribute("data-id");
    let index = -1;
    for (let i = 0; i < chosenMembers.length; i++) {
        if (chosenMembers[i] == getID)
        {
            index = i;
            break;
        }
    }
    chosenMembers.splice(index, 1);
    getParent.remove();
    numberOfMembers.innerHTML = chosenMembers.length.toString();
}

function checkForm() {
    if (chosenMembers.length > 1) {
        document.querySelector("#roomname").disabled = false;
        document.querySelector("#image-choose").disabled = false;
    } else {
        document.querySelector("#roomname").disabled = true;
        document.querySelector("#image-choose").disabled = true;
    }
}