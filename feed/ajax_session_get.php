<?php
    session_start();
    $_SESSION['user_id'] = '1';
    $_SESSION['user_name'] = 'admin';
    $_SESSION['user_image'] = 'https://upload.wikimedia.org/wikipedia/vi/f/f5/Dua_Lipa_-_Future_Nostalgia_%28Official_Album_Cover%29.png';
    if (isset($_SESSION['user_id'])) {
        echo json_encode($_SESSION);
    }
?>