<div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add a member</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add-chat-form" method="post" action="chat_add.php" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="members">
                        <div class="row">
                            <div class="col-12 text-right p-0 holder">
                                <input type="text" class="form-control" name="member-search" id="member-add-search" aria-describedby="helpId" placeholder="Search...">
                                <div id="member-search-add-results">
                            
                                </div>
                            </div>
                        </div>
                        
                        <!-- <div class="d-flex justify-content-between">
                            <p class="mb-1 mt-1">Members</p>
                        </div> -->
                        <div class="member-list mt-2 mb-1 pb-1" id="member-chosen-add-list">
                            
                        </div>
                    </div>
                    <p class="error err-execute"></p>
                </form>
            </div>
            <div class="modal-footer">
                <button class="btn btn-dark btn-block" role="submit" name="submit" id="btn-chat-room-add">Add</button>
            </div>
            </div>
        </div>

        <script src="includes/js/addChat.js"></script>
