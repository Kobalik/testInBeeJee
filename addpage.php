<?php
    require "db.php";

    if (isset($_POST['submit'])) {
        $err = array();

        // проверяем email
        $email = $_POST['email'];
        $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        if (! $email) {
            $err[] = 'Please enter a valid email address';
        }

        // экранирования символов для mysql
        $name = htmlentities(mysqli_real_escape_string($link, $_POST['name']));
        if (! $name) {
            $err[] = 'Please enter a name';
        }
        $text = htmlentities(mysqli_real_escape_string($link, $_POST['text']));
        if (! $text) {
            $err[] = 'Please enter a task';
        }
        // формируем запрос на добавление данных
        $query="INSERT INTO `posts`(`name`, `email`, `text`, `is_complete`) VALUES ('".$name."','".$email."','".$text."', 0)";

        if(count($err) == 0)
        {
            if (mysqli_query($link, $query)) {
                print "New record created successfully";
            } 
            
        } else {
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
        <h2>Add task page</h1>
      </div>
      
        <?php if ( isset($_SESSION["logged_user"]) ) : ?>
            <h1>Hello, <?php echo $_SESSION["logged_user"]; ?>!</h1>
            <hr></ht>
            <a href="index.php" class="addTask">Index</a>
            <a href="logout.php" class="logout">Logout</a>
        <!-- Если не авторизован - выводим ссылки на вход и регистрацию -->
        <?php else : ?>
          <a href="index.php" class="addTask">Index</a>
          <a href="login.php" class="index">Login</a>
          <a href="register.php" class="register">Register</a>
        <?php endif; ?>
    </div> 
    <form method="POST">
        Name: <input name="name" type="text" required><br>
        Email: <input name="email" type="text" required><br>
        Text task: <textarea rows='10' name='text' required></textarea><br>
        <input name="submit" type="submit" value="Add">
    </form>
</body>
</html>
