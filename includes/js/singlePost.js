let loadedComments = [];

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

document.addEventListener("DOMContentLoaded", function() {    
    setTimeout(function() {
        if (document.querySelector("form .emojionearea-editor") != null) {
            document.querySelector("form .emojionearea-editor").addEventListener("DOMSubtreeModified", function(e) {
                let getButton = document.querySelector("#create_post_btn");
                let getEditButton = document.querySelector("#edit_post_btn");
                if (e.target.childNodes.length == 0 && e.target.innerHTML == "") {
                    if (getButton != null)
                        getButton.disabled = true;
                    if (getEditButton != null)
                        getEditButton.disabled = true;
                } else {
                    if (getButton != null)
                        getButton.disabled = false;
                    if (getEditButton != null)
                        getEditButton.disabled = false;
                }
            })
    
            if (document.querySelector("form .emojionearea-editor").innerHTML != "") {
                let getButton = document.querySelector("#create_post_btn");
                let getEditButton = document.querySelector("#edit_post_btn");
                if (getButton != null)
                    getButton.disabled = false;
                if (getEditButton != null)
                    getEditButton.disabled = false;
            }
        }   
    }, 2000);

    updateSessionInfo();

    document.querySelectorAll(".btn-comment").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault;
            let getCommentSection = e.target.closest(".post-box").querySelector(".comment-section");
            if (getCommentSection.classList.contains("d-none"))
                getCommentSection.classList.remove("d-none");
        })
    })

    document.querySelectorAll(".a-comment").forEach(btn => {
        btn.addEventListener("click", function(e) {
            e.preventDefault;
            let getCommentSection = e.target.closest(".post-box").querySelector(".comment-section");
            if (getCommentSection.classList.contains("d-none"))
                getCommentSection.classList.remove("d-none");
        })
    })

    loadComments();
})

function loadComments() {
    document.querySelectorAll(".feedCard").forEach(post => {
        let getPostID = post.id;
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "././includes/php/commentRequestHandler.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if(this.status == 200 && this.readyState == 4) {
                let result = JSON.parse(this.responseText);
                outputComments(result);
            }
        }
        xhr.send(`get_all=true&post=${getPostID}`);
    })
}

