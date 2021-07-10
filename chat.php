<?php
    include "includes/header.php";  

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<link rel="stylesheet" href="./includes/css/chat.css">
<link rel="stylesheet" href="./includes/css/emoji/emojionearea.min.css">
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script src="./includes/js/emoji/emojionearea.min.js"></script>

<div id="chat-main-container" class="container-fluid p-0 mt-0 row">
    <div class="col-2 col-sm-3 chat-room-list p-0">
        <div class="d-flex justify-content-between">
            <h2 class="p-2 m-0" id="chat-h2">Chat</h2>
            <a name="" id="btn-create-room" class="btn btn-info d-flex align-items-center" href="#" role="button" 
            data-toggle="modal" data-target="#createChatRoom">
                <i class="fa fa-plus-circle" aria-hidden="true"></i>
            </a>
        </div>
        <div class="chat-list p-0 m-0">
            <div class="chat-list-items">
                
            </div>
            <div></div>
        </div>
    </div>

    <div class="col-10 col-sm-9 chat-room-content p-0">
        <div class="chat-room-title d-flex align-items-center justify-content-between p-2">
            <div class="d-flex align-items-center justify-content-start">
                <img class="chat-title-image rounded-circle" src="">
                <h5 class="ml-2"></h5>
            </div>
            <div class="d-none" id="room-edit">
                <div class="dropdown">
                    <button class="btn btn-info d-flex align-items-center dropdown-toggle" type="button" id="edit-drop-down" 
                    data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fa fa-info-circle" aria-hidden="true"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                        <a class="dropdown-item" href="#" role="button" 
                        data-toggle="modal" data-target="#leaveRoom">Leave Room</a>
                        <a class="dropdown-item" href="#" role="button" 
                        data-toggle="modal" data-target="#addMember">Add Member</a>
                        <a class="dropdown-item" id="edit-chat-dropdown" href="#" role="button" 
                        data-toggle="modal" data-target="#editChatRoom">Edit</a>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="chat-list p-0 m-0">
            <div></div>
            <div class="messages">
            </div>
        </div>
        <div class="chat-input-box p-1">
            <form method="post" action="chat.php" id="messageForm" class="d-flex flex-row" enctype="multipart/form-data">
                <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                <input id='messageImage' type='file' hidden/>
                <button class="btn btn-dark mr-1"  role="button" id="btn-file-image">
                    <i class="fa fa-file-image" aria-hidden="true"></i>
                </button>
                <input data-emoji-input='unicode' data-emojiable='true'
                type="text" class="form-control mr-1" name="message" id="message_inp" placeholder="Aa">
                <button class="btn btn-dark" role="submit" name="submit" id="btn-message">
                    <i class="fa fa-heart" aria-hidden="true" id="btn-message-icon" data-type=0></i>
                </button>
            </form>
        </div>
    </div>

    <div class="image-box">
        <img src="" alt="">
    </div>

    <div class="modal fade" id="createChatRoom" tabindex="-1" role="dialog" aria-labelledby="createChatRoom" aria-hidden="true">
        <?php 
            include "chat/chat_create.php";
        ?>
    </div>

    <div class="modal fade" id="editChatRoom" tabindex="-1" role="dialog" aria-labelledby="editChatRoom" aria-hidden="true">
        <?php 
            include "chat/chat_edit.php";
        ?>
    </div>

    <div class="modal fade" id="addMember" tabindex="-1" role="dialog" aria-labelledby="addMember" aria-hidden="true">
        <?php 
            include "chat/chat_add.php";
        ?>
    </div>

    <div class="modal fade" id="leaveRoom" tabindex="-1" role="dialog" aria-labelledby="leaveRoom" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure want to leave room <b id="modal_room_name"></b>? </p>
                </div>
                <div class="modal-footer">
                    <div class="row" style="width: 100%">
                        <div class="col-6 pr-1">
                            <button class="btn btn-success btn-block" role="submit" name="submit" id="btn-leave-approve">Yes</button>
                        </div>
                        <div class="col-6 pl-1">
                            <button class="btn btn-danger btn-block" role="submit" name="submit" id="btn-leave-cancel">No</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="leaveError" tabindex="-1" role="dialog" aria-labelledby="leaveError" aria-hidden="true">
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
</div>

<!-- The core Firebase JS SDK is always required and must be listed first -->
<script src="https://www.gstatic.com/firebasejs/8.6.7/firebase-app.js"></script>

<!-- TODO: Add SDKs for Firebase products that you want to use
     https://firebase.google.com/docs/web/setup#available-libraries -->
<script src="https://www.gstatic.com/firebasejs/8.6.7/firebase-database.js"></script>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

<script src="includes/js/chat.js"></script>

<script>
    $(document).ready(function() {
        $('#message_inp').emojioneArea({
            search: false,
            inline: true,
            events: {
                    // Enter key as submit button --> working
                    keyup: function (editor, event) {
                        if (event.which == 13) {
                            if ($('#btn-message-icon').attr("data-type") == 1) {
                                handleFormSubmit();
                            }
                        } else {
                            if (editor[0].innerText.trim() == '') {
                                $('#btn-message-icon').attr("class", "fa fa-heart");
                                $('#btn-message-icon').attr("data-type", 0);
                            } else {
                                $('#btn-message-icon').attr("class", "fa fa-paper-plane");
                                $('#btn-message-icon').attr("data-type", 1);
                            }
                        }
                    }
                }
        });   
    })
</script>

</body>
</html>

<script src="includes/js/main.js"></script>