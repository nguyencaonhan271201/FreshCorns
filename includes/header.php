<?php 
    //Handle connection to database
    include "config.php";
    include "classes/Database.php";
    include "classes/Validate.php";
    include "classes/User.php";
    include "classes/ThisUser.php";

    $site_name = basename($_SERVER['PHP_SELF'], ".php");

    if (!$_SESSION['signed_in'] && $site_name != 'signin' && $site_name != 'index') {
        header('Location: index.php');
    }

    $db = Database::getInstance();
    $db->initializeDatabaseConnection();

    $this_user = ThisUser::getInstance();
    $this_user->setConn($db->conn);

    function getTimeString($datetime) {
        $dt = date_create_from_format("Y-m-d H:i:s", $datetime);
        $dt = $dt->getTimestamp();

        $now = time();

        $timeDifference = $now - $dt;
        //Time zone
        $timeDifference += 3600 * 7;
        $aWeek = 86400 * 7;

        if ($timeDifference < 3600)
        {
            $getValue = floor($timeDifference / 60) <= 0 ? 1 : floor($timeDifference / 60);
            $getUnit = $getValue == 1? "minute" : "minutes";
        }
        else if ($timeDifference < 86400)
        {
            $getValue = floor($timeDifference / 3600);
            $getUnit = $getValue == 1? "hour" : "hours";
        }
        else if ($timeDifference < $aWeek)
        {
            $getValue = floor($timeDifference / 86400);
            $getUnit = $getValue == 1? "day" : "days";
        }
        else
        {
            $getValue = floor($timeDifference / $aWeek);
            $getUnit = $getValue == 1? "week" : "weeks";
        }
        return "{$getValue} {$getUnit}";
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Fresh Corns | <?php echo ucfirst($site_name);?></title>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">

        <!-- Additional CSS -->
        <link rel="stylesheet" href="includes/css/style.css">
        <link rel="stylesheet" href="./includes/css/tooltip.css">

        <?php if($site_name == "chat"): ?>
        <link href="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.css" rel="stylesheet">
        <script src="https://unpkg.com/material-components-web@latest/dist/material-components-web.min.js"></script>
        <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
        <?php endif; ?>
    </head>
    <body>
        
    <?php if($site_name != 'index' && $site_name != 'signin'): ?>
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <a class="navbar-brand" href="feeds.php">
            <span class="logo"><?php echo file_get_contents("assets/images/logo.svg");?></span>
        </a>
        <button class="navbar-toggler d-lg-none" type="button" data-toggle="collapse" data-target="#collapsibleNavId" aria-controls="collapsibleNavId"
            aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="collapsibleNavId">
            <ul class="navbar-nav mr-auto mt-2 mt-lg-0 search-box">
                <li class="nav-item active">
                    <div class="ml-1 row">
                        <div class="col-12 text-right p-0 holder">
                            <input type="text" class="form-control" name="member-search" id="header-search" aria-describedby="helpId" placeholder="Search on Fresh Corns">
                            <div id="header-search-result">
                                <div id="header-search-user">
                                    
                                </div>
                                <div id="header-search-movies">

                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
            <?php if($site_name != "signin" && $site_name != "signup"): ?>
                <ul class="navbar-nav float-lg-right">
                    <?php if(!$_SESSION['signed_in']): ?>
                        <li class="nav-item active">
                            <a class="nav-link sign-in" href="signin.php"><i class="fa fa-sign-in-alt" aria-hidden="true"></i> Sign in</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link join-us" href="signup.php"><i class="fa fa-user-plus" aria-hidden="true"></i> Join us</a>
                        </li>
                    <?php elseif($_SESSION['signed_in']): ?>
                        <li class="nav-item active dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <img class="rounded-circle d-inline-block profile-img" src="<?php echo $_SESSION['profile_img'];?>"> 
                                <?php echo htmlspecialchars($_SESSION['username']); ?>
                            </a>
                            <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="navbarDropdown">
                                <a class="dropdown-item" href="chat.php"><i class="fa fa-comments" aria-hidden="true"></i> Chat</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="edit_profile.php"><i class="fa fa-user" aria-hidden="true"></i> Edit Profile</a>
                                <a class="dropdown-item" href="change_pass.php"><i class="fa fa-key" aria-hidden="true"></i> Change Password</a>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="logout.php"><i class="fa fa-sign-out-alt"></i> Logout</a>
                            </div>
                        </li>
                    <?php endif;?>
                </ul>
            <?php endif;?>
        </div>
    </nav>
    <?php endif; ?>