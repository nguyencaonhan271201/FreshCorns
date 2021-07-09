<?php
    session_start();
    $_SESSION = [];
    session_destroy();
    require_once 'includes/php/db.php';    
    include "includes/header.php";
?>    

    <link rel="stylesheet" href="includes/css/index.css">

    <div class="banner" id="landing">
        <div class="img_box">
            <img id="carousel" class="fade-in" src="" crossorigin="anonymous" onerror="this.style.display='none'">
        </div>

        <div class="tagline_box">
            <h1>Fresh Corns</h1>
            <h2>RT stands for iRrelevanT</h2>
            <button class ="button-1" onclick="location.href='signin.php'">Join our comunity</button>
        </div>
    </div>   

    <div class="banner d-flex flex-column justify-content-center align-items-center text-center" id="info">
        <h1><u>Constructive</u> criticism. No bullsh*ts.</h1>
        <h2>A social media platform for those cinephiles who have taste & a common sense.</h2>
        <h3>No. You may not join us mr Snyder.</h3>

        <div>
            <i class="bi bi-arrow-up-circle"></i>
            <i class="bi bi-chat"></i>
            <i class="bi bi-heart"></i>
        </div>

        <h1 id="intro">we are <span class="h1-logo"><?php echo file_get_contents("assets/images/logo.svg");?></span> Fresh Corns</h1>

    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="includes/js/main.js"></script>
    <script type="text/javascript" src="includes/js/themoviedb.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/js/index.js" charset="utf-8"></script>

    <footer>
        <div class="d-flex justify-content-center align-items-center m-1">
            <h3 class="text-center m-0">This project came to life thanks to <a class="credit" href="https://developers.themoviedb.org/3/getting-started/introduction" target="_blank"><img src="https://www.themoviedb.org/assets/2/v4/logos/v2/blue_short-8e7b30f73a4020692ccca9c88bafe5dcb6f8a62a4c6bc55cd9ba82bb2cd95f6c.svg"></a> , <a href="https://github.com/cavestri/themoviedb-javascript-library" target="_blank">its js wrapper</a>, <a href="https://selectize.dev/" target="_blank">Selectize</a>, 
            <a href="https://lokeshdhakar.com/projects/color-thief/" target="_blank">Color Thief</a>, <a href="https://github.com/mervick/emojionearea" target="_blank">Emojionearea</a> and <a href="https://fabricelejeune.github.io/jquery-duotone/" target="_blank">jquery-duotone</a> as in our implementation of js, php, mysql, and <a class="credit" href="https://firebase.google.com" target="_blank"><img src="https://upload.wikimedia.org/wikipedia/commons/thumb/3/37/Firebase_Logo.svg/1200px-Firebase_Logo.svg.png"></a></h3>
        </div>
    </footer>
</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
</html>