<?php
include 'config.php';
checkAdmin();

$user_id = (int)$_GET['user_id'];
$course_id = (int)$_GET['course_id'];

mysqli_query($conn, 
    "DELETE FROM assignments 
    WHERE user_id = $user_id AND course_id = $course_id"
);

$_SESSION['success'] = "Назначение отменено";
header("Location: users.php");
exit();
?>