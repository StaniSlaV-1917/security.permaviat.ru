<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $mysqli->prepare("SELECT `id`, `password` FROM `users` WHERE `login` = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$id = -1;

if ($user && password_verify($password, $user['password'])) {
    $id = (int)$user['id'];
}

if($id != -1) {
    $_SESSION['user'] = $id;

    $Ip = $_SERVER["REMOTE_ADDR"];
    $DateStart = date("Y-m-d H:i:s");

   
    $Sql ="INSERT INTO `session`(`IdUser`, `Ip`, `DateStart`, `DateNow`) 
           VALUES (?, ?, ?, ?)";
    $stmtSes = $mysqli->prepare($Sql);
    $stmtSes->bind_param("isss", $id, $Ip, $DateStart, $DateStart);
    $stmtSes->execute();
    $_SESSION["IdSession"] = $stmtSes->insert_id;
    $stmtSes->close();

    $SqlLog = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`)
               VALUES (?, ?, ?, '00:00:00', ?)";
    $stmtLog = $mysqli->prepare($SqlLog);
    $event = "Пользователь {$login} авторизовался.";
    $stmtLog->bind_param("siss", $Ip, $id, $DateStart, $event);
    $stmtLog->execute();
    $stmtLog->close();
}

echo md5(md5($id));
?>
