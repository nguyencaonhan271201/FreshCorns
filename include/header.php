<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fresh Corns</title>
    <link rel="icon" href="css/icons/logo-white.svg">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css?v=<?=time();?>">
</head>
<body>
    <nav class="d-flex justify-content-between fixed-top">
        <div class="nav_brand d-flex">
            <span class="logo"><?php echo file_get_contents("css/icons/logo.svg");?></span>
            <h1>Fresh Corns</h1>
        </div>

        <div class="nav_list d-flex align-items-center">
            <div>
                <ul>
                    <li>Login</li>
                    <li><i class="bi bi-search"></i>Search</li>
                </ul>
            </div>
        </div>
    </nav>