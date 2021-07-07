<div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Chat Room</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit-chat-form" method="post" action="chat_create.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <label for="roomname">Room Name</label>
                        <input class="form-control data-tooltip" type="text" name="roomname" id="roomname" placeholder="Room name" 
                        value="">
                        <p class="error err-roomname"></p>
                    </div>
                    <div class="form-group">
                        <label for="image">Chat Room Thumbnail <em>(Optional)</em></label>
                        <input id="image-choose" class="form-control" type="file" name="image" accept="image/*" onchange="loadFileEditChat(event)">
                        <small class="form-text text-muted">
                            A default image will be used in case you do not use your own. Maximum size allowed: 5MB
                        </small>
                        <p class="error err-thumbnail"></p>
                        <div class="review-group mt-2 text-center">
                            <img id="review-image-edit">
                        </div>
                    </div>
                </form>
                <p class="error err-execute"></p>
            </div>
            <div class="modal-footer">
                <div class="row" style="width: 100%">
                    <div class="col-6 pr-1">
                        <button class="btn btn-info btn-block" role="submit" name="submit" id="btn-chat-room-edit-submit">Edit</button>
                    </div>
                    <div class="col-6 pl-1">
                        <button class="btn btn-danger btn-block" role="submit" name="submit" id="btn-chat-room-edit-cancel">Cancel</button>
                    </div>
                </div>
            </div>
            </div>
        </div>

        <script src="includes/js/editChat.js"></script>
        <script src="includes/js/loadFile.js"></script>