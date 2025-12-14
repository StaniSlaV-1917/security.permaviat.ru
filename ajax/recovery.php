<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';

$query_user = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
$query_user->bind_param("s", $login);
$query_user->execute();
$res = $query_user->get_result();
$user_read = $res->fetch_row();
$query_user->close();

$id = -1;
if ($user_read) {
    $id = (int)$user_read[0];
}

function PasswordGeneration() {
    $chars="qazxswedcvfrtgbnhyujmkiolp1234567890QAZXSWEDCVFRTGBNHYUJMKIOLP!@#$%^&*";
    $max=10;
    $size=strlen($chars)-1;
    $password="";
    while($max--) {
        $password .= $chars[rand(0,$size)];
    }
    return $password;
}

if($id != -1) {
    $password = PasswordGeneration();

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmtUpd = $mysqli->prepare("UPDATE `users` SET `password` = ? WHERE `login` = ?");
    $stmtUpd->bind_param("ss", $hash, $login);
    $stmtUpd->execute();
    $stmtUpd->close();

}

echo $id;
?>