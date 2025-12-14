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

if (!$user || !password_verify($password, $user['password'])) {

    echo "LOGIN_ERROR";
    exit;
}


$code = random_int(100000, 999999);
$_SESSION['pending_user_id'] = (int)$user['id'];
$_SESSION['pending_login'] = $login;
$_SESSION['pending_code'] = $code;
$_SESSION['pending_time'] = time(); 


$subject = 'Код авторизации';
$message = "Ваш код авторизации: {$code}";
$headers = "Content-Type: text/plain; charset=utf-8\r\n";

// раскомментируй в боевой версии
 mail($login, $subject, $message, $headers);

// для отладки можно временно возвращать код, но для продакшна так не делай
// echo "NEED_CODE_{$code}";
echo "NEED_CODE";
?>