function outputComments(result) {
    result.forEach(comment => {
        let commentID = comment['ID'];
        if (loadedComments.includes(commentID)) {
            return;
        }
        loadedComments.push(commentID);
        let postID = comment['post'];
        let userID = comment['user'];
        let commentContent = comment['content'];
        if (commentContent.indexOf(`<a href="profile.php?`) != -1) {
            let getMentionHTML = commentContent.substring(0, commentContent.indexOf(`</a>`) + 4);
            let remainingContent = "";
            if (commentContent.length > commentContent.indexOf(`</a>`) + 4) {
                remainingContent = commentContent.substring(commentContent.indexOf(`</a>`) + 4, commentContent.length);
                remainingContent = sanitize(remainingContent);
            }
            commentContent = getMentionHTML + remainingContent;
        } else {
            commentContent = sanitize(commentContent);
        }
        let dateCreated = comment['date_created'];
        let epochDateCreated = new Date(dateCreated);
        let displayName = comment['display_name'];
        let profileImage = comment['profile_image'];
        let commentParent = comment['parent'];
        let numberOfLikes = comment['number_of_likes'];
        let thisUserLiked = comment['current_user_likes'] == 1;
        let author = comment['author'];

        let likeButtonClass;
        if (thisUserLiked) {
            likeButtonClass = "comment-like liked";
        } else {
            likeButtonClass = "comment-like";
        }

        let commentLikeCountClass;
        if (numberOfLikes > 0) {
            commentLikeCountClass = "";
        } else {
            commentLikeCountClass = "d-none";
        }

        let commentOptionsClass = "";
        if (my_id == userID) {
            commentOptionsClass = `
                <div class="d-flex flex-column align-items-center justify-content-center comment-btn-block">
                    <button class="post-btn btn btn-sm d-flex align-items-center dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                        <a class="dropdown-item comment-edit" href="#" >Edit</a>
                        <a class="dropdown-item comment-delete" href="#" >Delete</a>
                    </div>
                </div>
            `;
        } else if (my_id == author) {
            commentOptionsClass = `
                <div class="d-flex flex-column align-items-center justify-content-center comment-btn-block">
                    <button class="post-btn btn btn-sm d-flex align-items-center dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                        <a class="dropdown-item comment-delete" href="#" >Delete</a>
                    </div>
                </div>
            `;
        }

        if (commentParent == null) {
            let parent = document.querySelector(`.feedCard .comment-section .pl-4.pr-4.pt-2.pb-2`);
            let parentInput = parent.querySelector(`.comment-input`);

            let commentDiv = document.createElement('div');
            commentDiv.className = "comment";
            commentDiv.id = `comment-${commentID}`;
            commentDiv.setAttribute("data-id", commentID);
            commentDiv.innerHTML = `
                <div class='d-flex flex-row comment-row mt-1 mb-1'>
                    <a href='profile.php?id=${userID}' class='comment-header-img'>
                        <img class='rounded-circle d-inline-block profile-img' src='${profileImage}'> 
                    </a>
                    <div class='comment-box ml-2 mr-0'>
                        <div>
                            <a href='profile.php?id=${userID}' data-user-id=${userID}><b>${displayName}</b></a>
                            <p class='m-0'>
                                ${commentContent}
                            </p>
                        </div>
                        <div class="comment-like-box ${commentLikeCountClass}">
                            <div>
                                <i class="fa fa-thumbs-up" aria-hidden="true"></i> 
                                <span class="comment-like-count"> ${numberOfLikes}</span>
                            </div>
                        </div>
                    </div>
                    ${commentOptionsClass}
                </div>
                <div class="mt-0 ml-4">
                    <span class="ml-4 comment-helper-btns">
                        <a href="#" class="${likeButtonClass}">Like</a>
                        <span>·</span>
                        <a href="#" class="comment-reply">Reply</a>
                        <span>·</span>
                        <span data-tooltip="${dateCreated}" data-tooltip-location="top">
                            ${getDuration(epochDateCreated)}
                        </span>
                    </span>
                    <div class="replies ml-4">
                        <p class="see-more-replies-btn d-none">
                            <i class="fa fa-reply" aria-hidden="true"></i>
                            <a href="#" class="see-more-reply">
                                See more
                                <span class="number-of-replies">
                                    0
                                </span> replies
                            </a>
                        </p>
                        <div class="replies-list d-none"></div>
                    </div>
                    <div class='reply-input d-none ml-4 mt-2 d-none' data-id="${commentID}">
                        <a href='profile.php?id=${my_id}' class='comment-header-img'>
                            <img class='rounded-circle d-inline-block profile-img' src='${my_image}'> 
                        </a>
                        <div class="reply-input-div ml-2">
                            <input data-emoji-input='unicode' data-emojiable='true'
                            type="text" class="form-control reply_inp" name="reply" placeholder="Write reply...">
                        </div>
                    </div>
                </div>
            `;
            parent.insertBefore(commentDiv, parentInput);
        } else {
            let parent = document.querySelector(`#comment-${commentParent} .replies .replies-list`);

            let commentDiv = document.createElement('div');
            commentDiv.className = "comment";
            commentDiv.id = `comment-${commentID}`;
            commentDiv.setAttribute("data-id", commentID);
            commentDiv.innerHTML = `
                <div class='d-flex flex-row comment-row mt-1 mb-1'>
                    <a href='profile.php?id=${userID}' class='comment-header-img'>
                        <img class='rounded-circle d-inline-block profile-img' src='${profileImage}'> 
                    </a>
                    <div class='comment-box ml-2 mr-0'>
                        <div>
                            <a href='profile.php?id=${userID}' data-user-id=${userID}><b>${displayName}</b></a>
                            <p class='m-0'>
                                ${commentContent}
                            </p>
                        </div>
                        <div class="comment-like-box ${commentLikeCountClass}">
                            <div>
                                <i class="fa fa-thumbs-up" aria-hidden="true"></i> 
                                <span class="comment-like-count"> ${numberOfLikes}</span>
                            </div>
                        </div>
                    </div>
                    ${commentOptionsClass}
                </div>
                <div class="mt-0 ml-4">
                    <span class="ml-4 comment-helper-btns">
                        <a href="#" class="${likeButtonClass}">Like</a>
                        <span>·</span>
                        <a href="#" class="comment-reply">Reply</a>
                        <span>·</span>
                        <span data-tooltip="${dateCreated}" data-tooltip-location="top">
                            ${getDuration(epochDateCreated)}
                        </span>
                    </span>
                    <div class="replies ml-4">
                        <p class="see-more-replies-btn d-none">
                            <i class="fa fa-reply" aria-hidden="true"></i>
                            <a href="#" class="see-more-reply">
                                See more
                                <span class="number-of-replies">
                                    0
                                </span> replies
                            </a>
                        </p>
                        <div class="replies-list d-none"></div>
                    </div>
                    <div class='reply-input d-none ml-4 mt-2' data-id="${commentID}">
                        <a href='profile.php?id=${my_id}' class='comment-header-img'>
                            <img class='rounded-circle d-inline-block profile-img' src='${my_image}'> 
                        </a>
                        <div class="reply-input-div ml-2">
                            <input data-emoji-input='unicode' data-emojiable='true'
                            type="text" class="form-control reply_inp" name="reply" placeholder="Write reply...">
                        </div>
                    </div>
                </div>
            `;
            parent.appendChild(commentDiv);
        }

        setTimeout(() => {
            $(`#comment-${commentID} .reply_inp`).emojioneArea({
                search: false,
                inline: true,
                events: {
                        keyup: function (editor, event) {
                            if (event.which == 13) {
                                let getID = event.target.parentNode.parentNode.getAttribute("data-id");
                                handleCommentSubmit(1, event.target);
                            }
                        }
                    }
                }); 
        }, 100)
    })

    //checkEmojiInput();
    document.querySelectorAll(".comment").forEach((commentDiv) => {
        let numberOfReplies = commentDiv.querySelectorAll(".replies-list .comment").length;
        if (numberOfReplies > 0) {
            // commentDiv.querySelector(".number-of-replies").innerHTML = numberOfReplies;
            commentDiv.querySelector(".see-more-replies-btn").classList.remove("d-none");

            let replyDisplay = numberOfReplies == 1? "reply" : "replies";
            commentDiv.querySelector(".see-more-reply").innerHTML = `
                See more
                <span class="number-of-replies">
                    ${numberOfReplies}
                </span> ${replyDisplay}
            `;
        }
    })

    document.querySelectorAll(".feedCard").forEach(box => {
        box.addEventListener("click", function(e) {
            let target = e.target;
            let postID = e.target.closest(".feedCard");
            if (postID != null) {
                postID = postID.id
            }
            if (target.classList.contains("comment-like")) {
                e.preventDefault();
                let getCommentID = target.closest(".comment").getAttribute("data-id");
                commentReactAction(getCommentID);
            } else if (target.classList.contains("comment-edit")) {
                e.preventDefault();
                getCommentID = target.closest(".comment").getAttribute("data-id");
                editCommentExecute(getCommentID);
            } else if (target.classList.contains("comment-delete")) {
                e.preventDefault();
                getCommentID = target.closest(".comment").getAttribute("data-id");
                document.querySelector("#commentDeleteModal").querySelector("#deleteCommentID").innerHTML = getCommentID; 
                $("#commentDeleteModal").modal("show");
            } else if (target.classList.contains("see-more-reply") || target.parentNode.classList.contains("see-more-reply")) {
                e.preventDefault();
                target.parentNode.parentNode.querySelector(".replies-list").classList.remove("d-none");
                target.parentNode.classList.add("d-none");
            } else if (target.classList.contains("comment-reply")) {
                e.preventDefault();
                let inputBlock = target.parentNode.parentNode.children[2];
                if (inputBlock.classList.contains("d-none")) {
                    inputBlock.classList.remove("d-none");
                    inputBlock.classList.add("d-flex");
                    inputBlock.classList.add("flex-row");
                } else {
                    inputBlock.classList.remove("d-flex");
                    inputBlock.classList.remove("flex-row");
                    inputBlock.classList.add("d-none");
                }
                let getName = target.parentNode.parentNode.parentNode.querySelector(".comment-row").querySelector(".comment-box").querySelector("div a b");
                let getID = getName.parentNode.getAttribute("data-user-id");
                let getEditor = inputBlock.querySelector(".emojionearea-editor");
                if (getEditor != "" && getID != my_id) {
                    inputBlock.querySelector(".emojionearea-editor").innerHTML = `
                        <a href="profile.php?id=${getID}" class="mention-a">${getName.innerText}</a>
                    `;
                }
            }
        })
    }); 
}

