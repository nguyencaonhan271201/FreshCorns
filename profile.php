<?php
    include 'include/header.php';
    include 'classes/Profile.php';
    
    //Dummy database for $_SESSION;
    $_SESSION['signed_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'trang';
    $_SESSION['name'] = 'Ho Trang';
    $_SESSION['profile_image'] = 'imgs/0.png';
    //End dummy
    if(isset($_GET['order'])){
        $order = $_GET['order'];
        $profile_user_id = $_GET['user_id'];
        $profile = new Profile($conn, $profile_user_id);
        //var_dump($profile);
        if($order == 'unfollow'){
            $profile->deleteRelationship($_SESSION['user_id']);
        }
        elseif ($order == 'follow') {
            $profile->addRelationship($_SESSION['user_id']);
        }
    }

    if(isset($_GET['user_id'])){
        $profile_user_id = $_GET['user_id'];
    }
    else $profile_user_id = $_SESSION['user_id'];

    //$profile_user_id = 3;

    $profile = new Profile($conn, $profile_user_id);
    
    $profile->getFollowing();
    $profile->getFollowers();

    //var_dump($profile);

?>
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
                            <img src="<?php echo $profile->profile_cover; ?>" class="img-fluid" alt="profile cover" 
                                style="height: 50vh;
                                        width: 100%">
                            <div class="cover-body d-flex justify-content-between align-items-center" style="
                                        margin: 5px 15px;
                                        z-index: 2;
                                        position: absolute;
                                        bottom: 10px;
                                        width: 95%;">
                                <div>
                            <!-- Profile pic -->
                                    <img class="profile-pic" src="<?php echo $profile->profile_img; ?>" alt="profile_img" style="
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
                                        <a href="" style="color: white;">Edit profile</a>
                                    </button>
                                    <!-- Unfollow -->
                                    <?php elseif(in_array($_SESSION['user_id'], $profile->followers)): ?>
                                        <button class="btn btn-danger btn-unfollow" on>
                                        <a href="profile.php?order=<?php echo 'unfollow' ?>&user_id=<?php echo $profile_user_id ?>" style="color: Yellow;">Unfollow</a>
                                    </button>
                                    <!-- Follow -->
                                    <?php elseif (!in_array($_SESSION['user_id'], $profile->followers)): ?>
                                        <button class="btn btn-success btn-follow">
                                        <a href="profile.php?order=<?php echo 'follow' ?>&user_id=<?php echo $profile_user_id ?>" style="color: white;">Follow</a>
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
                                            $info = new Profile($conn, $user_id);
                                            echo "<a class='dropdown-item' href='profile.php?user_id=".$user_id."'>
                                                <img src='".$profile->profile_img." class='img-xs rounded-circle' style='height: 5vh; width: 5vh; margin-right: 1rem' alt='profile_img'>" 
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
                                            echo "<a class='dropdown-item' href='profile.php?user_id=".$user_id."'>
                                                <img src='".$profile->profile_img." class='img-xs rounded-circle' style='height: 5vh; width: 5vh; margin-right: 1rem' alt='profile_img'>" 
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
            <div class="d-none d-md-block col-md-4 col-xl-3 left-wrapper">
                <div class="card rounded">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="card-title mb-0">About</h6>
                            <!-- <div class="dropdown">
                                <button class="btn p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="profilerentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-lg text-muted pb-3px">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="19" cy="12" r="1"></circle>
                                        <circle cx="5" cy="12" r="1"></circle>
                                    </svg>
                                </button>
                            </div> -->
                        </div>
                        <!--Description  -->
                        <p><?php echo $profile->description;?></p>
                        <div class="mt-3">
                            <label class="tx-11 font-weight-bold mb-0 text-uppercase">Joined:</label>
                            <!--Joined  -->
                            <p class="text-muted">July 03, 2021</p>
                        </div>
                        <div class="mt-3">
                            <label class="tx-11 font-weight-bold mb-0 text-uppercase">Email:</label>
                            <!-- Mail -->
                            <p class="text-muted"><?php echo $profile->email;?></p>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Left wraped end -->
            <div class="col-md-8 col-xl-6 middle-wrapper">
                <div class="row">
                    <div class="col-md-12 grid-margin">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <img class="img-xs rounded-circle" style="height: 10vh;" src="<?php echo $profile->profile_img ?>" alt="">
                                <div class="ml-2">
                                    <!-- get posts -->
                                    <p>Dummy post</p>
                                    <p class="tx-11 text-muted">1 min ago</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
    </div>
   <?php endif?>                                     
<?php
    include 'include/footer.php';
    ?>