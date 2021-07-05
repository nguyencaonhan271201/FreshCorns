// Your web app's Firebase configuration
// For Firebase JS SDK v7.20.0 and later, measurementId is optional
var firebaseConfig = {
    apiKey: "AIzaSyBFR272iszhRrRoTvYAAR_Xo2VmPS0KV2M",
    authDomain: "cs204finalproj.firebaseapp.com",
    databaseURL: "https://cs204finalproj-default-rtdb.asia-southeast1.firebasedatabase.app",
    projectId: "cs204finalproj",
    storageBucket: "cs204finalproj.appspot.com",
    messagingSenderId: "237018463485",
    appId: "1:237018463485:web:ce4287c830ffb1c9ae5294",
    measurementId: "G-QKDYQHKWGS"
};
// Initialize Firebase
firebase.initializeApp(firebaseConfig);

//Variables
let my_id;
let my_name;
let my_image;
let roomID = 1;
let messageRef;
let messageForm = document.querySelector("#messageForm");
let messageDiv = document.querySelector(".messages");
let controlItems = [];
let currentNumberOfRooms = 0;
let newMessageAudio = new Audio("assets/new_message_tone.mp3");
let isOnTab = true;

function updateSessionInfo() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/sessionInfoRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            let result = JSON.parse(this.responseText);
            my_id = result['user_id'];
            my_name = result['name'];
            my_image = result['profile_img'];
        }
    }
    xhr.send(`page=chat`);
}

function sanitize(content) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#x27;',
        "/": '&#x2F;',
    };
    const reg = /[&<>"'/]/ig;
    return content.replace(reg, (match)=>(map[match]));
}

//Event listeners
messageForm.addEventListener("submit", function(e) {
    e.preventDefault();
    handleFormSubmit();
})

document.addEventListener("visibilitychange", function() {
    isOnTab = !document.hidden;
});

//Load list of rooms
function ajaxRoomsList(type, newRoom = 0) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            document.querySelector(".chat-list-items").innerHTML = "";
            controlItems = [];
            outputRoomsList(JSON.parse(this.responseText), type, newRoom);
        }
    }
    xhr.send(`list_rooms=true`);
}

function ajaxGetSingleRoomInfo(ID) {
    roomID = ID;
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            updateCurrentRoomRef(JSON.parse(this.responseText));
        }
    }
    xhr.send(`single_room=true&room_ID=${ID}`);
}

