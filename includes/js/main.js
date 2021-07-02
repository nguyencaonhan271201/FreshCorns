let prevScrollpos = window.pageYOffset;
window.onscroll = function() {
    // let currentScrollPos = window.pageYOffset;
    // if (prevScrollpos > currentScrollPos) {
    //     document.querySelector(".navbar").style.top="-1px";
    // } else {
    //     document.querySelector(".navbar").style.top="-20vh";
    // }
    // prevScrollpos = currentScrollPos;
}

//Handle header search
let headerSearchInput = document.querySelector("#header-search");
let headerSearchResultZone = document.querySelector("#header-search-result")

function ajaxHeaderSearch(query) {
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "././includes/php/mainRequestHandler.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onload = function() {
        if(this.status == 200 && this.readyState == 4) {
            outputHeaderSearchResult(JSON.parse(this.responseText));
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
        headerSearchResultZone.innerHTML = "";
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
    headerSearchResultZone.innerHTML = "";
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
        headerSearchResultZone.innerHTML += html;
    })
}