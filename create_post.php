<?php 
    $post_types = [
        "Public" => 0,
        "Follower" => 1,
        "Only me" => 2
    ];

    $post_image = "";
?>

<form method="post" action="feeds.php" class="post-form" enctype="multipart/form-data">
    <h4 class="text-center">Create a post</h4>
    <hr>
    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
    <div class="form-group">
        <select class="form-control" name="post-type">
            <?php 
                foreach($post_types as $post_type => $value) {
                    $selected = "";
                    if (isset($_POST['post-type']) && $_POST['post-type'] == $value)
                        $selected = "selected";
                    echo "<option value='{$value}' {$selected}>{$post_type}</option>";
                }
            ?>
        </select>
        <p class="error"><?php 
            if(isset($errors['post-type']))
                echo $errors['post-type'];
        ?></p>
    </div>

    <textarea data-emoji-input='unicode' data-emojiable='true'
    type="text" class="form-control mb-2" rows="4" name="post_content" id="post_content" 
    placeholder="<?php echo $_SESSION['name']. ', what are you thinking?'?>"><?php
        if (isset($_POST['post_content'])) {
            echo $_POST['post_content'];
        }
    ?></textarea>

    <p class="error"><?php if(isset($errors['post-content'])) {echo $errors['post-content'];}?></p>
    
    <input id='post-image' name='post-image' accept="image/*" type='file' onchange="loadFileCreatePost(event)" hidden/>
    <button class="btn btn-dark mr-1" role="button" id="btn-post-image">
        <i class="fa fa-file-image" aria-hidden="true"></i>
    </button>
    <p class="error"><?php if(isset($errors['post-image'])) {echo $errors['post-image'];}?></p>
    <div class="review-group-post mt-2 text-center <?php if(!isset($post_image) || $post_image == "") {
            echo " d-none";
        }?>">
        <img id="review-post-image" src="<?php if(isset($post_image) && $post_image != "") {
            echo $post_image;
        }?>">
    </div>

    <button class="btn btn-primary btn-block mt-2" role="submit" name="create_post" id="create_post_btn" disabled="true">Post</button>

    <p class="error"><?php 
        if(isset($errors['create-post-execute-err']))
            echo $errors['create-post-execute-err'];
    ?></p>
</form>

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="includes/js/loadFile.js"></script>

<script>
    $(document).ready(function() {
        $('#post_content').emojioneArea({
            pickerPosition: "bottom",
            search: false,
        });   
    })
</script>