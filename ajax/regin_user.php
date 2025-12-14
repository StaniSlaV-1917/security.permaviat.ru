<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo -1; 
    exit;
}
$stmt->close();


$hash = password_hash($password, PASSWORD_DEFAULT); 

$stmtIns = $mysqli->prepare("INSERT INTO `users`(`login`, `password`, `roll`) VALUES (?, ?, 0)");
$stmtIns->bind_param("ss", $login, $hash);
$stmtIns->execute();

$id = $stmtIns->insert_id;
$stmtIns->close();

if ($id > 0) {
    $_SESSION['user'] = $id;
    echo $id;
} else {
    echo 0; 
}
?>
