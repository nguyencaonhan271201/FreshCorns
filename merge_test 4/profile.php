<?php
    include 'includes/header.php';
    include 'includes/php/db.php';
    
    //End dummy
    if(isset($_GET['order'])){
        $order = $_GET['order'];
        $profile_user_id = $_GET['id'];
        $profile = new Profile($db->conn, $profile_user_id);
        //var_dump($profile);
        if($order == 'unfollow'){
            $check_result = $profile->deleteRelationship($_SESSION['user_id']);
            if (!$check_result) {
                echo "<script>
                    $('#errorBox').modal('show');
                </script>";
            }
        }
        elseif ($order == 'follow') {
            $check_result = $profile->addRelationship($_SESSION['user_id']);
            if (!$check_result) {
                echo "<script>
                    $('#errorBox').modal('show');
                </script>";
            }
        }
    }

    if(isset($_GET['id'])){
        $profile_user_id = $_GET['id'];
    }
    else $profile_user_id = $_SESSION['user_id'];

    //$profile_user_id = 3;

    $profile = new Profile($db->conn, $profile_user_id);
    
    $profile->getFollowing();
    $profile->getFollowers();

    $posts = Post::getPostsProfile($db->conn, $profile_user_id);

    $infos = Profile::getInfo($db->conn, $profile_user_id);

    $csrf_token = bin2hex(random_bytes(32));
    $_SESSION['csrf_token'] = $csrf_token;
?>

<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<link rel="stylesheet" href="./includes/css/emoji/emojionearea.min.css">
<link rel="stylesheet" href="./includes/css/feed.css">
<script src="./includes/js/emoji/emojionearea.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css" integrity="sha512-MMojOrCQrqLg4Iarid2YMYyZ7pzjPeXKRvhW9nZqLo6kPBBTuvNET9DBVWptAo/Q20Fy11EIHM5ig4WlIrJfQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js" integrity="sha512-uto9mlQzrs59VwILcLiRYeLKPPbS/bT71da/OEBYEwcdNUk8jYIy+D176RYoop1Da+f9mvkYrmj5MCLZWEtQuA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<?php if($profile->user_id == null): ?>
    <div class='jumbotron jumbotron-fluid' style="text-align: center;">
        <h1>Profile not found!</h1></div>
