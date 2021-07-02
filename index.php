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
<script src="./includes/js/emoji/emojionearea.min.js"></script>

<div id="main-container" class="container-fluid p-0 m-0 row" style="height: 100vh;">
    <div class="col-lg-3 d-none d-lg-block p-0 m-0">
            
    </div>

    <div class="col-lg-6 col-md-8 col-sm-12 p-0 m-0">
        <?php if($_SESSION['signed_in']):?>
        <div class="thinking-box d-flex flex-row m-3">
            <a href="profile.php?id=<?php echo $_SESSION['user_id'];?>">
                <img class="rounded-circle d-inline-block profile-img" src="<?php echo $_SESSION['profile_img'];?>"> 
            </a>
            <input type="text" class="form-control ml-2" disabled placeholder="<?php echo $_SESSION['name']. ', what are you thinking?'?>">
        </div>
        <div class="create-post-box d-flex flex-row m-3">
            <?php if (!isset($_GET['edit_post']) && !isset($_POST['edit_post'])) { 
                include 'create_post.php';
            }?>
        </div>
        <?php endif;?>
        <div class = "posts">
        <?php foreach ($post->posts as $post): ?>
        <div class='post-box m-3 p-0' id="post-<?php echo $post['ID']; ?>" data-id="<?php echo $post['ID']; ?>">
            <div class="d-flex justify-content-between pl-4 pr-4 pt-4 pb-1">
                <div class='d-flex flex-row'>
                    <a href='profile.php?id=<?php echo $post['user'];?>' class='post-header-img'>
                        <img class='rounded-circle d-inline-block profile-img' src='<?php echo $post['profile_image']; ?>'> 
                    </a>
                    <div class='post-header-info ml-2'>
                        <div>
                            <a href='profile.php?id=<?php echo $post['user'];?>'><b><?php echo $post['display_name']; ?></b></a>
                            <p class='m-0'>
                                <span data-tooltip="<?php echo $post['date_created']; ?>" data-tooltip-location="top">
                                    <?php echo getTimeString($post['date_created']); ?>
                                </span>
                                <span>·</span>
                                <?php if($post['mode'] == 0): ?>
                                <span data-tooltip="Public" data-tooltip-location="top">
                                    <i class='fa fa-globe' aria-hidden='true'></i>
                                </span>
                                <?php elseif($post['mode'] == 1): ?>
                                <span data-tooltip="Follower" data-tooltip-location="top">
                                    <i class='fas fa-user-friends' aria-hidden='true'></i>
                                </span>
                                <?php else: ?>
                                <span data-tooltip="Only me" data-tooltip-location="top">
                                    <i class='fa fa-lock' aria-hidden='true'></i>
                                </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <button class="post-btn btn btn-sm d-flex align-items-center dropdown-toggle" type="button"
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                        <a class="dropdown-item" href="edit_post.php?edit_post=<?php echo $post['ID']; ?>" >Edit Post</a>
                    </div>
                </div>
            </div>
            <p class='post-content mt-2 pl-4 pr-4 pb-2'>
                <span>
                <?php 
                    $content = $post['content'];
                    if (strlen($content) > 200) {
                        $display_content = htmlspecialchars(substr($content, 0, 200));
                        echo "{$display_content} ... ";
                    } else {
                        echo htmlspecialchars($content);
                    }
                ?>
                </span>
                <?php 
                    if (strlen($post['content']) > 200) {
                        echo "<a href='#' data-id={$post['ID']} data-type=0 class='read-more'><b>Read more</b></a>";
                    }
                ?>
            </p>
            <?php if($post['media'] != ""): ?>
                <a class='post-img m-0 p-0 pt-1 pb-1' href=''>
                    <img src='<?php echo $post['media'];?>' alt=''></img>
                </a>
            <?php endif; ?>
            <div class='reaction-block pl-4 pr-4 pt-2 pb-2 d-flex justify-content-between'>
                <span>
                    <i class='fa fa-thumbs-up' aria-hidden='true'></i>
                     1
                </span>
                <span>
                    3 lượt chia sẻ
                </span>
            </div>
            <hr class='ml-4 mr-4 mt-1 mb-1'>
            <div class='button-block pl-4 pr-4 pt-2 pb-2 d-flex justify-content-between align-items-center'>
                <button type='button' class='btn btn-like btn-dark text-center btn-block'>
                    <i class='fa fa-thumbs-up' aria-hidden='true'></i> 
                    <span> Like</span>
                </button>
                <button type='button' class='btn btn-comment btn-dark text-center btn-block'>
                    <i class='fa fa-comment' aria-hidden='true'></i> 
                    <span> Comment</span>
                </button>
                <button type='button' class='btn btn-share btn-dark text-center btn-block'>
                    <i class='fa fa-share' aria-hidden='true'></i> 
                    <span> Share</span>
                </button>
            </div>

            <p class='d-none full-content'><?php echo $post['content']; ?></p>

            <div class="comment-section">
                <hr class="ml-4 mr-4 mt-1 mb-1">
                <div class="pl-4 pr-4 pt-2 pb-2">              
                    <div class='comment-input d-flex flex-row mt-2' data-id="<?php echo $post['ID']; ?>">
                        <a href='profile.php?id=<?php echo $post['user'];?>' class='comment-header-img'>
                            <img class='rounded-circle d-inline-block profile-img' src='<?php echo $post['profile_image']; ?>'> 
                        </a>
                        <div class="comment-input-div ml-2">
                            <input data-emoji-input='unicode' data-emojiable='true'
                            type="text" class="form-control comment_inp" name="comment" placeholder="Write comment...">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>

    <div class="col-lg-3 col-md-4 d-md-block d-none p-0 m-0 chat-col">
        <div class="chat-col-list">
            <div class="chat-col-header d-flex justify-content-center">
                <h2 class="p-2 m-0" id="chat-h2">Chats</h2>
            </div>
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
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a role="button" class="btn btn-danger comment-delete-confirm" href="">Confirm</a>
            </div>
        </div>
    </div>
</div>

<div class="image-box">
    <img src="" alt="">
</div>

<script src="includes/js/post.js"></script>
<script src="includes/js/main.js"></script>

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
</body>
</html>

