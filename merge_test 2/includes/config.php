<?php
header('Content-Type: text/html; charset=utf-8');

session_start();

spl_autoload_register(function ($class) {
    $classlocation = 'classes/' . $class . '.php';
    if (file_exists($classlocation)) {
        require_once $classlocation;
    }
});

if(!isset($_SESSION['signed_in'])) {
    $_SESSION['signed_in'] = false;
}