function outputRoomsList(roomLists, type, newRoom = 0) {
    document.querySelector(".chat-list-items").innerHTML = "";
    controlItems = [];

    if (roomLists.length == 0) {
        document.querySelector(".chat-room-content").setAttribute("style", "opacity: 0");
        document.querySelector(".chat-list-items").innerHTML = "<p class='text-center'>Create a new chat room</p>"
    } else {
        document.querySelector(".chat-room-content").setAttribute("style", "opacity: 1");
    }

    roomLists.forEach((room) => {
        let lastMessageRef = firebase.database().ref(`${room.ID}/messages/`).limitToLast(1);
        let lastMessage = "";
        let lastSenderImage = "";
        let roomName = room.room_name; 

        lastMessageRef.on("value", function(snapshot) {
            snapshot.forEach((child) => {
                let val = child.val();
                let personDisplay = "";
                if (child.hasChild("sender_id")) {
                    lastMessage = val.content;
                    if (lastMessage.length > 20) {
                        if (lastMessage == '<i class="fa fa-heart red chat-heart" aria-hidden="true"></i>')
                            lastMessage = "❤";
                        else lastMessage = lastMessage.substr(0, 20) + "...";
                    }
                        
                    personDisplay = val.sender_id == my_id? "You: " : ""
                } else {
                    lastMessage = val.content;
                    if (lastMessage.length > 20) {
                        lastMessage = lastMessage.substr(0, 25) + "...";
                    }
                    lastMessage = `<i>${lastMessage}</i>`;
                }

                if (val.media != undefined) {
                    lastMessage = `<i>${val.sender_id == my_id? "You " : val.sender_name} sent an image</i>`;
                    personDisplay = "";
                }

                if (val.active == 0) {
                    lastMessage = `<i>${val.sender_id == my_id? "You " : val.sender_name} deleted a message</i>`;
                    personDisplay = "";
                }

                if (room.type == 1) {
                    //Type 1 room
                    room['members'].forEach(member => {
                        if (member.display_name != my_name) {
                            roomName = member.display_name;
                            lastSenderImage = member.profile_image;
                        }
                    })
                } else {
                    lastSenderImage = room.thumbnail;
                }

                let time = getDuration(val.timestamp)

                if (lastMessage.includes("<img")) {
                    lastMessage += "></img>"
                } else if (!lastMessage.includes("<i>")) {
                    //lastMessage = sanitize(lastMessage);
                }

                let childHTML = `
                    <div class="col-lg-2 col-md-4 col-sm-12 d-flex align-items-center justify-content-center">
                        <img class="chat-title-image rounded-circle" id="img-room-${room.ID}" src="${lastSenderImage}">
                    </div>
                    <div class="col-lg-10 col-md-8 d-md-block d-sm-none d-none pr-1">
                        <h6>${roomName}</h6>
                        <p class="last-message" id="last-mess-${room.ID}">${personDisplay} ${lastMessage} · ${time}</p>
                    </div>
                `;

                let html = `
                    <div class="chat-list-item p-2 row mt-2" id="chat-list-item-${room.ID}" data-id=${room.ID}>
                        ${childHTML}
                    </div>
                `;

                if (document.querySelector(`#chat-list-item-${room.ID}`) == null) {
                    firebase.database().ref().child(`${room.ID}`).child('last_message').get().then(snapshot => {
                        if (snapshot.exists()) {
                            let object = {
                                html: html,
                                time: snapshot.val(),
                                id: room.ID
                            }
                            if (!duplicateObject(controlItems, object))
                                controlItems.push(object);

                            if (controlItems.length == roomLists.length) {
                                document.querySelector(".chat-list-items").innerHTML = "";
                                controlItems.sort((a,b) => (a.time < b.time) ? 1 : ((b.time < a.time) ? -1 : 0))
                                controlItems.forEach(item => {
                                    document.querySelector(`.chat-list-items`).innerHTML += item.html;
                                })
                                if (type == 1) {
                                    //Type 1 indicate if the selected item is the room with newest message
                                    roomID = controlItems[0].id;
                                    currentNumberOfRooms = controlItems.length;
                                    addRoomCountEventListener();
                                }
                                if (type == 3) {
                                    roomID = newRoom;
                                    currentNumberOfRooms = controlItems.length;
                                }
                                let type4Valid = false;
                                if (type == 4) {
                                    for (let i = 0; i < controlItems.length; i++) {
                                        if (controlItems[i].id == newRoom) {
                                            roomID = newRoom;
                                            currentNumberOfRooms = controlItems.length;
                                            type4Valid = true;
                                            break;
                                        }
                                    }
                                    if (!type4Valid) {
                                        roomID = controlItems[0].id;
                                        currentNumberOfRooms = controlItems.length;
                                    }
                                    addRoomCountEventListener();
                                }
                                if (document.querySelector(`#chat-list-item-${roomID}`) == null)
                                    roomID = controlItems[0].id;
                                document.querySelector(`#chat-list-item-${roomID}`).classList.add("selected");
                                ajaxGetSingleRoomInfo(roomID);                                
                            }

                            document.querySelectorAll(".chat-list-item").forEach(item => {
                                item.addEventListener("click", function(e) {
                                    let getID = item.getAttribute("data-id");
                                    document.querySelector(".selected").classList.remove("selected");
                                    e.target.closest(".chat-list-item").classList.add("selected");
                                    roomID = getID;
                                    ajaxGetSingleRoomInfo(getID);
                                })
                            });
                        }
                    })
                } else {
                    if (document.querySelector(`#last-mess-${room.ID}`) != null) {
                        if (document.querySelector(`#last-mess-${room.ID}`).innerText != lastMessage) {
                            document.querySelector(`#chat-list-item-${room.ID}`).innerHTML = childHTML;

                            let getOldNode = document.querySelector(`#chat-list-item-${room.ID}`);
                            let nodeClone = document.querySelector(`#chat-list-item-${room.ID}`).cloneNode();
                            document.querySelector(`.chat-list-items`).prepend(nodeClone);
                            document.querySelector(`.chat-list-items`).replaceChild(getOldNode, nodeClone);

                            //newMessageAudio.play();
                        }
                    }
                }
           })
        })
    });
}

