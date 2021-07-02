        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Create Chat Room</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="create-chat-form" method="post" action="chat_create.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="members">
                        <div class="row">
                            <div class="col-12 text-right p-0 holder">
                                <input type="text" class="form-control" name="member-search" id="member-search" aria-describedby="helpId" placeholder="Search...">
                                <div id="member-search-results">
                            
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <p class="mb-1 mt-1">Members</p>
                            <p class="mb-1 mt-1 font-italic"><span id="numberOfMembers">0</span> selected</p>
                        </div>
                        <div class="member-list mb-1 pb-1" id="member-chosen-list">
                            
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="roomname">Room Name</label>
                        <input class="form-control data-tooltip" type="text" name="roomname" id="roomname" placeholder="Room name" 
                        value="" disabled>
                        <p class="error err-roomname"></p>
                    </div>
                    <div class="form-group">
                        <label for="image">Chat Room Thumbnail <em>(Optional)</em></label>
                        <input id="image-choose" class="form-control" type="file" name="image" accept="image/*" onchange="loadFileCreateChat(event)" disabled>
                        <small class="form-text text-muted">
                            A default image will be used in case you do not use your own. Maximum size allowed: 5MB
                        </small>
                        <p class="error err-thumbnail"></p>
                        <div class="review-group mt-2 text-center d-none">
                            <img id="review-image">
                        </div>
                    </div>
                </form>
                <p class="error err-execute"></p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark btn-block" role="submit" name="submit" id="btn-chat-room-create">Create Room</button>
            </div>
            </div>
        </div>

        <script src="includes/js/createChat.js"></script>
        <script src="includes/js/loadFile.js"></script>