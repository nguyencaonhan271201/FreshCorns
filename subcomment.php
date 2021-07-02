<?php 
    $subcomments = Comment::getSubCommentsByParent($conn, $post_id);
?>

<?php foreach ($subcomments as $comment): ?>
<div class="comment" id = "comment-<?php echo $comment['ID']?>" data-id="<?php echo $comment['ID']?>">
    <div class='d-flex flex-row comment-row mt-1 mb-1'>
        <a href='profile.php?id=<?php echo $comment['user'];?>' class='comment-header-img'>
            <img class='rounded-circle d-inline-block profile-img' src='<?php echo $comment['profile_image']; ?>'> 
        </a>
        <div class='comment-box ml-2 mr-0'>
            <div>
                <a href='profile.php?id=<?php echo $comment['user'];?>'><b><?php echo $comment['display_name']; ?></b></a>
                <p class='m-0'>
                    <?php echo $comment['content']; ?>
                </p>
            </div>
            <div class="comment-like-box d-none">
                <div>
                    <i class="fa fa-thumbs-up" aria-hidden="true"></i> 
                    <span class="comment-like-count"> 1</span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-column align-items-center justify-content-center comment-btn-block">
            <button class="post-btn btn btn-sm d-flex align-items-center dropdown-toggle" type="button"
            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-ellipsis-h" aria-hidden="true"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="edit-drop-down">
                <a class="dropdown-item" href="#" >Edit</a>
                <a class="dropdown-item" href="#" >Delete</a>
            </div>
        </div>
    </div>
    <div class="mt-0 ml-4">
        <span class="ml-4 comment-helper-btns">
            <a href="#" class="comment-like">Like</a>
            <span>·</span>
            <a href="#" class="comment-reply">Reply</a>
            <span>·</span>
            <span data-tooltip="<?php echo $comment['date_created']; ?>" data-tooltip-location="bottom">
                <?php echo getTimeString($comment['date_created']); ?>
            </span>
        </span>
        <div class='reply-input d-flex flex-row ml-4 mt-2' data-id="0">
            <a href='profile.php?id=<?php echo $comment['user'];?>' class='comment-header-img'>
                <img class='rounded-circle d-inline-block profile-img' src='<?php echo $comment['profile_image']; ?>'> 
            </a>
            <div class="reply-input-div ml-2">
                <input data-emoji-input='unicode' data-emojiable='true'
                type="text" class="form-control reply_inp" name="reply" placeholder="Write reply...">
            </div>
        </div>
        <div class="replies ml-4">
            <?php 
                include "subcomment.php";
            ?>
        </div>
    </div>
</div>
<?php endforeach; ?>