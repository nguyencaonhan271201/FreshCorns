let img_links=[];
theMovieDb.movies.getPopular({}, data => {
    let results=JSON.parse(data)['results'];
    results.forEach(result=>{
        if (result['backdrop_path'] != null) img_links.push(result['backdrop_path']);
    });

    theMovieDb.tv.getPopular({}, data => {
        let results=JSON.parse(data)['results']; 
        results.forEach(result=>{
            if (result['backdrop_path'] != null) img_links.push(result['backdrop_path']);
        });
        img_links.sort((a, b) => 0.5 - Math.random());

        let i= 0;
        setInterval(function(){
            if (i+1 > img_links.length) i = 0;
            else i++;

            $('img#carousel').css("display","block");
            if (document.querySelector("img").classList.contains("fade-in")) {
                $("img#carousel").removeClass("fade-in");
                $('img#carousel').addClass("fade-out");
            }
            
            setTimeout(function() {
                $('img#carousel').attr("src",theMovieDb.common.images_uri+'original'+img_links[i]);
            }, 300);
            
            setTimeout(function() {
                if (document.querySelector("img#carousel").classList.contains("fade-out")) {
                    $("img#carousel").removeClass("fade-out");
                }
                $('img#carousel').addClass("fade-in");
            }, 600)
        }, 5000);

    }, data =>{

    });
  }, data =>{
    
  });

$(document).ready(function(){
    $('.navbar .logo').css("fill",`#0082FF`);
    $('.banner img#carousel').on('load',function(){
        colorize(colorThief.getPalette(this,5));
    });
});

function colorize(palette){
    //console.log(palette);
    $('.navbar').removeClass('bg-light');
    $('.navbar').css("background-color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`);
    $('.navbar .logo').css("fill",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`)
    $('.nav-link').attr("style",function(i,s) { 
        return (s || '') + `color: rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})!important;`;
    });

    $("body,.button-1,footer").css("background-color",`rgb(${palette[2][0]},${palette[2][1]},${palette[2][2]})`);

    $("h1,i,a").css("color",`rgb(${palette[3][0]},${palette[3][1]},${palette[3][2]})`);
    $("h2,h3").css("color",`rgb(${palette[4][0]},${palette[4][1]},${palette[4][2]})`);

    $(".button-1").css("color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);
    $(".button-1").css("border-color",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);

    $("#intro").css("color",`rgb(${palette[0][0]},${palette[0][1]},${palette[0][2]})`);    
    $(".h1-logo").css("fill",`rgb(${palette[1][0]},${palette[1][1]},${palette[1][2]})`);
}