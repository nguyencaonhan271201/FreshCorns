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

            loadPosts();
        }
    }
    xhr.send(`page=chat`);
}

function loadPosts(){
    let urlParams = new URLSearchParams(window.location.search);
    let user_id = -1;
    if (urlParams.has("id")) {
        user_id = urlParams.get("id")
    } else {
        user_id = my_id;
    }

    let xhr = new XMLHttpRequest();
    xhr.open("POST", "includes/php/profileRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200) {
            let results = JSON.parse(this.responseText);
            printPosts($('#posts'), results);
        }
    }
    xhr.send(`load_posts&user_id=${user_id}`);
}

Date.prototype.addHours= function(h){
    this.setHours(this.getHours() + h);
    return this;
  }
  
  function printPosts(div,results){
      div.empty();
      results.forEach(result=>{
        //console.log(result);
        let mode_text = '';
          if (result['mode']==1)  mode_text='<i class="bi bi-globe"></i>';
          else if (result['mode']==2)  mode_text='<i class="bi bi-people"></i>';
          else if (result['mode']==3)  mode_text='<i class="bi bi-person"></i>';
          let html = "";
          if (result['share_from'] == null) {
            html = `
            <div class="feedCard container-fluid p-0" id="${result['ID']}">
              <a href="profile.php?id=${result['user']}" class="cardUserImg">
                  <img src="${result['profile_image']}">
              </a>

              <div class="cardInfos mx-100" id="${result['ID']}">
                  <h2>
                    <a href="profile.php?id=${result['user']}">${result['display_name']}</a> 
                    talking about 
                    <a class="movie_title" id='${
                      JSON.stringify({
                        movie_id:result['movie_id'],
                        movie_type:result['movie_type']
                      })}' href="movie.php?id=${result['movie_id']}&type=${result['movie_type']}"></a> 
                    • <a class="cardDate" data-tooltip="${new Date(result['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom" 
                      href="single_post.php?id=${result['ID']}">${getDuration(new Date(result['date_created']))}</a>
                    • <span class="cardMode" data-tooltip-location="bottom" data-tooltip="${result['mode'] == 1? "Public" : result['mode'] == 2? "Followers" : "Private"}" 
                      id="${result['mode']}">${mode_text}</span>
                  </h2>

                  <p class="limited_text">${result['content']}</p>   
                  
                  <div class="cardMedia">    
                    <img src="${(result['media'])?result['media']:''}" class="media">
                  </div>
              </div>   
            </div>
          `;
          } 
          else 
          {
            let share_mode_text = '';
            if (result['original']['mode']==1)  share_mode_text='<i class="bi bi-globe"></i>';
            else if (result['original']['mode']==2)  share_mode_text='<i class="bi bi-people"></i>';
            else if (result['original']['mode']==3)  share_mode_text='<i class="bi bi-person"></i>';
            html = `
            <div class="feedCard container-fluid p-0" id="${result['ID']}">
              <a href="profile.php?id=${result['user']}" class="cardUserImg">
                  <img src="${result['profile_image']}">
              </a>
  
              <div class="cardInfos mw-100" id="${result['ID']}">
                  <h2>
                    <a href="profile.php?id=${result['user']}">${result['display_name']}</a> 
                    • <a class="cardDate" data-tooltip="${new Date(result['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom"
                      href="single_post.php?id=${result['ID']}">${getDuration(new Date(result['date_created']))}</a>
                    • <span class="cardMode" data-tooltip-location="bottom" data-tooltip="${result['mode'] == 1? "Public" : result['mode'] == 2? "Followers" : "Private"}"
                      id="${result['mode']}">${mode_text}</span>
                  </h2>

                  <div class="share-content mw-100" data-id="${result['original']['ID']}">
                    <div class="feedCard container-fluid p-0">
                      <a href="profile.php?id=${result['original']['user']}" class="cardUserImg">
                          <img src="${result['original']['profile_image']}">
                      </a>

                      <div class="cardInfos">
                          <h2>
                            <a href="profile.php?id=${result['original']['user']}">${result['original']['display_name']}</a> 
                            talking about 
                            <a class="movie_title" id='${
                            JSON.stringify({
                              movie_id:result['original']['movie_id'],
                              movie_type:result['original']['movie_type']
                            })}' href="movie.php?id=${result['original']['movie_id']}&type=${result['original']['movie_type']}"></a> 
                            • <a class="cardDate" data-tooltip="${new Date(result['original']['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom"
                              href="single_post.php?id=${result['original']['ID']}">${getDuration(new Date(result['original']['date_created']))}</a>
                            • <span class="cardMode" data-tooltip-location="bottom" data-tooltip="${result['original']['mode'] == 1? "Public" : result['original']['mode'] == 2? "Followers" : "Private"}"
                              id="${result['original']['mode']}">${share_mode_text}</span>
                          </h2>

                          <p class="limited_text">${result['original']['content']}</p>   
                          
                          <div class="cardMedia">    
                            <img src="${(result['original']['media'])?result['original']['media']:''}" class="media">
                          </div>
                    </div>
                  </div>
                </div>
              </div>   
            </div>
          `;
          }
  
          div.prepend(html);
      });  
      
      loadMovieTitles();
      loadNoReactions();
  
      div.find(".cardReact").click(function(){
        ReactPost($(this));
      });
      div.find(".cardEdit").click(function(){
        EditPost($(this));
      });  
      div.find(".cardShare").click(function(){
        document.querySelector("#profileShareConfirmationModal").querySelector("#profileSharePostID").innerHTML = $(this).attr('id'); 
        $("#profileShareConfirmationModal").modal("show");
      });
      div.find(".cardComment").click(function(){
        let id = $(this).attr('id');
        window.location.href = `single_post.php?id=${id}`;
      });  
      document.querySelectorAll(".feedCard").forEach(box => {
        box.addEventListener("click", function(e) {
            let target = e.target;
            let postID = e.target.closest(".feedCard");
            if (postID != null) {
                postID = postID.id
            }
            if (target.nodeName == "IMG" && target.parentNode.classList.contains("cardMedia")) {
              e.preventDefault();
              let getURL = target.src;
              loadImage(getURL);
              showImageBox();
            } else {
              if (target.classList.contains("comment-like")) {
                e.preventDefault();
              } else if (target.classList.contains("comment-edit")) {
                e.preventDefault();
              } else if (target.classList.contains("comment-delete")) {
                e.preventDefault();
              } else if (target.classList.contains("see-more-reply") || target.parentNode.classList.contains("see-more-reply")) {
                e.preventDefault();
              } else if(postID != null) {
                window.location = `single_post.php?id=${postID}`;
              }
            }
        })
      });
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
  
  function getDuration(epochTime) {
    let timeDifference = Date.now() - epochTime - 3600000 * 7;
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

  $(document).ready(function(){
    updateSessionInfo();
  });

  $(".cardShare").unbind().click(function(){
    document.querySelector("#profileShareConfirmationModal").querySelector("#profileSharePostID").innerHTML = $(this).attr('id'); 
    $("#profileShareConfirmationModal").modal("show");
});

document.querySelector("#profileShareConfirmationModal").addEventListener("click", function(e) {
    if (e.target.classList.contains("share-confirm")) {
        e.preventDefault();
        sharePost(e.target.parentNode.parentNode.querySelector("#profileSharePostID").innerHTML, e.target.parentNode.parentNode.querySelector("#profile-post-share-type").value);
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

$(".cardReact").click(function(){
    ReactPost($(this));
  });
$(".cardEdit").click(function(){
    EditPost($(this));
});  
$(".cardShare").click(function(){
    document.querySelector("#shareConfirmationModal").querySelector("#sharePostID").innerHTML = $(this).attr('id'); 
    $("#shareConfirmationModal").modal("show");
});

document.querySelector(".image-box").addEventListener("click", function() {
  hideImageBox();
})

let getForm = document.querySelector("#add-info-form");

getForm.addEventListener("submit", function(e) {
    e.preventDefault();
    
    //Get input
    let info = getForm.querySelector("textarea[name = 'info']").value;
    let startYear = getForm.querySelector("input[name = 'start']").value;
    let endYear = getForm.querySelector("input[name = 'end']").value;
    
    //Check type
    let type;
    if (getForm.querySelector("#job").checked) {
      type = 0;
    } else {
      type = 1;
    }

    let csrf = getForm.querySelector("input[name = 'csrf']").value;

    //Get error display p
    let errorDisplays = [getForm.querySelector("#info-err"), getForm.querySelector("#type-err"),
    getForm.querySelector("#year-err"), getForm.querySelector("#create-execute-err")];

    errorDisplays.forEach(error => {
        error.innerHTML = "";
    })
    
    //ajax
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/profileRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            let errors = JSON.parse(this.responseText);
            if (errors.length != 0) {
                if (errors['info']) {
                    errorDisplays[0].innerHTML = errors['info'];
                }
                if (errors['type']) {
                    errorDisplays[1].innerHTML = errors['type'];
                }
                if (errors['year']) {
                    errorDisplays[2].innerHTML = errors['year'];
                }
                if (errors['execute_err']) {
                    errorDisplays[3].innerHTML = errors['execute_err'];
                }
            } else {
                location.reload();
            }
        }
    }
    xhr.send(`add_info&info=${info}&type=${type}&start=${startYear}&end=${endYear}&csrf=${csrf}`);
})

let editForm = document.querySelector("#edit-info-form");

document.querySelectorAll(".edit-info").forEach(info => {
    info.addEventListener("click", function() {
        let id;
        if (info.classList.contains("edit-info")) {
            id = info.getAttribute("data-id");
        } else {
            id = info.parentNode.getAttribute("data-id");
        }

        document.querySelector("#editInfo").querySelector("#editInfoID").innerHTML = id; 

        let xhr = new XMLHttpRequest();
        xhr.open("POST", "././includes/php/profileRequestHandler.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if(this.status == 200 && this.readyState == 4) {
                let result = JSON.parse(this.responseText);
                editForm.querySelector("textarea[name = 'info']").innerHTML = result['info'];
                editForm.querySelector("input[name = 'start']").value = result['start_year'];
                if (result['end_year'] != null) 
                    editForm.querySelector("input[name = 'end']").value = result['end_year'];
                if (result['type'] == 0) {
                    editForm.querySelector("#edit-job").checked = true;
                } else {
                    editForm.querySelector("#edit-education").checked = true;
                }
                $("#editInfo").modal("show");
            }
            else {
                $("#errorBox").modal("show");
            }
        }
        xhr.send(`single_info&id=${id}`);
    })
})

document.querySelectorAll(".btn-info-edit").forEach(btn => {
    btn.addEventListener("click", function(e) {
        e.preventDefault();
        let getType = e.target.getAttribute("data-type");
        let id = editForm.querySelector("#editInfoID").innerHTML;
        
        if (getType == 0) {
            //Get input
            let info = editForm.querySelector("textarea[name = 'info']").value;
            let startYear = editForm.querySelector("input[name = 'start']").value;
            let endYear = editForm.querySelector("input[name = 'end']").value;
            
            //Check type
            let type;
            if (editForm.querySelector("#edit-job").checked) {
                type = 0;
            } else {
                type = 1;
            }

            let csrf = editForm.querySelector("input[name = 'csrf']").value;

            //Get error display p
            let errorDisplays = [editForm.querySelector("#info-edit-err"), editForm.querySelector("#type-edit-err"),
            editForm.querySelector("#year-edit-err"), editForm.querySelector("#edit-execute-err")];

            errorDisplays.forEach(error => {
                error.innerHTML = "";
            })
            
            //ajax
            let xhr = new XMLHttpRequest();
            xhr.open("POST", "././includes/php/profileRequestHandler.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if(this.status == 200 && this.readyState == 4) {
                    let errors = JSON.parse(this.responseText);
                    if (errors.length != 0) {
                        if (errors['info']) {
                            errorDisplays[0].innerHTML = errors['info'];
                        }
                        if (errors['type']) {
                            errorDisplays[1].innerHTML = errors['type'];
                        }
                        if (errors['year']) {
                            errorDisplays[2].innerHTML = errors['year'];
                        }
                        if (errors['execute_err']) {
                            errorDisplays[3].innerHTML = errors['execute_err'];
                        }
                    } else {
                        location.reload();
                    }
                }
            }
            xhr.send(`edit_info&info=${info}&type=${type}&start=${startYear}&end=${endYear}&csrf=${csrf}&id=${id}`);
        } else {
            let csrf = editForm.querySelector("input[name = 'csrf']").value;

            let xhr = new XMLHttpRequest();
            xhr.open("POST", "././includes/php/profileRequestHandler.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if(this.status == 200 && this.readyState == 4) {
                    //console.log(this.responseText);
                    let errors = JSON.parse(this.responseText);
                    if (errors.length != 0) {
                        if (errors['execute_err']) {
                            errorDisplays[3].innerHTML = errors['execute_err'];
                        }
                    } else {
                        location.reload();
                    }
                }
            }
            xhr.send(`delete_info&id=${id}&csrf=${csrf}`);
        }
    })
})

document.querySelector(".profile-cover").addEventListener("click", function(e) {
  e.preventDefault();
  let getURL = e.target.src;
  loadImage(getURL);
  showImageBox();
})

document.querySelector(".profile-pic").addEventListener("click", function(e) {
  e.preventDefault();
  let getURL = e.target.src;
  loadImage(getURL);
  showImageBox();
})