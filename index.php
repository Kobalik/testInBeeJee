<?php
  require "db.php";

  // Переменная хранит число сообщений выводимых на станице
  $num = 3;

  // Извлекаем из URL текущую страницу
  $page = $_GET['page'];

  // Определяем общее число сообщений в базе данных
  $query = mysqli_query($link,"SELECT COUNT(*) FROM posts");
  $posts = mysqli_fetch_assoc($query);
  $count=$posts["COUNT(*)"];

  // Находим общее число страниц
  $total = intval(($count - 1) / $num) + 1;
  // Определяем начало сообщений для текущей страницы
  $page = intval($page);
  // Если значение $page меньше единицы или отрицательно
  // переходим на первую страницу
  // А если слишком большое, то переходим на последнюю
  if(empty($page) or $page < 0) {
    $page = 1;
  } if($page > $total) {
      $page = $total;
  }
  // Вычисляем начиная к какого номера
  // следует выводить сообщения
  $start = $page * $num - $num;
  
  if (isset($_POST['sort'])){
    $_SESSION['what_sort'] = $_POST['what'];
    $_SESSION['how_sort'] = $_POST['how'];
  } else {
    $how_sort = 'asc';
    $what = 'name';
  }

  $what = $_SESSION['what_sort'] ? $_SESSION['what_sort'] : 'name';
  $how_sort = $_SESSION['how_sort'] ? $_SESSION['how_sort'] : 'asc';

  if ($how_sort == 'decrease') {
    $how_sort = 'desc';
  } else {
    $how_sort = 'asc';
  }


  // Выбираем $num сообщений начиная с номера $start
  $order = "ORDER BY ".$what." ".$how_sort;
  $query = mysqli_query($link,"SELECT * FROM posts $order LIMIT $start, $num") or die("Error: " . mysqli_error($link));

  // В цикле переносим результаты запроса в массив $postrow 
  $t = 0;
  while ($t < $count){
    $data = mysqli_fetch_assoc($query); 
    $postrow[] = $data;
    $t++;
  }
  

  // Красивый вывод готовности
  function checkComplete($status) {
    if ($status) {
      if ($status == '2') {
        return "complete, changed by admin";
      }
      return "complete";
    }
    return "in process";
  }

  if (isset($_POST['change_text'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $text = $_POST['text'];
    // создание строки запроса
    $query ="UPDATE posts SET text='$text', is_complete=2 WHERE name='$name' and email='$email' and id=$id";
     
    // выполняем запрос
    $result = mysqli_query($link, $query) or die("Error: " . mysqli_error($link)); 
  }

  if (isset($_POST['change_status'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    // создание строки запроса
    $query ="UPDATE posts SET  is_complete=1 WHERE name='$name' and email='$email' and id=$id";
     
    // выполняем запрос
    $result = mysqli_query($link, $query) or die("Error: " . mysqli_error($link)); 
  }
?>
<!doctype html>
<html lang="ru">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="css/main.css">
    <title>BeeJee &lt;3</title>
  </head>
  <body>
    <div class="navbar">
      <div class="logo">
        <h2>Test task in BeeJee!</h1>
      </div>
      
      <?php if ( isset($_SESSION["logged_user"]) ) : ?>
        <h1>Hello, <?php echo $_SESSION["logged_user"]; ?>!</h1>
        <hr></ht>
        <a href="addpage.php" class="addTask">Add Task</a>
        <a href="logout.php" class="logout">Logout</a>
        <!-- Если не авторизован - выводим ссылки на вход и регистрацию -->
        <?php else : ?>
          <a href="addpage.php" class="addTask">Add Task</a>
          <a href="login.php" class="index">Login</a>
          <a href="register.php" class="register">Register</a>
        <?php endif; ?>
    </div> 

    <div class='sort'>
      <h3>Sort by: <form method="POST">
      <select name='what'>
        <option>name</option>
        <option>email</option>
        <option>is_complete</option>
      </select>
      <select name='how'>
        <option>decrease</option>
        <option>increase</option>
      </select>
      <input type="submit" name="sort" value="Sort">
      </form></h3>
    </div>

    <?php
      echo "<div class='container'>";
      if (($count - $num*($page-1)) <= 3){
        $len = $count - $num*($page-1);
      } else {
        $len = 3;
      }
      if ($_SESSION["logged_user"] == "admin") {
        for($i = 0; $i < $len; $i++) {
          echo "<div class='content'>
                  <form method='POST'>
                    <h3>Name: <input name='name' type='text' value='".$postrow[$i]['name']."' readonly> <br />
                    Email: <input name='email' type='text' value='".$postrow[$i]['email']."' readonly> <br /></h3>
                    <b>Task:</b> <textarea rows='10' name='text'>".$postrow[$i]['text']."</textarea><br />
                    <b>Status:</b> ".checkComplete($postrow[$i]['is_complete'])."<br /> 
                    <input name='id' value='".$postrow[$i]['id']."' type='hidden'><br />
                    <input type='submit' name='change_text' value='Change text'>
                    <input type='submit' name='change_status' value='Change status' />
                  </form>
                </div>";
        }
        echo "</div>";
      } else {
        for($i = 0; $i < $len; $i++) {
          echo "<div class='content'>
                  <h3>Name: ".$postrow[$i]['name']." <br />
                  Email: ".$postrow[$i]['email']." <br /></h3>
                  <b>Task:</b> ".$postrow[$i]['text']." <br />
                  <b>Status:</b> ".checkComplete($postrow[$i]['is_complete'])."
                </div>";
        }
        echo "</div>";
      }
    ?>
<div>
    <?php
    
      // Проверяем нужны ли стрелки назад
      if ($page != 1) {
        $pervpage = '<a href= ./index.php?page=1><<</a>
            <a href= ./index.php?page='. ($page - 1) .'><</a> ';
      }
      // Проверяем нужны ли стрелки вперед
      if ($page != $total) {
        $nextpage = ' <a href= ./index.php?page='. ($page + 1) .'>></a>
             <a href= ./index.php?page=' .$total. '>>></a>';
      }

      // Находим две ближайшие станицы с обоих краев, если они есть
      if($page - 2 > 0) {
        $page2left = ' <a href= ./index.php?page='. ($page - 2) .'>'. ($page - 2) .'</a> | ';
      }
      if($page - 1 > 0) {
        $page1left = '<a href= ./index.php?page='. ($page - 1) .'>'. ($page - 1) .'</a> | ';
      }
      if($page + 2 <= $total) {
        $page2right = ' | <a href= ./index.php?page='. ($page + 2) .'>'. ($page + 2) .'</a>';
      }
      if($page + 1 <= $total) {
        $page1right = ' | <a href= ./index.php?page='. ($page + 1) .'>'. ($page + 1) .'</a>';
      }

      // Вывод меню
      echo $pervpage.$page2left.$page1left.'<b>'.$page.'</b>'.$page1right.$page2right.$nextpage;

    ?>
</div>
  </body>
</html>