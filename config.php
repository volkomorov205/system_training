<?php
session_start();
$conn = mysqli_connect("localhost", "root", "", "training_platform");

if(!$conn) die("Ошибка подключения: " . mysqli_connect_error());

function checkAuth() {
    if(!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

function checkAdmin() {
    checkAuth();
    if($_SESSION['role'] !== 'admin') {
        header("Location: index.php");
        exit();
    }
}

function sanitize($data) {
    global $conn;
    return mysqli_real_escape_string($conn, htmlspecialchars(strip_tags(trim($data))));
}
?>