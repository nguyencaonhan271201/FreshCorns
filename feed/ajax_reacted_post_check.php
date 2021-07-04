<?php
    session_start();
    require_once('db.php');
    if (isset($_GET['post_id']) && isset($_SESSION['user_id'])){
        $result = getRows($conn,'
        SELECT COUNT(*) as reacts
        FROM post_reactions
        WHERE post=? and user=?
        ',"ii",array($_GET['post_id'],$_SESSION['user_id']))[0]['reacts'];
        if ($result) echo 1;
    }
    echo 0;
?>