function commentReactAction(commentID) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/commentRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = JSON.parse(this.responseText);
            if (!message['success']) {
                $("#errorBox").modal("show");
            } else {
                let type = message['type'];
                if (type == 0) {
                    let getCurrentCount = parseInt(document.querySelector(`#comment-${commentID} .comment-like-count`).innerHTML);
                    getCurrentCount++;
                    document.querySelector(`#comment-${commentID} .comment-like-count`).innerHTML = (getCurrentCount).toString();
                    if (getCurrentCount == 1) {
                        document.querySelector(`#comment-${commentID} .comment-like-box`).classList.remove("d-none");
                    }
                    document.querySelector(`#comment-${commentID} .comment-like`).classList.add("liked");
                } else {
                    let getCurrentCount = parseInt(document.querySelector(`#comment-${commentID} .comment-like-count`).innerHTML);
                    getCurrentCount--;
                    document.querySelector(`#comment-${commentID} .comment-like-count`).innerHTML = (getCurrentCount).toString();
                    if (getCurrentCount == 0) {
                        document.querySelector(`#comment-${commentID} .comment-like-box`).classList.add("d-none");
                    }
                    document.querySelector(`#comment-${commentID} .comment-like`).classList.remove("liked");
                }
            }
        }
    }
    xhr.send(`comment_react=like&comment=${commentID}`);
}

