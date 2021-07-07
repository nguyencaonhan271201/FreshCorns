<?php 
    $post_types = [
        "Public" => 0,
        "Follower" => 1,
        "Only me" => 2
    ];
    include "includes/header.php";
    include "classes/Post.php";

    $errors = [];

    if (isset($_GET['edit_post'])) {
        $id = $_GET['edit_post'];
        $post = new Post($db->conn);
        $post->setID(intval($id));
        $post->getPost();
    } else if (isset($_POST['edit_post']) && $_POST['csrf'] == $_SESSION['csrf_token']) {
        $id = $_POST['edit_post'];
        $post = new Post($db->conn);
        $post->editPost($_POST, $_FILES, $errors);
    }

    $get_post = $post->post;
    $edit_post_image = $get_post['media'];

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

<div id="main-container" class="container-fluid p-0 m-0" style="height: 100vh;">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="edit-post-box d-flex flex-row m-3">
            <form method="post" action="edit_post.php" class="edit-post-form" enctype="multipart/form-data">
                <h4 class="text-center">Edit a post</h4>
                <hr>
                <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                <input name='post-old-image' type='text' value="<?php echo $edit_post_image; ?>" hidden/>
                <div class="form-group">
                    <select class="form-control" name="edit-post-type">
                        <?php 
                            foreach($post_types as $post_type => $value) {
                                $selected = "";
                                if (!empty($get_post['mode']) && $get_post['mode'] == $value)
                                    $selected = "selected";
                                echo "<option value='{$value}' {$selected}>{$post_type}</option>";
                            }
                        ?>
                    </select>
                    <p class="error"><?php 
                        if(isset($errors['edit-post-type']))
                            echo $errors['edit-post-type'];
                    ?></p>
                </div>

                <textarea data-emoji-input='unicode' data-emojiable='true'
                type="text" class="form-control mb-2" rows="4" name="edit_post_content" id="edit_post_content" 
                placeholder="<?php echo $_SESSION['name']. ', what are you thinking?'?>"><?php
                    if (isset($get_post['content'])) {
                        echo $get_post['content'];
                    }
                ?></textarea>

                <p class="error"><?php if(isset($errors['edit-post-content'])) {echo $errors['edit-post-content'];}?></p>
                
                <input id='edit-post-image' name='edit-post-image' accept="image/*" type='file' onchange="loadFileEditPost(event)" hidden/>
                <button class="btn btn-dark mr-1" role="button" id="btn-post-edit-image">
                    <i class="fa fa-file-image" aria-hidden="true"></i>
                </button>
                <p class="error"><?php if(isset($errors['edit-post-image'])) {echo $errors['edit-post-image'];}?></p>
                <div class="review-group-post-edit mt-2 text-center <?php if(!isset($edit_post_image) || $edit_post_image == "") {
                        echo " d-none";
                    }?>">
                    <img id="review-edit-post-image" src="<?php if(isset($edit_post_image) && $edit_post_image != "") {
                        echo $edit_post_image;
                    }?>">
                </div>

                <button class="btn btn-primary btn-block mt-2" role="submit" name="edit_post" id="edit_post_btn" disabled="true"
                value = "<?php if (isset($get_post['ID'])) {
                    echo $get_post['ID'];
                }?>">Post</button>

                <p class="error"><?php 
                    if(isset($errors['edit-post-execute-err']))
                        echo $errors['edit-post-execute-err'];
                ?></p>
            </form>
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
        $('#edit_post_content').emojioneArea({
            pickerPosition: "bottom",
            search: false,
        });   
    })
</script>
</body>
</html>





<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
<script src="includes/js/loadFile.js"></script>