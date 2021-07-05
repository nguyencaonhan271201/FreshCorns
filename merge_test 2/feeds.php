<?php
    include "includes/header.php";
    include "classes/Post.php";
    include "classes/Comment.php";
    $errors = [];

    if (isset($_POST['create_post']) && $_POST['csrf'] == $_SESSION['csrf_token']) {
        $post = new Post($db->conn);
        $post->createPost($_SESSION, $_POST, $_FILES, $errors);
    } elseif (!isset($post)) {
        $post = new Post($db->conn);
    }

    $post->getPosts();

    $chat_room = new ChatRoom($db->conn);
    if ($_SESSION['signed_in']) {
        $chat_rooms = $chat_room->getRoomsOfUser($_SESSION['user_id']);
    } else $chat_rooms = [];
    
    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<link rel="stylesheet" href="./includes/css/chat.css">
<link rel="stylesheet" href="./includes/css/emoji/emojionearea.min.css">
<link rel="stylesheet" href="./feed/style.css">
<script src="./includes/js/emoji/emojionearea.min.js"></script>

<div id="main-container" class="feed-container container-fluid p-0 m-0 row" style="height: 100vh;">
    <div class="left-box col-lg-3 d-none d-lg-block p-0 m-0">
        <div class="films-col-list">
            <div class="films-list p-0 m-0">
                
                <div class="films-items">
                    <h6 class="ml-2">Movies</h6>
                    <hr class="m-0">
                    <div class="films">

                    </div>
                    <h6 class="ml-2">TV Series</h6>
                    <hr class="m-0">
                    <div class="TVs">
                    
                    </div>
                </div>
                <div></div>
            </div>

        </div>
    </div>

    <div class="mid-box mx-auto col-lg-6 col-md-8 col-sm-12 p-0 m-0">
        <div class="create-post-box d-flex flex-row m-3">
            <?php if (!isset($_GET['edit_post']) && !isset($_POST['edit_post'])) { 
                include 'create_post.php';
            }?>
        </div>
        <div class = "posts pl-3 pr-3">

            <ul class="nav nav-tabs justify-content-center" id="myTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <a class="nav-link active" id="public-tab" data-toggle="tab" href="#public" role="tab" aria-controls="public" aria-selected="true">Public</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link" id="following-tab" data-toggle="tab" href="#following" role="tab" aria-controls="following" aria-selected="false">Following</a>
                </li>
            </ul>

            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="public" role="tabpanel" aria-labelledby="public-tab">Public</div>
                <div class="tab-pane fade" id="following" role="tabpanel" aria-labelledby="following-tab">Following</div>
            </div>

            <div id="mainFeed"></div>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 d-md-block d-none p-0 m-0 chat-col">
        <div class="chat-col-list">
            <!-- <div class="chat-col-header d-flex justify-content-center">
                <h2 class="p-2 m-0" id="chat-h2">Chats</h2>
            </div> -->
            <div class="contact-list p-0 m-0">
                <?php if(count($chat_rooms) == 0): ?>
                <h6 class="text-center mt-2">No chats created</h6>
                <?php else: ?>
                    <div class="contact-items">
                        <?php 
                            $single_rooms = [];
                            $groups = [];
                            foreach($chat_rooms as $chat_room) {
                                if ($chat_room['type'] == 1) {
                                    array_push($single_rooms, $chat_room);
                                } else {
                                    array_push($groups, $chat_room);
                                }
                            }
                        ?>
                        <?php if(count($single_rooms) > 0): ?>
                            <h6 class="ml-2">Contacts</h6>
                            <hr class="m-0">
                            <?php foreach($single_rooms as $chat_room): ?>
                            <?php 
                                $room_id = $chat_room['ID'];
                                foreach($chat_room['members'] as $member) {
                                    if ($member['display_name'] != $_SESSION['name']) {
                                        $room_name = $member['display_name'];
                                        $room_thumbnail = $member['profile_image'];
                                        break;
                                    }
                                }
                            ?>
                            <a class="contact-list-a" href="chat.php?room=<?php echo $room_id; ?>">
                                <div class="contact-list-item mt-1">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-4 col-sm-12 d-flex align-items-center justify-content-center">
                                            <img class="chat-title-image rounded-circle"
                                            src="<?php echo $room_thumbnail; ?>">
                                        </div>
                                        <div class="col-lg-10 col-md-8 d-md-block pr-1 contact-item-name">
                                            <h6 class="p-0 m-0"><?php echo $room_name; ?></h6>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <?php if(count($groups) > 0): ?>
                            <h6 class="ml-2 mt-2">Groups</h6>
                            <hr class="m-0">
                            <?php foreach($groups as $chat_room): ?>
                            <?php 
                                $room_id = $chat_room['ID'];
                                $room_name = $chat_room['room_name'];
                                $room_thumbnail = $chat_room['thumbnail'];
                                $room_members_name = "";
                                foreach($chat_room['members'] as $member) {
                                    if ($member['display_name'] != $_SESSION['name']) {
                                        $room_members_name .= $member['display_name'] . ', ';
                                    }
                                }
                                $room_members_name = substr($room_members_name, 0, 50) . "...";
                            ?>
                            <a class="contact-list-a" href="chat.php?room=<?php echo $room_id; ?>">
                                <div class="contact-list-item mt-1">
                                    <div class="row">
                                        <div class="col-lg-2 col-md-4 col-sm-12 d-flex align-items-center justify-content-center">
                                            <img class="chat-title-image rounded-circle"
                                            src="<?php echo $room_thumbnail; ?>">
                                        </div>
                                        <div class="col-lg-10 col-md-8 d-md-block pr-1 contact-item-name">
                                            <div>
                                                <h6 class="p-0 m-0"><?php echo $room_name; ?></h6>
                                                <p class="font-italic contact-group-p p-0 mt-1 m-0"><?php echo $room_members_name; ?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </a>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                <div></div>
            </div>

        </div>
    </div>
</div>

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

<div class="modal fade" id="shareConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Share this post?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <select class="form-control" id="share-type">
                    <option value="0">Public</option>
                    <option value="1">Follower</option>
                    <option value="2">Only me</option>        
                </select>
            </div>
            <p class="d-none" id="sharePostID"></p>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
                <a role="button" class="btn btn-danger share-confirm" href="">Yes</a>
            </div>
        </div>
    </div>
</div>

<div class="image-box">
    <img src="" alt="">
</div>

<script src="includes/js/post.js"></script>
<script type="text/javascript" src="feed/main.js" charset="utf-8"></script>
<script type="text/javascript" src="feed/img_preview.js" charset="utf-8"></script>
<script type="text/javascript" src="feed/feed.js" charset="utf-8"></script>
<script type="text/javascript" src="includes/js/themoviedb.js" charset="utf-8"></script>    
<script src="includes/js/main.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>

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

    $(document).ready(function() {
        $('#post_content').emojioneArea({
            pickerPosition: "bottom",
            search: false,
        });   
    })
</script>
</body>
</html>

