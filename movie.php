<?php
    require_once 'includes/php/db.php';    
    include "includes/header.php";
?>
<link rel="stylesheet" href="includes/css/movie.css?v=<?=time();?>">
<link rel="stylesheet" href="includes/css/feed.css?v=<?=time();?>">
    <img id="backdrop" crossorigin="anonymous">

    <img id="poster" class="img-fluid w-25" crossorigin="anonymous">
    
    <h1 id ="year"></h1>  
    <div id="basic_infos">  
        <h1 id="title"></h1>
        <p id="overview"></p>
    </div>

    <div id="posts">
    </div>

</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
     
    <script src="https://cdnjs.cloudflare.com/ajax/libs/color-thief/2.3.2/color-thief.min.js" integrity="sha512-mMe7BAZPOkGbq+zhRBMNV3Q+5ZDzcUEOJoUYXbHpEcODkDBYbttaW7P108jX66AQgwgsAjvlP4Ayb/XLJZfmsg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
       
    <script src="includes/js/main.js"></script>
    <script type="text/javascript" src="includes/js/themoviedb.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/js/jquery.duotone.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/js/movie.js" charset="utf-8"></script>
</html>

<?php 
    if ($_SESSION['signed_in']) {
        echo "<script> movieIsSignedIn = true; indexIsSignedIn = true; </script>";
    } else {
        echo "<script> movieIsSignedIn = false; indexIsSignedIn = false; </script>";
    }
?>