function duplicateObject(arr, key) {
    arr.forEach(item => {
        if (item.id == key.id) {
            return true;
        }
    })
    return false;
}

function updateCurrentRoomRef(room) {
    document.querySelector(".messages").innerHTML = "";

    if (room.type == 1) {
        //Type 1 room
        room['members'].forEach(member => {
            if (member.display_name != my_name) {
                roomName = member.display_name;
                lastSenderImage = member.profile_image;
                document.querySelector(".chat-room-title img").src = lastSenderImage;
                document.querySelector(".chat-room-title h5").innerText = roomName;
            }
        })
        document.querySelector("#room-edit").classList = "d-none";
    } else {
        roomName = room.room_name;
        lastSenderImage = room.thumbnail;
        document.querySelector(".chat-room-title img").src = lastSenderImage;
        document.querySelector(".chat-room-title h5").innerText = roomName;
        document.querySelector("#room-edit").setAttribute("data-id", roomID);
        document.querySelector("#room-edit").classList = "";
        document.querySelector("#modal_room_name").innerText = roomName;
    }

    messageRef = firebase.database().ref(`${roomID}/messages`);
    //Listen for incoming messages
    messageRef.on("child_added", function(snapshot) {
        let val = snapshot.val();
        let html = "";
        if (!snapshot.hasChild("sender_id")) {
            if (messageDiv.querySelector(`#chat-noti-${snapshot.key}`) == null && val.room == roomID) {
                html = `<p class='chat-room-noti mt-2' data-id=${snapshot.key} id="chat-noti-${snapshot.key}">${val.content}</p>`;
                messageDiv.innerHTML += html; 
            }
            return;
        }

        if (messageDiv.querySelector(`#message-${snapshot.key}`) == null && val.room == roomID) {
            let sender_id = val.sender_id;

            let deleteButton = "";
            let tooltip = "";

            if (val.active == 1) {
                deleteButton = `<a class="nav-link dropdown-toggle message-delete-a 
                ${val.media != undefined ? "d-flex align-items-end" : ""}" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" 
                aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis-v" aria-hidden="true"></i></a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item delete-item" data-id="${snapshot.key}" href="#">Delete</a>
                </div>`;
                tooltip = `data-tooltip='${getTimeString(val.timestamp)}' data-tooltip-location='${val.media != undefined? "top" : sender_id == my_id? "left" : "right"}'`;
            }
            
            let message_content = "";
            let message_box_class = sender_id == my_id? "dark mr-2" : "light ml-2";

            // if (val.content != `<i class="fa fa-heart red chat-heart" aria-hidden="true"></i>` && val.content.indexOf("emojioneemoji") == -1 
            //     && !val.content.includes("<i>")) {
            //     val.content = sanitize(val.content);
            // }

            if (val.media == undefined || val.content == `<i class="fa fa-heart red chat-heart" aria-hidden="true"></i>`) {
                message_content = `<span ${tooltip} class="message-content ${message_box_class}">
                    ${val.active == 1? val.content : "<i>This message is deleted!</i>"}
                </span>`
            } else {
                if (val.active == 1)
                    message_content = `<a class="message-img-a ${sender_id == my_id? "right" : "left"}" ${tooltip}>
                    <img alt="" src="${val.media}" class="message-img ${sender_id == my_id? "mr-2" : "ml-2"}"></img></a>`;
                else {
                    message_content = `<span ${tooltip} class="message-content ${message_box_class}">
                        <i>This message is deleted!</i>
                    </span>`
                }
            }


            if (sender_id == my_id) {
                html += `
                    <div class="message d-flex justify-content-end p-2" id="message-${snapshot.key}">
                        ${deleteButton}
                        ${message_content}
                        <div class="chat-image-block">
                            <div></div>
                            <a href="profile.php?id=${val.sender_id}">
                                <img class="chat-content-image rounded-circle" 
                                src="${val.sender_image}"/>
                            </a>
                        </div> 
                    </div>   
                `;
            } else {
                html += `
                    <div class="message d-flex justify-content-start p-2" id="message-${snapshot.key}">
                        <div class="chat-image-block">
                            <div></div>
                            <a href="profile.php?id=${val.sender_id}">
                                <img class="chat-content-image rounded-circle" 
                                src="${val.sender_image}"/>
                            </a>
                        </div>
                        ${message_content}
                    </div>    
                `;
                // if (!isOnTab) {
                //     newMessageAudio.play();
                // }
            }
            
            
            messageDiv.innerHTML += html; 

            if (sender_id == my_id) {
                document.querySelector(`#message-${snapshot.key}`).scrollIntoView();
            }

            if (document.querySelector(".chat-heart") != null) {
                document.querySelectorAll(".chat-heart").forEach(heart => {
                    if (heart.parentNode.classList.contains("light")) {
                        heart.parentNode.classList.remove("light");
                    } else {
                        heart.parentNode.classList.remove("dark");
                    }
                    heart.parentNode.classList.add("none-background")
                })
            }

            messageForm.querySelector("#message_inp").value = "";

            addRoomObjectsEventListener();

            firebase.database().ref(`${roomID}/messages/${snapshot.key}`).on("value", function(snapshot) {
                let val = snapshot.val();
                if (val.room == roomID && val.active == 0) {
                    let message_content = "";
                    let message_box_class = sender_id == my_id? "dark mr-2" : "light ml-2";

                    if (val.media == undefined || val.content == `<i class="fa fa-heart red chat-heart" aria-hidden="true"></i>`) {
                        message_content = `<span class="message-content ${message_box_class}">
                            ${val.active == 1? val.content : "<i>This message is deleted!</i>"}
                        </span>`
                    } else {
                        message_content = `<span class="message-content ${message_box_class}">
                            <i>This message is deleted!</i>
                        </span>`
                    }

                    let html = "";
                    if (val.sender_id == my_id) {
                        html = `${message_content}
                        <div class="chat-image-block">
                            <div></div>
                            <a href="profile.php?id=${val.sender_id}">
                                <img class="chat-content-image rounded-circle" 
                                src="${val.sender_image}"/>
                            </a>
                        </div> `;
                    } else {
                        html = `
                        <div class="chat-image-block">
                            <div></div>
                            <a href="profile.php?id=${val.sender_id}">
                                <img class="chat-content-image rounded-circle" 
                                src="${val.sender_image}"/>
                            </a>
                        </div> 
                        ${message_content}`;
                    }

                    messageDiv.querySelector(`#message-${snapshot.key}`).innerHTML = html;
                }
            })
        }
    })

    
}

