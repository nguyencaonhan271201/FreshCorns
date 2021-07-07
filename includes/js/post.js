// let my_id;
// let my_name;
// let my_image;
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

if (document.querySelector("#btn-post-image") != null) {
    document.querySelector("#btn-post-image").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById('post-image').click();
    })
}

if (document.querySelector("#btn-post-edit-image") != null) {
    document.querySelector("#btn-post-edit-image").addEventListener("click", function(e) {
        e.preventDefault();
        document.getElementById('edit-post-image').click();
    })
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

    getTrendingMovies();
})

document.querySelectorAll(".read-more").forEach(item => {
    item.addEventListener("click", function(e) {
        e.preventDefault();
        let getANode = e.target.parentElement;
        let get_type = getANode.getAttribute("data-type");
        let get_full_content = getANode.parentElement.parentElement.querySelector(".full-content").innerText;
        let get_short_content = get_full_content.substring(0, 200) + "... ";
        let content_block = getANode.closest(".post-content").querySelector("span");
        if (get_type == 0) {
            content_block.innerHTML = get_full_content + " ";
            getANode.innerHTML = "<b>Collapse</b>";
            getANode.setAttribute("data-type", 1);
        } else {
            content_block.innerHTML = get_short_content;
            getANode.innerHTML = "<b>Read more</b>";
            getANode.setAttribute("data-type", 0);
        }
    })
})

document.querySelector(".image-box").addEventListener("click", function() {
    hideImageBox();
})

document.querySelector("#shareConfirmationModal").addEventListener("click", function(e) {
    if (e.target.classList.contains("share-confirm")) {
        e.preventDefault();
        sharePost(e.target.parentNode.parentNode.querySelector("#sharePostID").innerHTML, e.target.parentNode.parentNode.querySelector("#share-type").value);
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
                $("#errorBox").modal("show");
            } else {
                location.reload();
            }
        }
    }
    xhr.send(`post_share&postID=${postID}&mode=${mode}`);
    $("#shareConfirmationModal").modal("hide");
}

let movies = [];
let TVs = [];
function getTrendingMovies() {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/mainRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            IDs = JSON.parse(this.responseText);
            let getCompleted = 0;
            for (let i = 0; i < IDs.length; i++) {
                let getID = IDs[i]['movie_id'];
                let getType = IDs[i]['movie_type'];
                if (getType == 0) {
                    theMovieDb.tv.getById({"id": getID}, data => {
                        TVs.push(JSON.parse(data));
                        getCompleted++;
                        if (getCompleted == IDs.length) {
                            loadTrendingMoviesToDOM();
                        }
                    }, data => {});
                } else {
                    theMovieDb.movies.getById({"id": getID}, data => {
                        movies.push(JSON.parse(data));
                        getCompleted++;
                        if (getCompleted == IDs.length) {
                            loadTrendingMoviesToDOM();
                        }
                    }, data => {});
                }
                
            }
        }
    }
    xhr.send(`trending_movies`);
}

function loadTrendingMoviesToDOM() {
    let movieBlock = document.querySelector(".films-items .films");
    let TVBlock = document.querySelector(".films-items .TVs");
    for (let i = 0; i < movies.length; i++) {
        filmTitle = movies[i]['title'];
        filmID = movies[i]['id'];
        filmPoster = movies[i]['poster_path'] != null? 'https://image.tmdb.org/t/p/original' + movies[i]['poster_path'] : "https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/866069.png?alt=media&token=fe8a87b5-c062-496d-a7d2-60ad9559fcb3";
        movieBlock.innerHTML += `
            <a href="movie.php?id=${filmID}&type=1" class="film-item-a">
                <div class="film-box d-flex flex-row">
                    <img class="d-inline-block film-img" src="${filmPoster}">
                    <div class="ml-2 flex-title">
                        <h6 class="m-0">${filmTitle}</h6>
                    </div>
                </div>
            </a>
        `
    }
    for (let i = 0; i < TVs.length; i++) {
        filmTitle = TVs[i]['name'];
        filmID = TVs[i]['id'];
        filmPoster = TVs[i]['poster_path'] != null? 'https://image.tmdb.org/t/p/original' + TVs[i]['poster_path'] : "https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/866069.png?alt=media&token=fe8a87b5-c062-496d-a7d2-60ad9559fcb3";
        TVBlock.innerHTML += `
            <a href="movie.php?id=${filmID}&type=0" class="film-item-a">
                <div class="film-box d-flex flex-row">
                    <img class="d-inline-block film-img" src="${filmPoster}">
                    <div class="ml-2 flex-title">
                        <h6 class="m-0">${filmTitle}</h6>
                    </div>
                </div>
            </a>
        `
    }
}

function loadMoviesInfo() {
    return new Promise((resolve, reject) => {
        for (let i = 0; i < movies.length; i++) {
            let getID = movies[i]['movie_id'];
            theMovieDb.movies.getById({"id": getID}, data => {
                movies[i]['info'] = JSON.parse(data);
            }, data => {});
        }
    });
}

