<?php
    require_once 'includes/php/db.php';
    require_once 'classes/Post.php';
    include "includes/header.php";
    include "classes/Comment.php";
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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="includes/css/feed.css?v=<?=time();?>">
    <link rel="stylesheet" href="includes/css/style.css?v=<?=time();?>">
    <script
    src="https://code.jquery.com/jquery-3.6.0.min.js"
    integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
    crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./includes/css/chat.css">
    <link rel="stylesheet" href="./includes/css/emoji/emojionearea.min.css">
    <script src="./includes/js/emoji/emojionearea.min.js"></script>
</head>
<body>

<?php if (!empty($post->post)):?>
<?php 
    $post->post = $post->post[0];
    if ($post->post['mode'] == 1) $mode_text = '<i class="bi bi-globe"></i>';
    else if ($post->post['mode'] == 2) $mode_text = '<i class="bi bi-people"></i>';
    else if ($post->post['mode'] == 3) $mode_text = '<i class="bi bi-person"></i>';
?>
    <div class="container-fluid" style="margin-top: 80px;">
        <div class="row">
            <div class="col-1">
            </div>
            <div class="col-10">
                <?php if($post->post['share_from'] == null):?>
                <div class="feedCard container-fluid p-0" id="<?php echo htmlspecialchars($post->post_id) ?>">
                    <div class="d-flex pr-3">
                        <a href="profile.php?id=<?php echo $post->post['user'];?>" class="cardUserImg">
                            <img src="<?php echo htmlspecialchars($post->post['profile_image'])?>">
                        </a>

                        <div class="cardInfos container-fluid p-0">
                            <div>
                                <h2>
                                <a href="profile.php?id=<?php echo $post->post['user'];?>"><?php echo htmlspecialchars($post->post['display_name'])?></a> 
                                talking about 
                                <a class="movie_title" 
                                id='<?php echo json_encode(array('movie_id'=>$post->post['movie_id'],'movie_type'=>$post->post['movie_type']))?>' 
                                href="movie.php?id=<?php echo $post->post['movie_id']?>&type=<?php echo $post->post['movie_type']?>"></a> 
                                • <?php echo htmlspecialchars(getTimeString($post->post['date_created']))?>
                                • <span class="cardMode" data-tooltip-location="bottom" data-tooltip=<?php 
                                    if ($post->post['mode'] == 1)
                                        echo "Public";
                                    elseif ($post->post['mode'] == 2)
                                        echo "Followers";
                                    else
                                        echo "Private";
                                ?>
                                id="<?php echo htmlspecialchars($post->post['mode'])?>"><?php echo $mode_text ?></span>
                                </h2>
                            </div>

                            <p><?php echo htmlspecialchars($post->post['content'])?></p>      

                            <div class="cardMedia">  
                                <img src="<?php echo htmlspecialchars($post->post['media'])?>" onerror="this.style.display='none'">
                            </div>
                        </div>
                    </div>   
                
                    <div class="cardChin container-fluid">
                        <div class="row">
                            <div class="col text-center">
                                <div clas="d-flex">       
                                    <i class="cardReact far fa-thumbs-up" id="<?php echo htmlspecialchars($post->post['ID'])?>">100</i>
                                </div>
                            </div>
                            <div class="col text-center">       
                                <i class="cardComment bi bi-chat-text"></i>
                            </div>
                            <?php if($post->post['user'] == $_SESSION['user_id']):?>                            
                            <div class="col text-center">
                                <i class="cardEdit bi bi-pencil" id="<?php echo htmlspecialchars($post->post['ID'])?>"></i>
                            </div>
                            <?php endif?>                            
                            <div class="col text-center">      
                                <i class="singleCardShare fa fa-share" aria-hidden="true" id="<?php echo htmlspecialchars($post->post['ID'])?>"></i>
                            </div>
                        </div>
                    </div>

                    <div class="comment-section">
                        <hr class="ml-4 mr-4 mt-1 mb-1">
                        <div class="pl-4 pr-4 pt-2 pb-2">              
                            <div class='comment-input d-flex flex-row mt-2' data-id="<?php echo $post->post['ID'];?>">
                                <a class='comment-header-img'>
                                    <img class='rounded-circle d-inline-block profile-img' src='<?php echo $_SESSION['profile_img']; ?>'> 
                                </a>
                                <div class="comment-input-div ml-2">
                                    <input data-emoji-input='unicode' data-emojiable='true'
                                    type="text" class="form-control comment_inp" name="comment" placeholder="Write comment...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="feedCard container-fluid p-0" id="<?php echo htmlspecialchars($post->post_id) ?>">
                    <div class="d-flex pr-3">
                        <div class="cardUserImg">
                            <img src="<?php echo htmlspecialchars($post->post['profile_image'])?>">
                        </div>

                        <div class="cardInfos container-fluid p-0">
                            <div>
                                <h2>
                                <a href=""><?php echo htmlspecialchars($post->post['display_name'])?></a> 
                                • <?php echo htmlspecialchars(getTimeString($post->post['date_created']))?>
                                • <span class="cardMode" data-tooltip-location="bottom" data-tooltip=<?php 
                                    if ($post->post['mode'] == 1)
                                        echo "Public";
                                    elseif ($post->post['mode'] == 2)
                                        echo "Followers";
                                    else
                                        echo "Private";
                                ?>
                                id="<?php echo htmlspecialchars($post->post['mode'])?>"><?php echo $mode_text ?></span>
                                </h2>
                            </div>   
                        </div>

                        <div class="share-content">
                            <div class="feedCard container-fluid p-0">
                                <div class="d-flex pr-3">
                                    <a href="profile.php?id=<?php echo $post->post['original']['user'];?>" class="cardUserImg">
                                        <img src="<?php echo htmlspecialchars($post->post['original']['profile_image'])?>">
                                    </a>

                                    <div class="cardInfos container-fluid p-0">
                                        <div>
                                            <h2>
                                            <a href="profile.php?id=<?php echo $post->post['original']['user'];?>"><?php echo htmlspecialchars($post->post['original']['display_name'])?></a> 
                                            talking about 
                                            <a class="movie_title" id='
                                            <?php echo json_encode(array('movie_id'=>$post->post['original']['movie_id'],'movie_type'=>$post->post['original']['movie_type']))?>' href=""></a> 
                                            • <?php echo htmlspecialchars(getTimeString($post->post['original']['date_created']))?>
                                            • <span class="cardMode" data-tooltip-location="bottom" data-tooltip=<?php 
                                                if ($post->post['original']['mode'] == 1)
                                                    echo "Public";
                                                elseif ($post->post['original']['mode'] == 2)
                                                    echo "Followers";
                                                else
                                                    echo "Private";
                                            ?>
                                            id="<?php echo htmlspecialchars($post->post['original']['mode'])?>"><?php echo $mode_text ?></span>
                                            </h2>
                                        </div>

                                        <p><?php echo htmlspecialchars($post->post['original']['content'])?></p>      
                                        
                                        <img src="<?php echo htmlspecialchars($post->post['original']['media'])?>" onerror="this.style.display='none'">
                                    </div>
                                </div>   
                            </div>
                        </div>
                    </div>   
                
                    <div class="cardChin container-fluid">
                        <div class="row">
                            <div class="col text-center">
                                <div clas="d-flex">       
                                    <i class="cardReact far fa-thumbs-up" id="<?php echo htmlspecialchars($post->post['ID'])?>">100</i>
                                </div>
                            </div>
                            <div class="col text-center">  
                                <i class="cardComment bi bi-chat-text"></i>
                            </div>
                            <?php if($post->post['user'] == $_SESSION['user_id']):?>                            
                            <div class="col text-center">
                                <i class="cardEdit bi bi-pencil" id="<?php echo htmlspecialchars($post->post['ID'])?>"></i>
                            </div>
                            <?php endif?>
                        </div>
                    </div>

                    <div class="comment-section">
                        <hr class="ml-4 mr-4 mt-1 mb-1">
                        <div class="pl-4 pr-4 pt-2 pb-2">              
                            <div class='comment-input d-flex flex-row mt-2' data-id="<?php echo $post->post['ID'];?>">
                                <a class='comment-header-img'>
                                    <img class='rounded-circle d-inline-block profile-img' src='<?php echo $_SESSION['profile_img']; ?>'> 
                                </a>
                                <div class="comment-input-div ml-2">
                                    <input data-emoji-input='unicode' data-emojiable='true'
                                    type="text" class="form-control comment_inp" name="comment" placeholder="Write comment...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="col-1">
            </div>