<?php else: ?>
    <div class="container" style="width: auto;">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="profile-header">
                    <div class="cover" style="
                                            position: relative;
                                            border-radius: 25rem 25rem 0 0;
                                            ">
                        <figure>
                        <!-- Cover -->
                            <img src="<?php echo $profile->profile_cover; ?>" class="img-fluid profile-cover" alt="profile cover" 
                                style="height: 50vh;
                                        width: 100%;
                                        object-fit: cover;">
                            <div class="cover-body d-flex justify-content-between align-items-center" style="
                                        margin: 5px 15px;
                                        z-index: 2;
                                        position: absolute;
                                        bottom: 10px;
                                        width: 95%;">
                                <div>
                            <!-- Profile pic -->
                                    <img class="profile-pic" src="<?php echo $profile->profile_image; ?>" alt="profile_img" style="
                                                                border-radius: 50%;
                                                                width: 100px;
                                                                height: 100px;">
                                    <span class="profile-name" style="
                                                            margin-left: 20px;
                                                            font-size: 20px;
                                                            font-weight: 600;">
                            <!-- Username -->
                                    <?php echo $profile->display_name; ?></span>
                                </div>
                                <div class="d-md-block">
                            <!-- Button -->
                                <?php if($_SESSION['signed_in']): ?>
                                    <!-- Edit profile -->
                                    <?php if($_SESSION['user_id'] == $profile_user_id): ?>
                                    <button class="btn btn-primary btn-icon-text btn-edit-profile">
                                        <a href="edit_profile.php" style="color: white;">Edit profile</a>
                                    </button>
                                    <!-- Unfollow -->
                                    <?php elseif(in_array($_SESSION['user_id'], $profile->followers)): ?>
                                        <button class="btn btn-danger btn-unfollow" on>
                                        <a href="profile.php?order=<?php echo 'unfollow' ?>&id=<?php echo $profile_user_id ?>" style="color: Yellow;">Unfollow</a>
                                    </button>
                                    <!-- Follow -->
                                    <?php elseif (!in_array($_SESSION['user_id'], $profile->followers)): ?>
                                        <button class="btn btn-success btn-follow">
                                        <a href="profile.php?order=<?php echo 'follow' ?>&id=<?php echo $profile_user_id ?>" style="color: white;">Follow</a>
                                    <?php endif ?>
                                <?php endif ?>
                                </div>
                            </div>

                        </figure>           
                    </div>
                <!-- Header links icon using svg -->
                <div class="container">
                    <ul class="row align-items-center mt-3 mt-md-0">
                        <!-- <li class="header-link-item d-flex align-items-center active">
                            <a class="pt-1px d-none d-md-block" href="#">Timeline</a>
                        </li>
                        <li class="header-link-item ml-3 pl-3 border-left d-flex align-items-center">
                            <a class="pt-1px d-none d-md-block" href="#">About</a>
                        </li> -->
                        <li class="header-link-item ml-3 pl-3 border-left d-flex align-items-center">
                            <!-- Following count show -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   Following <?php echo count($profile->following);?>
                                </button>
                                <!-- Show following -->
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php
                                        foreach ($profile->following as $user_id) {
                                            $info = new Profile($db->conn, $user_id);
                                            echo "<a class='dropdown-item' href='profile.php?id=".$user_id."'>
                                                <img src='".$info->profile_image."' class='img-xs rounded-circle' style='height: 5vh; width: 5vh; margin-right: 1rem' alt='profile_img'>" 
                                            .$info->display_name."</a>";
                                        } 
                                    ?>
                                </div>
                            </div>
                            <!-- <a class="pt-1px d-none d-md-block" href="#">Following <span class="text-muted tx-12">
                                <?php //echo count($profile->following);?>
                            </span></a> -->
                        </li>
                        <li class="header-link-item ml-3 pl-3 border-left d-flex align-items-center">
                            <!-- Followers count show -->
                            <div class="dropdown">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                   Followers <?php echo count($profile->followers);?>
                                </button>
                                <!-- Show followers -->
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <?php
                                        foreach ($profile->followers as $user_id) {
                                            $info = new Profile($conn, $user_id);
                                            echo "<a class='dropdown-item' href='profile.php?id=".$user_id."'>
                                                <img src='".$info->profile_image."' class='img-xs rounded-circle' style='height: 5vh; width: 5vh; margin-right: 1rem' alt='profile_img'>" 
                                            .$info->display_name."</a>";
                                        } 
                                    ?>
                                </div>
                            </div>
                            <!-- <a class="pt-1px d-none d-md-block" href="#">Followers <span class="text-muted tx-12">
                                <?php //echo count($profile->followers);?>
                            </span></a> -->
                        </li>
                    </ul>
                </div>
                </div>
            </div>
        </div>
        
        <div class="row profile-body" style="padding: 2rem; ">
            <!-- left wrapper start -->
            <div class="d-none d-md-block col-md-5 col-xl-4 left-wrapper">
                <div class="card rounded">
                    <div class="card-body">
                        <h4 class="card-title mb-0">About</h4>
                        <div class="d-flex align-items-center justify-content-between flex-column mt-2">
                            <!-- <div class="dropdown">
                                <button class="btn p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="profilerentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-lg text-muted pb-3px">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="19" cy="12" r="1"></circle>
                                        <circle cx="5" cy="12" r="1"></circle>
                                    </svg>
                                </button>
                            </div> -->
                            <p class="text-muted"><?php echo $profile->description;?></p>
                        </div>
                        <!--Description  -->
                        <div class="mt-0">
                            <label class="tx-11 font-weight-bold mb-0 text-uppercase">Joined:</label>
                            <!--Joined  -->
                            <p class="text-muted"><?php
                                $timestamp = strtotime($profile->date_joined, strtotime('+7 hours'));
                                echo date("d/m/Y", $timestamp);
                            ?></p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 font-weight-bold mb-0 text-uppercase">Email:</label>
                            <!-- Mail -->
                            <p class="text-muted"><?php echo $profile->email;?></p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 font-weight-bold mb-0 text-uppercase">Info:</label>
                            <!-- Mail -->
                            <?php foreach($infos as $info): ?>
                                <?php if ($_SESSION['user_id'] == $profile_user_id): ?>
                                <a class="edit-info"
                                data-id="<?php echo $info['ID']?>">
                                    <p class="text-muted" data-tooltip="Click to edit" data-tooltip-location = "top"><?php 
                                    if($info['type'] == 0) {
                                        echo '<i class="fa fa-briefcase" aria-hidden="true"></i>';
                                    } else {
                                        echo '<i class="fa fa-graduation-cap" aria-hidden="true"></i>';
                                    } ?> 
                                    <?php 
                                        echo $info['info'];

                                        if ($info['end_year'] == null) {
                                            echo " ({$info['start_year']} - now)";
                                        } else {
                                            echo " ({$info['start_year']} - {$info['end_year']})";
                                        }
                                    ?></p>
                                </a>
                                <?php else: ?>
                                    <p class="text-muted"><?php 
                                    if($info['type'] == 0) {
                                        echo '<i class="fa fa-briefcase" aria-hidden="true"></i>';
                                    } else {
                                        echo '<i class="fa fa-graduation-cap" aria-hidden="true"></i>';
                                    } ?> 
                                    <?php 
                                        echo $info['info'];

                                        if ($info['end_year'] == null) {
                                            echo " ({$info['start_year']} - now)";
                                        } else {
                                            echo " ({$info['start_year']} - {$info['end_year']})";
                                        }
                                    ?></p>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                        <?php if($_SESSION['user_id'] == $profile_user_id): ?>
                            <a data-toggle="modal" data-target="#addInfo" class="mt-2 btn btn-dark btn-block" href="#" role="button">Add Info</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Left wraped end -->
            <div class="col-md-7 col-xl-4 middle-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="ml-2" id="posts">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
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

