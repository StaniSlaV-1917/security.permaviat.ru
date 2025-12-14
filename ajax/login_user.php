<?php
session_start();
include("../settings/connect_datebase.php");

$login = $_POST['login'] ?? '';
$password = $_POST['password'] ?? '';

// ищем пользователя по логину
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

// логин+пароль верны → генерируем код
$code = random_int(100000, 999999);
$_SESSION['pending_user_id'] = (int)$user['id'];
$_SESSION['pending_login'] = $login;
$_SESSION['pending_code'] = $code;
$_SESSION['pending_time'] = time();

// === КРАСИВОЕ ПИСЬМО С HTML ===
$subject = '🔐 Ваш код подтверждения - Авиатехникум';

$message = "
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 500px;
            margin: 0 auto;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 600;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
        }
        .greeting {
            font-size: 16px;
            color: #333;
            margin-bottom: 20px;
        }
        .code-block {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 8px;
            padding: 25px;
            text-align: center;
            margin: 25px 0;
        }
        .code-label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 10px;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: white;
            letter-spacing: 5px;
            font-family: 'Courier New', monospace;
        }
        .warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
            color: #856404;
        }
        .info {
            background: #e7f3ff;
            border-left: 4px solid #2196F3;
            padding: 15px;
            border-radius: 4px;
            margin: 20px 0;
            font-size: 14px;
            color: #0c5460;
        }
        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            border-top: 1px solid #e0e0e0;
            font-size: 12px;
            color: #666;
        }
        .footer-text {
            margin: 5px 0;
        }
        .button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            margin: 15px 0;
            font-weight: 600;
        }
        .divider {
            height: 1px;
            background: #e0e0e0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h1>🔐 Код Подтверждения</h1>
            <p>Пермский авиационный техникум им. А. Д. Швецова</p>
        </div>

        <div class='content'>
            <div class='greeting'>
                <p>Привет! 👋</p>
                <p>Мы получили запрос на вход в твой аккаунт. Используй код ниже для подтверждения личности.</p>
            </div>

            <div class='code-block'>
                <div class='code-label'>Твой код подтверждения</div>
                <div class='code'>{$code}</div>
            </div>


            <div class='info'>
                ℹ️ <strong>Совет:</strong> Никогда не делись этим кодом с другими людьми. Команда поддержки никогда не будет его просить.
            </div>

            <div class='divider'></div>

            <div style='text-align: center; margin: 20px 0;'>
                <p style='color: #666; font-size: 14px;'>Или перейди на сайт и введи код вручную:</p>
                <a href='http://localhost/login.php' class='button'>Вернуться на сайт</a>
            </div>
        </div>

        <div class='footer'>
            <div class='footer-text'><strong>© 2025 Пермский авиационный техникум</strong></div>
            <div class='footer-text'>им. А. Д. Швецова</div>
            <div class='footer-text' style='margin-top: 10px; opacity: 0.7;'>Письмо отправлено автоматически. Пожалуйста, не отвечай на это письмо.</div>
        </div>
    </div>
</body>
</html>
";

// === ЗАГОЛОВКИ С КРАСИВЫМ ИМЕНЕМ ОТПРАВИТЕЛЯ ===
$headers = "From: 🚀 SecureAuth <noreply@aviatechnikum.local>\r\n" .
           "Reply-To: noreply@aviatechnikum.local\r\n" .
           "Content-Type: text/html; charset=utf-8\r\n" .
           "X-Mailer: PHP/" . phpversion() . "\r\n";

mail($login, $subject, $message, $headers);

echo "NEED_CODE";
?>