<?php endif?>

<div class="modal fade" id="errorBox" tabindex="-1" role="dialog" aria-labelledby="errorBox" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Error occured! Please try again later!</p>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="singlePostErrorBox" tabindex="-1" role="dialog" aria-labelledby="errorBox" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Error</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Error occured! Please try again later!</p>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="singlePostShareConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Share this post?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <select class="form-control" id="single-post-share-type">
                    <option value="1">Public</option>
                    <option value="2">Followers</option>
                    <option value="3">Private</option>        
                </select>
            </div>
            <p class="d-none" id="singlePostSharePostID"></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <a role="button" class="btn btn-danger share-confirm" href="">Yes</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="commentDeleteModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                Are you sure want to delete this comment?
            </div>
            <p class="d-none" id="deleteCommentID"></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <a role="button" class="btn btn-danger comment-delete-confirm" href="">Yes</a>
            </div>
        </div>
    </div>
</div>

</body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script type="text/javascript" src="includes/js/themoviedb.js" charset="utf-8"></script>    
    <script type="text/javascript" src="includes/js/main.js" charset="utf-8"></script>    
    <script type="text/javascript" src="includes/js/img_preview.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/js/feed.js" charset="utf-8"></script>
    <script type="text/javascript" src="includes/js/singlePost.js" charset="utf-8"></script>
</html>


<script>
    $(document).ready(function() {
        $('.comment_inp').emojioneArea({
            search: false,
            inline: true,
            events: {
                    keyup: function (editor, event) {
                        if (event.which == 13) {
                            handleCommentSubmit(0, event.target);
                        }
                    }
                }
            });   
    })
</script>