<div class="modal fade" id="profileShareConfirmationModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Share this post?</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <select class="form-control" id="profile-post-share-type">
                    <option value="1">Public</option>
                    <option value="2">Followers</option>
                    <option value="3">Private</option>        
                </select>
            </div>
            <p class="d-none" id="profileSharePostID"></p>
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

<div class="modal fade" id="addInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Work & Education Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <form method="post" action="profile.php" id="add-info-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <!-- <label for="display">Display Name</label> -->
                        <textarea class="form-control" name="info" placeholder="Info"></textarea>
                        <p class="error" id="info-err"></p>
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <div class="form-check">
                            <input type="radio" name="type" id="job" value="0" <?php 
                                if((isset($_POST['type']) && $_POST['type'] == 0) || !isset($_POST['type']))
                                    echo "checked";
                            ?>>
                            <label class="form-check-label">
                                Work
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="type" id="education" value="1" <?php 
                                if(isset($_POST['type']) && $_POST['type'] == '1')
                                    echo "checked";
                            ?>>
                            <label class="form-check-label">
                                Education
                            </label>
                        </div>
                        <p class="error" id="type-err"></p>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-1">
                                <input class="form-control" type="number" name="start" placeholder="Start Year">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-1">
                                <input class="form-control" type="number" name="end" placeholder="End Year (optional)">
                            </div>
                        </div>
                        <p class="error ml-3" id="year-err"></p>
                    </div>
                    
                    <p class="error mt-1" id="create-execute-err"></p>
                    <button class="btn btn-success btn-block" role="submit" name="submit">Add Info</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editInfo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Work & Education Info</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-left">
                <form method="post" action="profile.php" id="edit-info-form" enctype="multipart/form-data">
                    <input type="hidden" name="csrf" value="<?php echo $csrf_token; ?>">
                    <div class="form-group">
                        <!-- <label for="display">Display Name</label> -->
                        <textarea class="form-control" name="info" placeholder="Info"></textarea>
                        <p class="error" id="info-edit-err"></p>
                    </div>

                    <div class="form-group">
                        <label for="type">Type</label>
                        <div class="form-check">
                            <input type="radio" name="type" id="edit-job" value="0" <?php 
                                if((isset($_POST['type']) && $_POST['type'] == 0) || !isset($_POST['type']))
                                    echo "checked";
                            ?>>
                            <label class="form-check-label">
                                Work
                            </label>
                        </div>
                        <div class="form-check">
                            <input type="radio" name="type" id="edit-education" value="1" <?php 
                                if(isset($_POST['type']) && $_POST['type'] == '1')
                                    echo "checked";
                            ?>>
                            <label class="form-check-label">
                                Education
                            </label>
                        </div>
                        <p class="error" id="type-edit-err"></p>
                    </div>

                    <div class="row">
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-1">
                                <input class="form-control" type="number" name="start" placeholder="Start Year">
                            </div>
                        </div>
                        <div class="col-12 col-sm-6">
                            <div class="form-group mb-1">
                                <input class="form-control" type="number" name="end" placeholder="End Year (optional)">
                            </div>
                        </div>
                        <p class="error ml-3" id="year-edit-err"></p>
                    </div>
                    
                    <p class="error mt-1" id="edit-execute-err"></p>
                    <div class="row">
                        <div class="col-6">
                            <button class="btn btn-success btn-block btn-info-edit" role="button" data-type="0">Update</button>
                        </div>
                        <div class="col-6">
                            <button class="btn btn-danger btn-block btn-info-edit" role="button" data-type="1">Delete</button>
                        </div>
                    </div>

                    <p class="d-none" id="editInfoID"></p>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="image-box">
    <img src="" alt="">
</div>


<script src="includes/js/main.js" type="text/javascript"></script>
<script type="text/javascript" src="includes/js/themoviedb.js" charset="utf-8"></script>    
<script src="includes/js/profile.js" type="text/javascript"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js" integrity="sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns" crossorigin="anonymous"></script>