function handleFormSubmit() {
    let getMessage = messageForm.querySelector("#message_inp").value;
    if (getMessage == "") {
        getMessage = document.querySelector(".emojionearea-editor").innerHTML;
    }

    messageForm.querySelector("#message_inp").value = "";
    document.querySelector(".emojionearea-editor").innerHTML = "";
    sendMessage(getMessage);

    let getMessageIcon = document.querySelector("#btn-message-icon");
    getMessageIcon.classList = "fa fa-heart";
    getMessageIcon.dataset.type = 0;
}

function sendMessage(message) {
    messageRef.child(Date.now()).set({
        "sender_name": `${my_name}`,
        "sender_id": `${my_id}`,
        "sender_image": `${my_image}`,
        "content": `${message.replace("<br>", "")}`,
        "room": `${roomID}`,
        "timestamp": firebase.database.ServerValue.TIMESTAMP,
        "active": 1
    })

    let updates = {};
    updates[`${roomID}/last_message`] = firebase.database.ServerValue.TIMESTAMP;

    firebase.database().ref().update(updates);
}

function sendMessageWithMedia(media) {
    messageRef.child(Date.now()).set({
        "sender_name": `${my_name}`,
        "sender_id": `${my_id}`,
        "sender_image": `${my_image}`,
        "content": ``,
        "media": `${media}`,
        "room": `${roomID}`,
        "timestamp": firebase.database.ServerValue.TIMESTAMP,
        "active": 1
    })

    let updates = {};
    updates[`${roomID}/last_message`] = firebase.database.ServerValue.TIMESTAMP;

    firebase.database().ref().update(updates);
}

