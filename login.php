<?php
    require "db.php";

    if (isset($_POST['submit'])) {
        // Вытаскиваем из БД запись, у которой логин равняеться введенному
        $query = mysqli_query($link,"SELECT user_id, user_password FROM users WHERE user_login='".mysqli_real_escape_string($link,$_POST['login'])."' LIMIT 1");
        $data = mysqli_fetch_assoc($query);
        
        if($data['user_password'] === md5(md5($_POST['password']))) {
            $_SESSION['logged_user'] = $_POST['login'];
            header("Location: index.php"); exit();
        } else {
            print "You entered the wrong username / password";        
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
        <h2>Login page</h1>
      </div>
      
      <a href="index.php" class="index">Index</a>
      <a href="register.php" class="register">Register</a>
    </div> 
    <form method="POST">
        Login: <input name="login" type="text" required><br>
        Password: <input name="password" type="password" required><br>
        <input name="submit" type="submit" value="Login">
    </form>
</body>
</html>
