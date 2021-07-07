<?php
    include 'include/header.php';
    include 'classes/profile.php';
    //Dummy database for $_SESSION;
    $_SESSION['signed_in'] = true;
    $_SESSION['user_id'] = 1;
    $_SESSION['username'] = 'trang';
    $_SESSION['name'] = 'Ho Trang';
    $_SESSION['profile_image'] = 'imgs/0.png';
    //End dummy

    $profile = new Profile($conn, $_SESSION['user_id']);
    $profile->getFollowing();
    $profile->getFollowers();
    //var_dump($profile);

?>
    <div class="container" style="width: fit-content;">
        <div class="row">
            <div class="col-12 grid-margin">
                <div class="profile-header">
                    <div class="cover" style="
                                            position: relative;
                                            border-radius: 25rem 25rem 0 0;">
                        <figure>
                        <!-- Cover -->
                            <img src="<?php echo $profile->profile_cover; ?>" class="img-fluid" alt="profile cover" 
                                style="height: 80vh;">
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
                                <div class="d-none d-md-block">
                                    <button class="btn btn-primary btn-icon-text btn-edit-profile">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit btn-icon-prepend">
                                            <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                            <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                        </svg> Edit profile
                                    </button>
                                </div>
                            </div>

                        </figure>           
                    </div>
                <!-- Header links icon using svg -->
                <div class="header-links">
                    <ul class="links d-flex align-items-center mt-3 mt-md-0">
                        <li class="header-link-item d-flex align-items-center active">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-columns mr-1 icon-md">
                                <path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7m0-18H5a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h7m0-18v18"></path>
                            </svg>
                            <a class="pt-1px d-none d-md-block" href="#">Timeline</a>
                        </li>
                        <li class="header-link-item ml-3 pl-3 border-left d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-user mr-1 icon-md">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <a class="pt-1px d-none d-md-block" href="#">About</a>
                        </li>
                        <li class="header-link-item ml-3 pl-3 border-left d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users mr-1 icon-md">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <!-- Following count show -->
                            <a class="pt-1px d-none d-md-block" href="#">Following <span class="text-muted tx-12">
                                <?php echo count($profile->following);?>
                            </span></a>
                        </li>
                        <li class="header-link-item ml-3 pl-3 border-left d-flex align-items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-users mr-1 icon-md">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            <!-- Followers count show -->
                            <a class="pt-1px d-none d-md-block" href="#">Followers <span class="text-muted tx-12">
                                <?php echo count($profile->followers);?>
                            </span></a>
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
                            <div class="dropdown">
                                <button class="btn p-0" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-more-horizontal icon-lg text-muted pb-3px">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="19" cy="12" r="1"></circle>
                                        <circle cx="5" cy="12" r="1"></circle>
                                    </svg>
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-edit-2 icon-sm mr-2">
                                            <path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path>
                                        </svg> <span class="">Edit</span></a>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-git-branch icon-sm mr-2">
                                            <line x1="6" y1="3" x2="6" y2="15"></line>
                                            <circle cx="18" cy="6" r="3"></circle>
                                            <circle cx="6" cy="18" r="3"></circle>
                                            <path d="M18 9a9 9 0 0 1-9 9"></path>
                                        </svg> <span class="">Update</span></a>
                                    <a class="dropdown-item d-flex align-items-center" href="#">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye icon-sm mr-2">
                                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                            <circle cx="12" cy="12" r="3"></circle>
                                        </svg> <span class="">View all</span></a>
                                </div>
                            </div>
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

<?php
    include 'include/footer.php';
    ?>