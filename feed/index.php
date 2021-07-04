<?php header('Access-Control-Allow-Origin: *');?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css" integrity="sha512-MMojOrCQrqLg4Iarid2YMYyZ7pzjPeXKRvhW9nZqLo6kPBBTuvNET9DBVWptAo/Q20Fy11EIHM5ig4WlIrJfQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style.css?v=<?=time();?>">
</head>
<body>
    <div id="php_return"></div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-1">
            </div>
            <div class="col-10">

                <div class="postDiv container-fluid d-flex p-0">
                    <div class="postUserImg">
                        <img src="https://upload.wikimedia.org/wikipedia/vi/f/f5/Dua_Lipa_-_Future_Nostalgia_%28Official_Album_Cover%29.png">
                    </div>    


                    <div class="container-fluid">
                        <form class="postForm" name="form" action="" method="post" enctype="multipart/form-data">
                            <div class="d-flex">                                           
                                <h2>talking about</h2>                                
                                <div class="container-fluid">
                                    <select class="postMvVl"></select>
                                </div>
                            </div>

                            <textarea class="postCap" name="caption"></textarea>                           

                            <div class="postFile_preview_div">
                                <img class="postFile_preview">
                            </div>
                            <label>
                                <i class="bi bi-image"></i>
                                <input class="postFile" type="file" name="create_user_img" accept="image/png, image/jpeg" onchange='previewFile(this,$(".postForm .postFile_preview"));'>
                            </label>
                            
                            <select class="postMode">
                                <option value="1">Public</option>
                                <option value="2">Followers</option>
                                <option value="3">Private</option>
                            </select>

                            <button>submit</button>
                        </form>
                    </div>
                </div>

                <div id="mainFeed"></div>

            </div>
            <div class="col-1">
            </div>
        </div>
    </div>
    
</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="themoviedb.js" charset="utf-8"></script>    
    <script type="text/javascript" src="main.js" charset="utf-8"></script>
    <script type="text/javascript" src="img_preview.js" charset="utf-8"></script>
    <script type="text/javascript" src="feed.js" charset="utf-8"></script>
</html>