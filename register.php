<?php
    require "db.php";

    if(isset($_POST['submit']))
    {
        $err = array();

        if(strlen($_POST['login']) < 3 or strlen($_POST['login']) > 30)
        {
            $err[] = "Login must be at least 3 characters and no more than 30";
        }

        // проверяем, не сущестует ли пользователя с таким именем
        $query = mysqli_query($link, "SELECT user_id FROM users WHERE user_login='".mysqli_real_escape_string($link, $_POST['login'])."'");
        if(mysqli_num_rows($query) > 0)
        {
            $err[] = "This username is already in use.";
        }

        // Если нет ошибок, то добавляем в БД нового пользователя
        if(count($err) == 0)
        {

            $login = $_POST['login'];

            // Убераем лишние пробелы и делаем двойное хеширование
            $password = md5(md5(trim($_POST['password'])));

            mysqli_query($link,"INSERT INTO users SET user_login='".$login."', user_password='".$password."'");
            header("Location: login.php"); exit();
        }
        else
        {
            print "<b>The following errors occurred during registration:</b><br>";
            foreach($err AS $error)
            {
                print $error."<br>";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css">
    <title>BeeJee &lt;3</title>
</head>
<body>
    <div class="navbar">
      <div class="logo">
        <h2>Register page</h1>
      </div>
      
      <a href="login.php" class="login">Login</a>
      <a href="index.php" class="index">Index</a>
    </div> 
    <form method="POST">
        Login: <input name="login" type="text" required><br>
        Password: <input name="password" type="password" required><br>
        <input name="submit" type="submit" value="Register">
    </form>
</body>
</html>
