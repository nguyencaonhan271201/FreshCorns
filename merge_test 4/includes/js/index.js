let img_links=[];
theMovieDb.movies.getPopular({}, data => {
    let results=JSON.parse(data)['results'];
    results.forEach(result=>{
        img_links.push(result['poster_path']);
    });

    theMovieDb.movies.getPopular({}, data => {
        let results=JSON.parse(data)['results']; 
        results.forEach(result=>{
            img_links.push(result['poster_path']);
        });
        img_links.sort((a, b) => 0.5 - Math.random());

        let i= 0;
        setInterval(function(){
            if (i+1 > img_links.length) i = 0;
            else i++;
            $('img').css("display","block");
            $('img').attr("src",theMovieDb.common.images_uri+'original'+img_links[i]);
            //colorize(document.querySelector('.banner img'));
        }, 5000);

    }, data =>{

    });
  }, data =>{
    
  });

$(document).ready(function(){
    $('.banner img').on('load',function(){
        colorize(colorThief.getPalette(this,5));
    });
});

function colorize(palette){
    console.log(palette);
    $('.navbar').removeClass('bg-light');
    $('.navbar').css("background-color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);
    $('.navbar .logo').css("fill",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`)
    $('.nav-link').attr("style",function(i,s) { 
        return (s || '') + `color: rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})!important;`;
    });
    $("body").css("background-color",`rgb(${palette[2][0]},${palette[2][1]},${palette[2][2]})`);
    $("h1,i").css("color",`rgb(${palette[3][0]},${palette[3][1]},${palette[3][2]})`);
    $("h2,h3").css("color",`rgb(${palette[4][0]},${palette[4][1]},${palette[4][2]})`);
}