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
        <title> Регистрация </title>
        
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
                    <div class="name">Регистрация</div>
                
                    <div class = "sub-name">Логин (email):</div>
                    <input name="_login" type="text" onkeypress="return PressToEnter(event)"/>
                    <div class = "sub-name">Пароль:</div>
                    <input name="_password" type="password" onkeypress="return PressToEnter(event)"/>
                    <div class = "sub-name">Повторите пароль:</div>
                    <input name="_passwordCopy" type="password" onkeypress="return PressToEnter(event)"/>
                    
                    <a href="login.php">Вернуться</a>
                    <input type="button" class="button" value="Зайти" onclick="RegIn()" style="margin-top: 0px;"/>
                    <img src = "img/loading.gif" class="loading" style="margin-top: 0px;"/>
                </div>
                
                <div class="footer">
                    © КГАПОУ "Авиатехникум", 2020
                    <a href=#>Конфиденциальность</a>
                    <a href=#>Условия</a>
                </div>
            </div>
        </div>
        
        <script>
    var loading = document.getElementsByClassName("loading")[0];
    var button = document.getElementsByClassName("button")[0];

    function validatePassword(password) {
        if (password.length < 9) return "Пароль должен быть длиннее 8 символов.";
        if (!/[A-Za-z]/.test(password)) return "Пароль должен содержать латинские буквы.";
        if (!/[A-Z]/.test(password)) return "Пароль должен содержать хотя бы одну заглавную букву.";
        if (!/[0-9]/.test(password)) return "Пароль должен содержать хотя бы одну цифру.";
        if (!/[^A-Za-z0-9]/.test(password)) return "Пароль должен содержать хотя бы один спецсимвол.";
        return "";
    }
            
    function RegIn() {
        var _login = document.getElementsByName("_login")[0].value;
        var _password = document.getElementsByName("_password")[0].value;
        var _passwordCopy = document.getElementsByName("_passwordCopy")[0].value;
        
        if(_login != "") {
            if(_password != "") {
                if(_password == _passwordCopy) {

                    // Проверка сложности пароля (клиент)
                    var msg = validatePassword(_password);
                    if (msg !== "") {
                        alert(msg);
                        return;
                    }

                    loading.style.display = "block";
                    button.className = "button_diactive";
                    
                    var data = new FormData();
                    data.append("login", _login);
                    data.append("password", _password);
                    
                    $.ajax({
                        url         : 'ajax/regin_user.php',
                        type        : 'POST',
                        data        : data,
                        cache       : false,
                        dataType    : 'html',
                        processData : false,
                        contentType : false, 
                        success: function (_data) {
                            console.log("Регистрация, ответ: " + _data);

                            if (_data.indexOf("PWD_ERROR_") === 0) {
                                alert("Пароль не соответствует требованиям безопасности (сервер).");
                                loading.style.display = "none";
                                button.className = "button";
                                return;
                            }

                            if(_data == -1) {
                                alert("Пользователь с таким логином существует.");
                                loading.style.display = "none";
                                button.className = "button";
                            } else if (_data > 0) {
                                location.reload();
                                loading.style.display = "none";
                                button.className = "button";
                            } else {
                                alert("Ошибка регистрации. Код: " + _data);
                                loading.style.display = "none";
                                button.className = "button";
                            }
                        },
                        error: function(){
                            console.log('Системная ошибка!');
                            loading.style.display = "none";
                            button.className = "button";
                        }
                    });
                } else alert("Пароли не совпадают.");
            } else alert("Введите пароль.");
        } else alert("Введите логин.");
    }
            
    function PressToEnter(e) {
        if (e.keyCode == 13) {
            RegIn();
        }
    }
        </script>
    </body>
</html>