function editCommentExecute(commentID) {
    let getCommentWrapper = document.querySelector(`#comment-${commentID}`);
    if (getCommentWrapper == null) {
        $("#errorBox").modal("show");
    }

    let commentBox = getCommentWrapper.querySelector(".comment-box");
    let commentBoxHTML = commentBox.outerHTML;
    
    let commentOriginalContent = commentBox.querySelector("p").innerHTML;

    commentBox.classList.add("comment-box-edit")
    commentBox.innerHTML = `
        <textarea data-emoji-input='unicode' data-emojiable='true'
        type="text" class="form-control" name="comment_edit_content" id="comment_edit_content_${commentID}" data-id="commentID"
        placeholder="Write...">${commentOriginalContent.trim()}</textarea>
        <p>Press Esc to <a class="blue-a" href="">cancel</a>.</p>
    `;

    document.activeElement.blur();
    commentBox.focus();

    let getMentionHTML = "";
    let getMentionName = "";

    if (commentOriginalContent.trim().indexOf(`<a href="profile.php?id=`) != -1) {
        getMentionHTML = commentOriginalContent.trim().substring(0, commentOriginalContent.trim().indexOf(`</a>`) + 4);
        getMentionName = getMentionHTML.substring(getMentionHTML.indexOf(`>`) + 1, commentOriginalContent.trim().indexOf(`</a>`));
        getMentionHTMLEdit = getMentionHTML.replace("mention-a-display", "mention-a");
        commentBox.innerHTML = `
        <textarea data-emoji-input='unicode' data-emojiable='true'
        type="text" class="form-control" name="comment_edit_content" id="comment_edit_content_${commentID}" data-id="commentID"
        placeholder="Write...">${commentOriginalContent.trim().replace(getMentionHTML, getMentionHTMLEdit)}</textarea>
        <p>Press Esc to <a class="blue-a" href="">cancel</a>.</p>
        `;
    }
    

    let commentButtonBlock = getCommentWrapper.querySelector(".comment-btn-block");
    let commentButtonBlockHTML = commentButtonBlock.outerHTML;
    commentButtonBlock.innerHTML = "";

    let commentHelperBlock = getCommentWrapper.querySelector(".comment-helper-btns");
    commentHelperBlock.classList.add("d-none");

    $(`#comment_edit_content_${commentID}`).emojioneArea({
        pickerPosition: "bottom",
        search: false,
        events: {
            keyup: function (editor, event) {
                if (event.which == 13) {
                    event.preventDefault();
                    //Handle comment submit
                    let element = event.target;

                    //Get input
                    document.activeElement.blur();
                    element.focus();
                
                    //Get real content to store in DB
                    element.querySelectorAll("div").forEach(element => element.remove());
                    let getInnerHTML = element.innerHTML;

                    while (getInnerHTML.indexOf("<img") != -1) {
                        let getStartIndex = getInnerHTML.indexOf("<img");
                        let getSubstring = getInnerHTML.substring(getStartIndex, getInnerHTML.length);
                        let getEndIndex = getStartIndex + getSubstring.indexOf(">") + 1;
                        let getEmojiString = getInnerHTML.substring(getStartIndex, getEndIndex);
                        let getEmojiText = getEmojiString.substring(getEmojiString.indexOf('"') + 1, getEmojiString.indexOf('"') + 3);
                        getInnerHTML = getInnerHTML.substring(0, getStartIndex) + getEmojiText + getInnerHTML.substring(getEndIndex, getInnerHTML.length);
                    }

                
                    let newContent = getInnerHTML.replace(/&nbsp;/g, " ");

                    //Check for mentioning
                    if (getMentionHTML != "" && newContent.indexOf(getMentionName) == 0) {
                        newContent = newContent.replace(getMentionName, getMentionHTML).trim();
                    }

                    if (newContent == "") {
                        //Ask for deletion
                        document.querySelector("#commentDeleteModal").querySelector("#deleteCommentID").innerHTML = getCommentID; 
                        $("#commentDeleteModal").modal("show");
                        return;
                    }
               
                    let xhr = new XMLHttpRequest();
                    xhr.open("POST", "././includes/php/commentRequestHandler.php", true);
                    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
                    xhr.onload = function() {
                        let result = this.responseText;
                        if (result == "false") {
                            commentBox.outerHTML = commentBoxHTML;
                            commentButtonBlock.outerHTML = commentButtonBlockHTML;
                            getCommentWrapper.querySelector(".comment-btn-block").querySelector(".dropdown-menu").classList.remove("show");
                            getCommentWrapper.querySelector(".comment-btn-block").classList.remove("show");
                            commentHelperBlock.classList.remove("d-none");
                            $("#errorBox").modal("show");
                        } else {
                            commentBox.outerHTML = commentBoxHTML;
                            commentButtonBlock.outerHTML = commentButtonBlockHTML;
                            getCommentWrapper.querySelector(".comment-btn-block").querySelector(".dropdown-menu").classList.remove("show");
                            getCommentWrapper.querySelector(".comment-btn-block").classList.remove("show");
                            commentHelperBlock.classList.remove("d-none");
                            getCommentWrapper.querySelector(".comment-box").querySelector("p").innerHTML = newContent;
                        }
                    }
                    xhr.send(`comment_edit=true&content=${newContent}&comment=${commentID}`);    
                } else if (event.key == "Escape") {
                    commentBox.outerHTML = commentBoxHTML;
                    commentButtonBlock.outerHTML = commentButtonBlockHTML;
                    getCommentWrapper.querySelector(".comment-btn-block").querySelector(".dropdown-menu").classList.remove("show");
                    getCommentWrapper.querySelector(".comment-btn-block").classList.remove("show");
                    commentHelperBlock.classList.remove("d-none");
                }
            }
        }
    });

    //Change format for mentioning
    document.querySelectorAll(".comment .emojionearea-editor").forEach(comment => {
        comment.innerHTML = comment.innerHTML.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');
    })
    // document.querySelector(".comment .emojionearea-editor").innerHTML = 
    // document.querySelector(".emojionearea-editor").innerHTML.replace(/&lt;/g,'<').replace(/&gt;/g,'>').replace(/&amp;/g,'&');

    commentBox.querySelector(".blue-a").addEventListener("click", function(e) {
        e.preventDefault();
        commentBox.outerHTML = commentBoxHTML;
        commentButtonBlock.outerHTML = commentButtonBlockHTML;
        getCommentWrapper.querySelector(".comment-btn-block").querySelector(".dropdown-menu").classList.remove("show");
        getCommentWrapper.querySelector(".comment-btn-block").classList.remove("show");
        commentHelperBlock.classList.remove("d-none");
    });
}

