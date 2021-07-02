<?php
//Database connection
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "";
$DB_NAME = "cs204_final_project";
$DB_PORT = 3306;

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
mysqli_set_charset($conn, 'utf8mb4');