function getSameDate(date1, date2) {
    return date1.getFullYear() === date2.getFullYear() &&
    date1.getMonth() === date2.getMonth() &&
    date1.getDate() === date2.getDate();
}

Number.prototype.pad = function(size) {
    var s = String(this);
    while (s.length < (size || 2)) {s = "0" + s;}
    return s;
}

function getTimeString(epochTime) {
    let getDate = new Date(epochTime);
    let getCurrentDate = new Date();

    if (getSameDate(getDate, getCurrentDate)) {
        return getDate.getHours().pad(2) + ":" + getDate.getMinutes().pad(2);
    } else if (getDate.getFullYear() == getCurrentDate.getFullYear()) {
        return `${getDate.getDate().pad(2)}/${(getDate.getMonth() < 12 ? getDate.getMonth() + 1 : 1).pad(2)} ${getDate.getHours().pad(2)}:${getDate.getMinutes().pad(2)}`;
    } else {
        return `${getDate.getDate().pad(2)}/${(getDate.getMonth() < 12 ? getDate.getMonth() + 1 : 1).pad(2)}/${getDate.getFullYear()} ${getDate.getHours().pad(2)}:${getDate.getMinutes().pad(2)}`;
    }
}

function getDuration(epochTime) {
    let timeDifference = Date.now() - epochTime;
    let aWeek = 86400000 * 7;
    let getValue;
    let getUnit;
    if (timeDifference < 3600000)
    {
        getValue = Math.floor(timeDifference / 60000) <= 0? 1 : Math.floor(timeDifference / 60000);
        getUnit = getValue == 1? "minute" : "minutes";
    }
    else if (timeDifference < 86400000)
    {
        getValue = Math.floor(timeDifference / 3600000);
        getUnit = getValue == 1? "hour" : "hours";
    }
    else if (timeDifference < aWeek)
    {
        getValue = Math.floor(timeDifference / 86400000);
        getUnit = getValue == 1? "day" : "days";
    }
    else
    {
        getValue = Math.floor(timeDifference / aWeek);
        getUnit = getValue == 1? "week" : "weeks";
    }
    return `${getValue} ${getUnit}`;
}

function addRoomCountEventListener() {
    firebase.database().ref().on("value", function(snapshot) {
        if (snapshot.numChildren() != currentNumberOfRooms) {
            currentNumberOfRooms = snapshot.numChildren();
            ajaxRoomsList(2);
        }
    });
}

