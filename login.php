<?php
    session_start();
    include("./settings/connect_datebase.php");
    
    if (isset($_SESSION['user'])) {
        if($_SESSION['user'] != -1) {
            
            $user_query = $mysqli->query("SELECT * FROM `users` WHERE `id` = ".$_SESSION['user']);
            while($user_read = $user_query->fetch_row()) {
                if($user_read[3] == 0) header("Location: user.php");
                else if($user_read[3] == 1) header("Location: admin.php");
            }
        }
    }
?>
<html>
    <head> 
        <meta charset="utf-8">
        <title> Авторизация </title>
        
        <script src="https://code.jquery.com/jquery-1.8.3.js"></script>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <div class="top-menu">
            <a href=#><img src = "img/logo1.png"/></a>
            <div class="name">
                <a href="index.php">
                    <div class="subname">БЗОПАСНОСТЬ  ВЕБ-ПРИЛОЖЕНИЙ</div>
                    Пермский авиационный техникум им. А. Д. Швецова
                </a>
            </div>
        </div>
        <div class="space"> </div>
        <div class="main">
            <div class="content">
                <div class = "login">
                    <div class="name">Авторизация</div>
                
                    <div class = "sub-name">Логин:</div>
                    <input name="_login" type="text" placeholder="" onkeypress="return PressToEnter(event)"/>
                    <div class = "sub-name">Пароль:</div>
                    <input name="_password" type="password" placeholder="" onkeypress="return PressToEnter(event)"/>
                    
                    <a href="regin.php">Регистрация</a>
                    <br><a href="recovery.php">Забыли пароль?</a>
                    <input type="button" class="button" value="Войти" onclick="LogIn()"/>
                    <img src = "img/loading.gif" class="loading"/>
                    
                    <div id="codeBlock" style="display:none; margin-top:15px;">
                        <div class="sub-name">Код из почты:</div>
                        <input name="_code" type="text" placeholder="6-значный код"/>
                        <input type="button" class="button" value="Подтвердить код" onclick="VerifyCode()" style="margin-top: 10px;"/>
                    </div>
                </div>
                
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                    <a href=#>Конфиденциальность</a>
                    <a href=#>Условия</a>
                </div>
            </div>
        </div>
        
        <script>
            function LogIn() {
                var loading = document.getElementsByClassName("loading")[0];
                var button = document.getElementsByClassName("button")[0];
                
                var _login = document.getElementsByName("_login")[0].value;
                var _password = document.getElementsByName("_password")[0].value;

                if (_login === "" || _password === "") {
                    alert("Введите логин и пароль.");
                    return;
                }

                loading.style.display = "block";
                button.className = "button_diactive";
                
                var data = new FormData();
                data.append("login", _login);
                data.append("password", _password);
                
                $.ajax({
                    url         : 'ajax/login_user.php',
                    type        : 'POST',
                    data        : data,
                    cache       : false,
                    dataType    : 'html',
                    processData : false,
                    contentType : false, 
					success: function (_data) {
    console.log("Авторизация, ответ: " + _data);

    if (_data === "LOGIN_ERROR") {
        loading.style.display = "none";
        button.className = "button";
        alert("Логин или пароль не верный.");
        return;
    }

    // принимаем и "NEED_CODE", и "NEED_CODE_430514"
    if (_data.indexOf("NEED_CODE") === 0) {
        document.getElementById("codeBlock").style.display = "block";
        loading.style.display = "none";
        button.className = "button";

        // можно для отладки показать код, если он есть
        // var parts = _data.split("_");
        // if (parts.length === 3) alert("Код (отладка): " + parts[2]);

        return;
    }

    if(_data == "") {
        loading.style.display = "none";
        button.className = "button";
        alert("Логин или пароль не верный.");
    } else {
        console.log("СТАРАЯ ВЕТКА, ответ: " + _data);
        loading.style.display = "none";
        button.className = "button";
        // location.reload(); // пока отключено
    }
},
                    error: function(){
                        console.log('Системная ошибка!');
                        loading.style.display = "none";
                        button.className = "button";
                    }
                });
            }
            
            function PressToEnter(e) {
                if (e.keyCode == 13) {
                    var _login = document.getElementsByName("_login")[0].value;
                    var _password = document.getElementsByName("_password")[0].value;
                    
                    if(_password != "" && _login != "") {
                        LogIn();
                    }
                }
            }

            function VerifyCode() {
                var _code = document.getElementsByName("_code")[0].value;
                if (_code === "") {
                    alert("Введите код из письма.");
                    return;
                }

                var data = new FormData();
                data.append("code", _code);

                $.ajax({
                    url         : 'ajax/verify_code.php',
                    type        : 'POST',
                    data        : data,
                    cache       : false,
                    dataType    : 'html',
                    processData : false,
                    contentType : false,
                    success: function (_data) {
                        console.log("Проверка кода: " + _data);
						console.log("Авторизация, ответ: " + _data);

                        if (_data === "CODE_ERROR") {
                            alert("Код неверный.");
                            return;
                        }
                        if (_data === "CODE_EXPIRED") {
                            alert("Код просрочен. Войдите заново.");
                            location.reload();
                            return;
                        }
                        if (_data === "NO_PENDING") {
                            alert("Нет ожидающей авторизации. Войдите заново.");
                            location.reload();
                            return;
                        }
                        if (_data === "OK") {
                            location.reload();
                        } else {
                            alert("Неизвестный ответ сервера: " + _data);
                        }
                    },
                    error: function(){
                        console.log('Системная ошибка при проверке кода!');
                    }
                });
            }
        </script>
    </body>
</html>