document.querySelector("#commentDeleteModal").addEventListener("click", function(e) {
    if (e.target.classList.contains("comment-delete-confirm")) {
        e.preventDefault();
        performCommentDeletion(e.target.parentNode.parentNode.querySelector("#deleteCommentID").innerHTML);
    }
})

function performCommentDeletion(commentID) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/commentRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = this.responseText;
            if (message == "false") {
                $("#errorBox").modal("show");
            } else {
                //Success
                document.querySelector(`#comment-${commentID}`).remove();
            }
        }
    }
    xhr.send(`comment_delete&comment=${commentID}`);
    $("#commentDeleteModal").modal("hide");
}

function handleCommentSubmit(type, element) {
    let getInnerHTML = element.innerHTML.trim();
    let getMentionHTML = "";
    let getMentionName = "";
    if (getInnerHTML.indexOf(`<a href="profile.php?id=`) != -1) {
        getMentionHTML = getInnerHTML.substring(0, getInnerHTML.indexOf(`</a>`) + 4);
        getMentionName = getMentionHTML.substring(getMentionHTML.indexOf(`>`) + 1, getInnerHTML.indexOf(`</a>`));
    }

    //Get input
    document.activeElement.blur();
    element.focus();

    let commentContent;
    let getPostID;
    let parent;

    if (type == 0) {
        commentContent = element.closest(".comment-input").querySelector(".comment-input-div input").value;
        getPostID = element.closest(".comment-input").getAttribute("data-id");
        parent = 0;
    } else {
        commentContent = element.closest(".reply-input").querySelector(".reply-input-div input").value;
        parent = element.closest(".comment").getAttribute("data-id");
        getPostID = element.closest(".feedCard").id;
    } 

    if (commentContent == "") {
        return;
    }

    if (getMentionHTML != "") {
        getMentionHTML = getMentionHTML.replace("mention-a", "mention-a-display")
        commentContent = commentContent.replace(getMentionName, getMentionHTML).trim();
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/commentRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = JSON.parse(this.responseText);
            if (message.length == 0) {
                $("#errorBox").modal("show");
            } else {
                element.innerHTML = "";
                outputNewComment(message);
            }
        }
    }
    xhr.send(`add=true&content=${commentContent}&post=${getPostID}&parent=${parent}`);    
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

