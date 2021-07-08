<?php
    require_once 'includes/php/db.php';    
    include "includes/header.php";
?>    
    <link rel="stylesheet" href="includes/css/index.css">

    <div class="banner" id="landing">
        <div class="img_box">
            <img src="imgs/0.png" crossorigin="anonymous" onerror="this.style.display='none'">
        </div>

        <div class="tagline_box">
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

        <h1 id="intro">we are Fresh Corns</h1>

    </div>

    
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.min.js" integrity="sha512-mMe7BAZPOkGbq+zhRBMNV3Q+5ZDzcUEOJoUYXbHpEcODkDBYbttaW7P108jX66AQgwgsAjvlP4Ayb/XLJZfmsg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="includes/js/main.js"></script>
    <script type="text/javascript" src="includes/js/themoviedb.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/js/index.js" charset="utf-8"></script>
<?php 
    include "includes/footer.php";
?>