const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const movie_id = urlParams.get('id');
const movie_type = urlParams.get('type');
let movieIsSignedIn = false;

if (movie_type==1){
    theMovieDb.movies.getById({"id":movie_id}, data => {
        let result=JSON.parse(data);
        $('img#poster').attr('src',theMovieDb.common.images_uri+'original'+result['poster_path']).on('load',function(){
            let palette= colorThief.getPalette(this,5);
            printMovieDatas(result,palette,movie_type);
        });
      }, data =>{
        
      });
} else {
    theMovieDb.tv.getById({"id":movie_id},  data => {
        let result = JSON.parse(data);        
        $('img#poster').attr('src',theMovieDb.common.images_uri+'original'+result['poster_path']).on('load',function(){
            let palette= colorThief.getPalette(this,5);
            printMovieDatas(result,palette,movie_type);
        });
    }, data =>{
      
    });
}

function printMovieDatas(result,palette,type){
    console.log(result);
    //console.log(palette);   
    $('.navbar').removeClass('bg-light');
    $('.navbar').css("background-color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);
    $('.navbar .logo').css("fill",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`)
    $('.navbar-toggler').attr("style",function(i,s) { 
        return (s || '') + `border-color: rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})!important`;
    });
    $('.navbar-toggler-icon').attr("style",function(i,s) { 
        return (s || '') + `background-image: 
            url("data:image/svg+xml;charset=utf8,%3Csvg viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath stroke='rgba(${palette[0][0]},${palette[0][1]},${palette[0][2]}, 0.8)' stroke-width='2' stroke-linecap='round' stroke-miterlimit='10' d='M4 8h24M4 16h24M4 24h24'/%3E%3C/svg%3E")
            !important;
            }`;
    });
    $('.nav-link.dropdown-toggle').attr("style",function(i,s) { 
        return (s || '') + `color: rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})!important;`;
    });
    $('.nav-link').attr("style",function(i,s) { 
        return (s || '') + `color: rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})!important;`;
    });

    if (type==1){
        $('h1#year').text(new Date(result['release_date']).getFullYear()).css("color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`).css("background-color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`);
    
        $('h1#title').text(result['title']).css("color",`rgb(${palette[2][0]},${palette[2][1]},${palette[2][2]})`).css("background-color",`rgb(${palette[3][0]},${palette[3][1]},${palette[3][2]})`);
    }
    else {
        $('h1#year').text(new Date(result['first_air_date']).getFullYear()).css("color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`).css("background-color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`);
    
        $('h1#title').text(result['name']).css("color",`rgb(${palette[2][0]},${palette[2][1]},${palette[2][2]})`).css("background-color",`rgb(${palette[3][0]},${palette[3][1]},${palette[3][2]})`);
    }
    
    $('p#overview').text(result['overview']).css("color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`).css("background-color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);

    /*$('body').css("background-image",'url("'+theMovieDb.common.images_uri+'original'+result['backdrop_path']+'")');*/
    $('img.backdrop').attr('src',theMovieDb.common.images_uri+'original'+result['backdrop_path']);

    if ((palette[0][0]*0.299 + palette[0][1]*0.587 + palette[0][2]*0.114) > 150) $('img.backdrop#duo').duotone({
        gradientMap: `rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]}), rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`
    });
    else $('img.backdrop#duo').duotone({
        gradientMap: `rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]}), rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`
    });
    $('img.backdrop#duo').on('load', function(){
        console.log($(this));
        $(this).css('visibility','visible');
        setTimeout(function(){$('img.backdrop#og').css('visibility','visible');}, 10000);
        $(this).fadeOut(60000);
    });

    if (movieIsSignedIn) {
        loadPosts_movie(palette);
    }
}

function loadPosts_movie(palette){
    $.ajax({
        url: `includes/php/feed/ajax_post_get_movie.php?movie_id=${movie_id}&movie_type=${movie_type}`,
        success: function(data){
          //console.log(data)
          //$('#php_return').html(data);
          let results = JSON.parse(data);
          console.log(results);
          printPosts_simplified($('#posts'),results,palette);
        }
    });
}

Date.prototype.addHours= function(h){
    this.setHours(this.getHours() + h);
    return this;
}

function printPosts_simplified(div,results,palette){
    results.forEach(result=>{
        if (result['share_from']!=null) return true;
        let html = `
            <div class="feedCard container-fluid p-0" id="${result['ID']}"
                data-tooltip="Click to the time to view the whole content of the post" data-tooltip-location="top">
                <a href="profile.php?id=${result['user']}" class="cardUserImg">
                    <img src="${result['profile_image']}">
                </a>
  
                <div class="cardInfos mw-100" id="${result['ID']}">
                    <h2>
                      <a href="profile.php?id=${result['user']}">${result['display_name']}</a> 
                      • <a class="cardDate" data-tooltip="${new Date(result['date_created']).addHours(7).toLocaleString()}" data-tooltip-location="bottom"
                      href="single_post.php?id=${result['ID']}">${getDuration(new Date(result['date_created']))}</a>
                    </h2>
  
                    <p class="limited_text">${result['content']}</p>   
                    
                    <div class="cardMedia">    
                      <img src="${(result['media'])?result['media']:''}" class="media">
                    </div>
              </div>   
            </div>         
            `;
        div.prepend(html);
    });
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

//hide navbar
let prevScrollpos = window.pageYOffset;
window.onscroll = function() {
  let currentScrollPos = window.pageYOffset;
  if (prevScrollpos > currentScrollPos) {
    document.querySelector(".navbar").style.top="0";
} else {
    document.querySelector(".navbar").style.top="-20vh";
}
  prevScrollpos = currentScrollPos;
}