function outputNewComment(result) {
    let commentID = result['ID'];
    let postID = result['post'];
    let userID = result['user'];
    let commentContent = result['content'];
    if (commentContent.indexOf(`<a href="profile.php?`) != -1) {
        let getMentionHTML = commentContent.substring(0, commentContent.indexOf(`</a>`) + 4);
        let remainingContent = "";
        if (commentContent.length > commentContent.indexOf(`</a>`) + 4) {
            remainingContent = commentContent.substring(commentContent.indexOf(`</a>`) + 4, commentContent.length);
            remainingContent = sanitize(remainingContent);
        }
        commentContent = getMentionHTML + remainingContent;
    } else {
        commentContent = sanitize(commentContent);
    }
    let dateCreated = result['date_created'];
    let epochDateCreated = new Date(dateCreated);
    let displayName = result['display_name'];
    let profileImage = result['profile_image'];
    let commentParent = result['parent'];

    let parent;
    let parentInput;

    if (commentParent == null) {
        parent = document.querySelector(`.feedCard .comment-section .pl-4.pr-4.pt-2.pb-2`);
        parentInput = parent.querySelector(`.comment-input`);

        let commentDiv = document.createElement('div');
        commentDiv.className = "comment";
        commentDiv.id = `comment-${commentID}`;
        commentDiv.setAttribute("data-id", commentID);
        commentDiv.innerHTML = `
            <div class='d-flex flex-row comment-row mt-1 mb-1'>
                <a href='profile.php?id=${userID}' class='comment-header-img'>
                    <img class='rounded-circle d-inline-block profile-img' src='${profileImage}'> 
                </a>
                <div class='comment-box ml-2 mr-0'>
                    <div>
                        <a href='profile.php?id=${userID}' data-user-id=${userID}><b>${displayName}</b></a>
                        <p class='m-0'>
                            ${commentContent}
                        </p>
                    </div>
                    <div class="comment-like-box d-none">
                        <div>
                            <i class="fa fa-thumbs-up" aria-hidden="true"></i> 
                            <span class="comment-like-count"> 0</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-center comment-btn-block">
                    <button class="post-btn btn btn-sm d-flex align-items-center dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                        <a class="dropdown-item comment-edit" href="#" >Edit</a>
                        <a class="dropdown-item comment-delete" href="#" >Delete</a>
                    </div>
                </div>
            </div>
            <div class="mt-0 ml-4">
                <span class="ml-4 comment-helper-btns">
                    <a href="#" class="comment-like">Like</a>
                    <span>·</span>
                    <a href="#" class="comment-reply">Reply</a>
                    <span>·</span>
                    <span data-tooltip="${dateCreated}" data-tooltip-location="top">
                        ${getDuration(epochDateCreated)}
                    </span>
                </span>
                <div class="replies ml-4">
                    <p class="see-more-replies-btn d-none">
                        <i class="fa fa-reply" aria-hidden="true"></i>
                        <a href="#" class="see-more-reply">
                            See more
                            <span class="number-of-replies">
                                0
                            </span> replies
                        </a>
                    </p>
                    <div class="replies-list d-none"></div>
                </div>
                <div class='reply-input d-none ml-4 mt-2' data-id="${commentID}">
                    <a href='profile.php?id=${my_id}' class='comment-header-img'>
                        <img class='rounded-circle d-inline-block profile-img' src='${my_image}'> 
                    </a>
                    <div class="reply-input-div ml-2">
                        <input data-emoji-input='unicode' data-emojiable='true'
                        type="text" class="form-control reply_inp" name="reply" placeholder="Write reply...">
                    </div>
                </div>
            </div>
        `;

        parent.insertBefore(commentDiv, parentInput);
    } else {
        let parent = document.querySelector(`#comment-${commentParent} .replies`);

        let commentDiv = document.createElement('div');
        commentDiv.className = "comment";
        commentDiv.id = `comment-${commentID}`;
        commentDiv.setAttribute("data-id", commentID);
        commentDiv.innerHTML = `
            <div class='d-flex flex-row comment-row mt-1 mb-1'>
                <a href='profile.php?id=${userID}' class='comment-header-img'>
                    <img class='rounded-circle d-inline-block profile-img' src='${profileImage}'> 
                </a>
                <div class='comment-box ml-2 mr-0'>
                    <div>
                        <a href='profile.php?id=${userID}' data-user-id=${userID}><b>${displayName}</b></a>
                        <p class='m-0'>
                            ${commentContent}
                        </p>
                    </div>
                    <div class="comment-like-box d-none">
                        <div>
                            <i class="fa fa-thumbs-up" aria-hidden="true"></i> 
                            <span class="comment-like-count"> 0</span>
                        </div>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-center justify-content-center comment-btn-block">
                    <button class="post-btn btn btn-sm d-flex align-items-center dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                        <a class="dropdown-item comment-edit" href="#" >Edit</a>
                        <a class="dropdown-item comment-delete" href="#" >Delete</a>
                    </div>
                </div>
            </div>
            <div class="mt-0 ml-4">
                <span class="ml-4 comment-helper-btns">
                    <a href="#" class="comment-like">Like</a>
                    <span>·</span>
                    <a href="#" class="comment-reply">Reply</a>
                    <span>·</span>
                    <span data-tooltip="${dateCreated}" data-tooltip-location="top">
                        ${getDuration(epochDateCreated)}
                    </span>
                </span>
                <div class="replies ml-4">
                    <p class="see-more-replies-btn d-none">
                        <i class="fa fa-reply" aria-hidden="true"></i>
                        <a href="#" class="see-more-reply">
                            See more
                            <span class="number-of-replies">
                                0
                            </span> replies
                        </a>
                    </p>
                    <div class="replies-list d-none"></div>
                </div>
                <div class='reply-input d-none ml-4 mt-2' data-id="${commentID}">
                    <a href='profile.php?id=${my_id}' class='comment-header-img'>
                        <img class='rounded-circle d-inline-block profile-img' src='${my_image}'> 
                    </a>
                    <div class="reply-input-div ml-2">
                        <input data-emoji-input='unicode' data-emojiable='true'
                        type="text" class="form-control reply_inp" name="reply" placeholder="Write reply...">
                    </div>
                </div>
            </div>
        `;
        parent.appendChild(commentDiv);
    }

    

    setTimeout(() => {
        $(`#comment-${commentID} .reply_inp`).emojioneArea({
            search: false,
            inline: true,
            events: {
                    keyup: function (editor, event) {
                        if (event.which == 13) {
                            let getID = event.target.parentNode.parentNode.getAttribute("data-id");
                            handleCommentSubmit(1, event.target);
                        }
                    }
                }
            }); 
    }, 100)
}

