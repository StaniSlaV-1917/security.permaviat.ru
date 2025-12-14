<?php
session_start();
include("../settings/connect_datebase.php");

$inputCode = $_POST['code'] ?? '';

if (!isset($_SESSION['pending_user_id'], $_SESSION['pending_code'], $_SESSION['pending_time'], $_SESSION['pending_login'])) {
    echo "NO_PENDING";
    exit;
}

$maxLifetime = 10 * 60;
if (time() - $_SESSION['pending_time'] > $maxLifetime) {

    unset($_SESSION['pending_user_id'], $_SESSION['pending_code'], $_SESSION['pending_time'], $_SESSION['pending_login']);
    echo "CODE_EXPIRED";
    exit;
}

if ($inputCode != $_SESSION['pending_code']) {
    echo "CODE_ERROR";
    exit;
}

// код верный → создаём сессию пользователя
$id = (int)$_SESSION['pending_user_id'];
$login = $_SESSION['pending_login'];

$_SESSION['user'] = $id;

$Ip = $_SERVER["REMOTE_ADDR"];
$DateStart = date("Y-m-d H:i:s");

// запись в таблицу session
$Sql ="INSERT INTO `session`(`IdUser`, `Ip`, `DateStart`, `DateNow`) 
       VALUES (?, ?, ?, ?)";
$stmtSes = $mysqli->prepare($Sql);
$stmtSes->bind_param("isss", $id, $Ip, $DateStart, $DateStart);
$stmtSes->execute();
$_SESSION["IdSession"] = $stmtSes->insert_id;
$stmtSes->close();

// лог в logs
$SqlLog = "INSERT INTO `logs`(`Ip`, `IdUser`, `Date`, `TimeOnline`, `Event`)
           VALUES (?, ?, ?, '00:00:00', ?)";
$stmtLog = $mysqli->prepare($SqlLog);
$event = "Пользователь {$login} авторизовался (2FA).";
$stmtLog->bind_param("siss", $Ip, $id, $DateStart, $event);
$stmtLog->execute();
$stmtLog->close();

// чистим pending-данные
unset($_SESSION['pending_user_id'], $_SESSION['pending_code'], $_SESSION['pending_time'], $_SESSION['pending_login']);

echo "OK";
?>
