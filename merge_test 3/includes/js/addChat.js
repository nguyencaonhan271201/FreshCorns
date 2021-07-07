let chosenMember = 0;
let chosenMemberName = "";
let addForm = document.querySelector("#add-chat-form");
let searchAddInput = addForm.querySelector("#member-add-search");
let memberSearchAddList = addForm.querySelector("#member-search-add-results");
let memberChosenAddList = addForm.querySelector("#member-chosen-add-list");
let buttonAddMemberSubmit = document.querySelector("#btn-chat-room-add");

function ajaxSearchAddMembers(query) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            outputSearchAddResults(JSON.parse(this.responseText));
        }
    }
    xhr.send(`member_search_add=true&q=${query}&room=${roomID}&csrf=${addForm.querySelector('input[name="csrf"]').value}`);
}

searchAddInput.addEventListener("keyup", function(e) {
    e.preventDefault();
    let input = searchAddInput.value;
    ajaxSearchAddMembers(input);
})

searchAddInput.addEventListener("focus", function(e) {
    e.preventDefault();
    let input = searchAddInput.value;
    if (input == "") {
        ajaxSearchAddMembers(input);
    }
})

buttonAddMemberSubmit.addEventListener("click", function(e) {
    $("[data-dismiss=modal]").trigger({ type: "click" });
    e.preventDefault();
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            if (this.responseText == "true") {
                firebase.database().ref().child(`${roomID}/messages/${Date.now()}`).set({
                    "active": 2,
                    "content": `${chosenMemberName} has joined the chat room`,
                    "room": roomID,
                    "timestamp": firebase.database.ServerValue.TIMESTAMP,
                });
            }
            else {
                //Error occured
                $("#leaveError").modal("show");
            }
        }
    }
    xhr.send(`chat_room_member_add=true&id=${chosenMember}&room=${roomID}&csrf=${addForm.querySelector('input[name="csrf"]').value}`);
})

$('#addMember').on('hidden.bs.modal', function () {
    addChatResetModal();
})

function addChatResetModal() {
    addForm.querySelector(".err-execute").innerText = "";
    memberChosenAddList.innerHTML = "";
    document.getElementById("create-chat-form").reset();
    chosenMember = 0;
    chosenMemberName = "";
    $("[data-dismiss=modal]").trigger({ type: "click" });
}

function outputSearchAddResults(results) {
    memberSearchAddList.innerHTML = "";
    results.forEach(result => {
        if (result.ID != chosenMember)
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
            memberSearchAddList.innerHTML += html;
        }
    })
    createEventAddListeners();
}

function createEventAddListeners() {
    addForm.querySelectorAll(".search-result").forEach(result => {
        result.addEventListener("click", function(e) {
            let getTarget = e.target.closest(".search-result");
            let getID = parseInt(getTarget.getAttribute("data-id"));
            if (getID != chosenMember) {
                chosenMember = getID;
                chosenMemberName = getTarget.querySelector("#search-display-name").innerText;
                getImage = getTarget.querySelector("img").src;
                memberChosenAddList.innerHTML = `
                    <div class="mdc-chip" role="row">
                        <div class="mdc-chip__ripple"></div>
                        <img class="mdc-chip__icon mdc-chip__icon--leading" src="${getImage}"></img>
                        <span role="gridcell">
                            <span role="button" tabindex="0" class="mdc-chip__primary-action">
                                <span class="mdc-chip__text">${chosenMemberName}</span>
                            </span>
                        </span>
                    </div>
                `;
                memberSearchAddList.innerHTML = "";
                getTarget.classList.add("selected");
            }
        });
    });
}