function checkEmojiInput() {
    $('.reply_inp').emojioneArea({
        search: false,
        inline: true,
        events: {
                keyup: function (editor, event) {
                    if (event.which == 13) {
                        let getID = event.target.parentNode.parentNode.getAttribute("data-id");
                        handleCommentSubmit(1, event.target);
                    }
                }
            }
        }); 
}

$(".singleCardShare").unbind().click(function(){
    document.querySelector("#singlePostShareConfirmationModal").querySelector("#singlePostSharePostID").innerHTML = $(this).attr('id'); 
    $("#singlePostShareConfirmationModal").modal("show");
});

document.querySelector("#singlePostShareConfirmationModal").addEventListener("click", function(e) {
    if (e.target.classList.contains("share-confirm")) {
        e.preventDefault();
        sharePost(e.target.parentNode.parentNode.querySelector("#singlePostSharePostID").innerHTML, e.target.parentNode.parentNode.querySelector("#single-post-share-type").value);
    }
})

function sharePost(postID, mode) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/mainRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let message = this.responseText;
            if (message != "true") {
                $("#singlePostErrorBox").modal("show");
            } else {
                window.location.href = 'feeds.php';
            }
        }
    }
    xhr.send(`post_share&postID=${postID}&mode=${mode}`);
    $("#singlePostShareConfirmationModal").modal("hide");
}