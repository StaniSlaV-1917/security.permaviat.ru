<?php
// предполагаем, что session_start() и connect_datebase.php уже вызваны до include этого файла

if (!isset($_SESSION['user']) || !isset($_SESSION['IdSession'])) {
    // нет полноценной авторизации
    session_destroy();
    header("Location: login.php");
    exit;
}

$IdUser = (int)$_SESSION['user'];
$IdSession = (int)$_SESSION['IdSession'];

// 1) проверяем, что такая сессия есть в БД и принадлежит этому пользователю
$SqlCheck = "SELECT * FROM `session` WHERE `Id` = ? AND `IdUser` = ?";
$stmt = $mysqli->prepare($SqlCheck);
$stmt->bind_param("ii", $IdSession, $IdUser);
$stmt->execute();
$Read = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$Read) {
    // запись в таблице session удалена (например, из-за логина в другом браузере)
    session_destroy();
    header("Location: login.php");
    exit;
}

// 2) если всё ок — обновляем DateNow
$DateNow = date("Y-m-d H:i:s");
$SqlUpdate = "UPDATE `session` SET `DateNow` = ? WHERE `Id` = ?";
$stmtUpd = $mysqli->prepare($SqlUpdate);
$stmtUpd->bind_param("si", $DateNow, $IdSession);
$stmtUpd->execute();
$stmtUpd->close();
?>
