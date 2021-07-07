<?php
    require_once('../includes/php/db.php');
    if (isset($_GET['post_id'])){
        echo getRows($conn,'
        SELECT COUNT(*) as reacts
        FROM post_reactions
        WHERE post=?
        ',"i",array($_GET['post_id']))[0]['reacts'];
    }
?>