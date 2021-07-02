<?php
    require_once 'db.php';
    require_once 'post.php';
    if (isset($_GET['id'])){
        $post = new Post($conn);
        $post->getSinglePost($_GET['id']);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css" integrity="sha512-MMojOrCQrqLg4Iarid2YMYyZ7pzjPeXKRvhW9nZqLo6kPBBTuvNET9DBVWptAo/Q20Fy11EIHM5ig4WlIrJfQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css?v=<?=time();?>">
</head>
<body>

<?php if (!empty($post->post)):?>
<?php $post->post = $post->post[0]?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-1">
            </div>
            <div class="col-10">
                <div class="feedCard container-fluid p-0" id="${result['ID']}">
                    <div class="d-flex pr-3">
                        <div class="cardUserImg">
                            <img src="<?php echo htmlspecialchars($post->post['profile_image'])?>">
                        </div>

                        <div class="cardInfos container-fluid p-0">
                            <div>
                                <h2><a href=""><?php echo htmlspecialchars($post->post['display_name'])?></a> is talking about <a class="movie_title" id='
                                <?php echo json_encode(array('movie_id'=>$post->post['movie_id'],'movie_type'=>$post->post['movie_type']))?>
                                
                                ' href=""></a> • <?php echo htmlspecialchars($post->post['date_created'])?></h2>
                            </div>

                            <p><?php echo htmlspecialchars($post->post['content'])?></p>      
                                
                            <img src="<?php echo htmlspecialchars($post->post['media'])?>">
                        </div>
                    </div>   
                
                    <div class="cardChin container-fluid">
                        <div class="row">
                        <div class="col-3 text-center">
                            ♥
                        </div>
                        <div class="col-3 text-center">              
                            comment
                        </div>
                        <div class="col-3 text-center">
                            edit
                        </div>
                        <div class="col-3 text-center">              
                            ↺
                        </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-1">
            </div>
<?php endif?>

</body>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="themoviedb.js" charset="utf-8"></script>    
    <script type="text/javascript" src="main.js" charset="utf-8"></script>
</html>