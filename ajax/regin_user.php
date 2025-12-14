<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

// === Проверка сложности пароля (сервер) ===
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

// === Проверка уникальности логина ===
$stmt = $mysqli->prepare("SELECT `id` FROM `users` WHERE `login` = ?");
$stmt->bind_param("s", $login);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo -1; // логин занят
    $stmt->close();
    exit;
}
$stmt->close();

// === Хэширование пароля и вставка ===
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
