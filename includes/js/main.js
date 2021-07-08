let my_id;
let my_name;
let my_image;
let absolute_path;
let colorThief = new ColorThief();

let indexIsSignedIn = true;

let preFix = "/FreshCorns-Tan/merge_test%204"
let isIndexPage = (window.location.pathname == preFix + '/' 
|| window.location.pathname == preFix + '/index' || window.location.pathname == preFix + '/index.php');


//let prevScrollpos = window.pageYOffset;
let moviesSearch = [];
/*window.onscroll = function() {
    // let currentScrollPos = window.pageYOffset;
    // if (prevScrollpos > currentScrollPos) {
    //     document.querySelector(".navbar").style.top="-1px";
    // } else {
    //     document.querySelector(".navbar").style.top="-20vh";
    // }
    // prevScrollpos = currentScrollPos;
}*/

//Handle header search
let headerSearchInput = document.querySelector("#header-search");
let headerSearchResultZone = document.querySelector("#header-search-result")
let headerSearchUserZone = document.querySelector("#header-search-user")
let headerSearchMoviesZone = document.querySelector("#header-search-movies")

function ajaxHeaderSearch(query) {
    moviesSearch = [];
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/mainRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            outputHeaderSearchResult(JSON.parse(this.responseText));
            theMovieDb.search.getMulti({"query" : query},
            data => {
                moviesSearch = JSON.parse(data)['results'];
                outputHeaderSearchMovies(query);
            }, data => {});
        }
    }
    xhr.send(`header_search=true&q=${query}`);
}

headerSearchInput.addEventListener("keyup", function(e) {
    e.preventDefault();
    let input = headerSearchInput.value;
    if (input != "") {
        ajaxHeaderSearch(input);
    } else {
        headerSearchUserZone.innerHTML = "";
        headerSearchMoviesZone.innerHTML = "";
    }
})

headerSearchInput.addEventListener("focus", function(e) {
    e.preventDefault();
    let input = headerSearchInput.value;
    if (input != "") {
        ajaxHeaderSearch(input);
    }
})

function outputHeaderSearchResult(results) {
    if (!isIndexPage && indexIsSignedIn) {
        headerSearchUserZone.innerHTML = `
        <h6 class="ml-2 text-left mt-1">People</h6>
        <hr class="m-0">
        `;
        results.forEach(result => {
            let html = `<a href="profile.php?id=${result.ID}">
            <div class="member search-result d-flex flex-row justify-content-start align-items-center" data-id=${result.ID}>
                <img class="member-search-img rounded-circle mr-2 ml-2" src="${result.profile_image}">
                <div class="ml-2 d-flex flex-column align-items-center justify-content-start">
                    <div class="text-left">
                        <p class="mt-1 mb-0" id="search-display-name">${result.display_name}</p>
                    </div>
                </div>
            </div>
            </a>`;
            headerSearchUserZone.innerHTML += html;
        })
    } else {
        headerSearchUserZone.innerHTML = "";
    }

    headerSearchUserZone.innerHTML += `
        <h6 class="ml-2 text-left mt-1">Movies & Series</h6>
        <hr class="m-0">
    `;
}

function outputHeaderSearchMovies(query) {
    headerSearchMoviesZone.innerHTML = "";
    console.log(moviesSearch);
    moviesSearch.forEach(result => {
        if (result['media_type'] == 'tv' || result['media_type'] == 'movie') {
            let getHref = `movie.php?id=${result['id']}&type=${result['media_type'] == 'tv'? 0 : 1}`;
            let filmTitle = result['media_type'] == 'tv'? result['name'] : result['title'];
            let filmPoster = result['poster_path'] != null? 'https://image.tmdb.org/t/p/w185' + result['poster_path'] : "https://firebasestorage.googleapis.com/v0/b/cs204finalproj.appspot.com/o/866069.png?alt=media&token=fe8a87b5-c062-496d-a7d2-60ad9559fcb3";
            console.log(filmTitle);
            if ((result['media_type'] != 'tv' && result['media_type'] != 'movie') || filmTitle.toLowerCase().indexOf(query.toLowerCase()) != -1) {
                let html = `<a href="${getHref}">
                <div class="member search-result d-flex flex-row justify-content-start align-items-center" data-id=${result.ID}>
                    <img class="member-search-img rounded-circle mr-2 ml-2" src="${filmPoster}">
                    <div class="ml-2 d-flex flex-column align-items-center justify-content-start">
                        <div class="text-left">
                            <p class="mt-1 mb-0" id="search-display-name">${filmTitle}</p>
                        </div>
                    </div>
                </div>
                </a>`;
                headerSearchMoviesZone.innerHTML += html;
            }
        }
    })
}

$(document).ready(function(){
    loadMovieTitles();
    loadNoReactions();
    updateSessionInfo();
})

function isFunction(fn){
    return typeof fn === 'function'
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
            loadPosts();
        }
    }
    xhr.send(`page=chat`);
}
  
function loadMovieTitles(){
$("a.movie_title").each(function(i,obj){
let result = JSON.parse($(this).attr('id'));
//console.log(result);
if (result['movie_type']) theMovieDb.movies.getById({"id":result['movie_id']}, data => {
$(this).html(JSON.parse(data)['title']);
}, data =>{

});
else theMovieDb.tv.getById({"id":result['movie_id']},  data => {
    $(this).html(JSON.parse(data)['name']);
}, data =>{

});

});
}

function loadNoReactions(){
    $(".cardReact").each(function(i,obj){
        let temp = $(this);
        $.ajax({
            url:`includes/php/feed/ajax_react_post_get.php?post_id=${temp.attr('id')}`,
            success:function(data){
                temp.html(data);
            }
        });
        $.ajax({
            url:`includes/php/feed/ajax_reacted_post_check.php?post_id=${temp.attr('id')}`,
            success:function(data){
                if(data!=0) {
                    temp.removeClass("far");
                    temp.addClass("fas");
                } else {
                temp.removeClass("fas");
                temp.addClass("far");
                }
            }
        });
    });
}