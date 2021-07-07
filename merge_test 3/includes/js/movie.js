const queryString = window.location.search;
const urlParams = new URLSearchParams(queryString);
const movie_id = urlParams.get('id');
const movie_type = urlParams.get('type');
const colorThief = new ColorThief();

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

    if (type==1){
        $('h1#year').text(new Date(result['release_date']).getFullYear()).css("color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`).css("background-color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`);
    
        $('h1#title').text(result['title']).css("color",`rgb(${palette[2][0]},${palette[2][1]},${palette[2][2]})`).css("background-color",`rgb(${palette[3][0]},${palette[3][1]},${palette[3][2]})`);
    }
    else {
        $('h1#year').text(new Date(result['first_air_date']).getFullYear()).css("color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`).css("background-color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`);
    
        $('h1#title').text(result['name']).css("color",`rgb(${palette[2][0]},${palette[2][1]},${palette[2][2]})`).css("background-color",`rgb(${palette[3][0]},${palette[3][1]},${palette[3][2]})`);
    }
    
    $('p#overview').text(result['overview']).css("color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`).css("background-color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);

    $('body').css("background-image",'url("'+theMovieDb.common.images_uri+'original'+result['backdrop_path']+'")');

    loadPosts_movie(palette);
}

function loadPosts_movie(palette){
    $.ajax({
        url: `includes/php/feed/ajax_post_get_movie.php?movie_id=${movie_id}&movie_type=${movie_type}`,
        success: function(data){
          //console.log(data)
          //$('#php_return').html(data);
          let results = JSON.parse(data);
          printPosts_simplified($('#posts'),results,palette);
        }
    });
}

function printPosts_simplified(div,results,palette){
    results.forEach(result=>{
        console.log(result);
        let html = `
            <div class="feedCard container-fluid p-0" id="${result['ID']}">
              <div class="d-flex pr-3">
                <div class="cardUserImg">
                    <img src="${result['profile_image']}">
                </div>
  
                <div class="cardInfos container-fluid p-0" id="${result['ID']}">
                    <h2>
                      <a href="">${result['display_name']}</a> 
                      • <a class="cardDate" href="single_post.php?id=${result['ID']}">${getDuration(new Date(result['date_created']))}</a>
                    </h2>
  
                    <p class="limited_text">${result['content']}</p>   
                    
                    <div class="cardMedia">    
                      <img src="${(result['media'])?result['media']:''}" class="media">
                    </div>
                </div>
              </div>   
            </div>         
            `;
        div.prepend(html);
    });
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