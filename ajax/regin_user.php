<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'];
$password = $_POST['password'];

function validatePasswordServer($pwd, &$errorCode) {
    if (strlen($pwd) < 9) { $errorCode = "len"; return false; }
    if (!preg_match('/[A-Za-z]/', $pwd)) { $errorCode = "letter"; return false; }
    if (!preg_match('/[A-Z]/', $pwd)) { $errorCode = "upper"; return false; }
    if (!preg_match('/[0-9]/', $pwd)) { $errorCode = "digit"; return false; }
    if (!preg_match('/[^A-Za-z0-9]/', $pwd)) { $errorCode = "symbol"; return false; }
    return true;
}

$errorCode = "";
if (!validatePasswordServer($password, $errorCode)) {

    echo "PWD_ERROR_" . $errorCode;
    exit;
}

$query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."'");
$id = -1;

if($user_read = $query_user->fetch_row()) {
    echo $id; 
} else {
    $mysqli->query("INSERT INTO `users`(`login`, `password`, `roll`) VALUES ('".$login."', '".$password."', 0)");

    $query_user = $mysqli->query("SELECT * FROM `users` WHERE `login`='".$login."' AND `password`= '".$password."';");
    $user_new = $query_user->fetch_row();
    $id = $user_new[0];
            
    if($id != -1) $_SESSION['user'] = $id;
    echo $id;
}
?>
