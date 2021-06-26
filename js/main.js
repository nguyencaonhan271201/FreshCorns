window.addEventListener('DOMContentLoaded', (event) => {    
    
    document.querySelector('.banner img').addEventListener('load', function() {
        colorize(this);
    });

    let i = 0;

    setInterval(function(){
        if (i+1 > 5) i = 0;
        else i++;
        $('img').attr("src",`imgs/${i}.png`);
        colorize(document.querySelector('.banner img'));
    }, 5000);

    function colorize(img){
        let colorThief = new ColorThief();

        if(img.complete){
            let tmp = colorThief.getPalette(img,50);
            
            $("body").css("background-color",`rgb(${tmp[0][0]},${tmp[0][1]},${tmp[0][2]})`);
            
            if ((tmp[0][0]*0.299 + tmp[0][1]*0.587 + tmp[0][2]*0.114) > 150) $("h1,h2,nav ul li").css("color","#000000");
            else $("h1,h2,h3,nav ul li").css("color","#ffffff");
    
            $("nav .logo svg").css("fill",`rgb(${tmp[1][0]},${tmp[1][1]},${tmp[1][2]})`);
            $(".button-1,i").css("color",`rgb(${tmp[1][0]},${tmp[1][1]},${tmp[1][2]})`);
            $(".button-1").css("border-color",`rgb(${tmp[1][0]},${tmp[1][1]},${tmp[1][2]})`);
            $("u").css("text-decoration-color",`rgb(${tmp[1][0]},${tmp[1][1]},${tmp[1][2]})`);
        }
    }
});