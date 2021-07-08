<?php
//Database connection
$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASSWORD = "";
$DB_NAME = "cs204_final_project";
$DB_PORT = 3306;

$conn = new mysqli($DB_HOST, $DB_USER, $DB_PASSWORD, $DB_NAME);
mysqli_set_charset($conn, 'utf8mb4');

function getRows($conn,$sql,$type,array $params) {
    $stmt = $conn->prepare($sql);
    if ($type!='') $stmt->bind_param($type,...$params);
    $stmt->execute();
    $results = $stmt->get_result();
    return $results->fetch_all(MYSQLI_ASSOC);
}

function setRow($conn,$sql,$type,array $params){
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($type,...$params);
    $stmt->execute();
    if($stmt->affected_rows != 1) return false;
    else return true;
}