function ajaxSendMessageImage(fileUpload) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/chatRoomRequestHandler.php", true);
    xhr.onload = function() {
        if(this.status == 200) {
            fileUpload.innerHTML = "<input id='messageImage' type='file' hidden/>";
            if (this.responseText == "false") {
                alert("File cannot be uploaded!");
            } else {
                let getURL = this.responseText;
                getURL = getURL.substring(8, getURL.length);
                sendMessageWithMedia(getURL);
            }
        }
    }
    let formData = new FormData();
    formData.append("file", fileUpload.files[0]);
    formData.append("file_upload", true);
    formData.append("csrf", message.querySelector('input[name="csrf"]').value)
    xhr.send(formData);
}

document.addEventListener("DOMContentLoaded", function() {
    //Initiate session info
    updateSessionInfo();
    
    let urlParams = new URLSearchParams(window.location.search);
    //Initiate the rooms list
    if (urlParams.has("room")) {
        ajaxRoomsList(4, urlParams.get("room"))
    } else {
        ajaxRoomsList(1);
    }
    
    //Update the rooms list once every minute
    setTimeout(function() {
        ajaxRoomsList(2);
    }, 60000)

    setTimeout(function() {
        document.querySelector(".emojionearea-editor").addEventListener("DOMSubtreeModified", function(e) {
            if (e.target.childNodes.length == 0 && e.target.firstChild == "") {
                document.querySelector("#btn-message-icon").classList = "fa fa-heart";
                document.querySelector("#btn-message-icon").setAttribute("data-type", 0);
            } else {
                document.querySelector("#btn-message-icon").classList = "fa fa-paper-plane";
                document.querySelector("#btn-message-icon").setAttribute("data-type", 1);
            }
        })
    }, 2000);
})

document.querySelector("#btn-message-icon").addEventListener("click", function(e) {
    e.preventDefault();
    if (e.target.dataset.type == 0) {
        sendMessage('<i class="fa fa-heart red chat-heart" aria-hidden="true"></i>');
    } else {
        handleFormSubmit();
    }
})

document.querySelector("#btn-file-image").addEventListener("click", function(e) {
    e.preventDefault();
    document.getElementById('messageImage').click();
})

document.getElementById("messageImage").onchange = function(e) {
    ajaxSendMessageImage(e.target);
};

document.querySelector(".image-box").addEventListener("click", function() {
    hideImageBox();
})

document.querySelector("#btn-leave-cancel").addEventListener("click", function() {
    $("[data-dismiss=modal]").trigger({ type: "click" });
})

document.querySelector("#btn-leave-approve").addEventListener("click", function(e) {
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
                    "content": `${my_name} has left the chat room`,
                    "room": roomID,
                    "timestamp": firebase.database.ServerValue.TIMESTAMP,
                });
                setTimeout(function() {
                    ajaxRoomsList(1);
                }, 1000);
            } else {
                //Error occured
                $("#leaveError").modal("show");
            }
        }
    }
    xhr.send(`leave_room=true&room_ID=${roomID}&csrf=${message.querySelector('input[name="csrf"]').value}`);
})

function addRoomObjectsEventListener() {
    document.querySelectorAll(".delete-item").forEach((button) => {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            let get_id = e.target.getAttribute("data-id");
            let updates = {};
            updates[`${roomID}/messages/${get_id}/active`] = 0;
            firebase.database().ref().update(updates);
        })
    })

    document.querySelectorAll(".message-img-a").forEach((image) => {
        image.addEventListener("click", function(e) {
            e.preventDefault();
            let getURL = image.querySelector(".message-img").src;
            loadImage(getURL);
            showImageBox();
        })
    })
}

function showImageBox() {
    $(".image-box").css("display", "flex");
    setTimeout(function() {
        $(".image-box").css("opacity", 1);
    }, 10);
}

function hideImageBox() {
    $(".image-box").css("opacity", 0);
    setTimeout(function() {
        $(".image-box").css("display", "none");
    }, 300);
}

function loadImage(src) {
    $(".image-box img").attr("src", src);
}