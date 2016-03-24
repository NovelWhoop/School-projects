<?php
  include ("config.php");

  if(count($_POST))
  {
    $login = $_POST['login'];
    $password = $_POST['password'];

    if((empty($login)) || (empty($password)))
    {
      header("Location: ?loginErr=1", TRUE, 303);
      exit;
    }

    $query1 = MySQL_Query("SELECT * FROM users where login='$login'", $db); //podle loginu vyhledam odpovidajici zaznam
    $dbpass = MySQL_Fetch_Row($query1);

    if($password == $dbpass[2]) // a porovnam napsane heslo s tim, ktere nalezi danemu zaznamu
    {
      session_start();
      $_SESSION['user'] = $login;
      session_regenerate_id();

      header("Location: admin.php", TRUE, 303);
      exit;
    }
    else
    {
      header("Location: ?loginErr=1", TRUE, 303);
      exit;
    }
  }
?>

<!DOCTYPE html>
<html lang="cs">
  <head>
  <meta charset="iso-8859-2">
  <title>Autoškola V&V</title>
  <meta name="author" content="Roman Halík">
  <meta name="description" content="Autokoškola V&V. Vaše autoškola v Brně na Lidické ulici.">
  <meta name="keywords" content="autoškola, auto, motorka, řidičský průkaz, řidičák, Vosinková, Brno, Lidická, školící centrum">
  <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
  </head>
  <body>
    <div class="pull-left btn">
      <a href="index.php"><span class="glyphicon glyphicon-chevron-left"></span> Zpět</a>
    </div>
    <article>
      <section>
        <h2>Přihlásit se do systému</h2>
        <hr>
        <h3>Registrovaní uživatelé</h3>
        <?php
          $query = MySQL_Query("SELECT * FROM users", $db);

          while($users = MySQL_Fetch_Row($query))
            echo($users[1]."<br>");

          if($loginErr=1) echo('<hr><div class="alert alert-danger"><strong>Chyba!</strong> Špatný login nebo heslo.</div>');
        ?>
        <hr>
        <form class="form-horizontal" method="post">
          <div class="form-group">
            <label class="control-label col-sm-2" for="login">Login:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="login" id="login" aria-describedby="basic-addon3" placeholder="Napište svůj login">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="password">Heslo:</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" name="password" id="password" aria-describedby="basic-addon3" placeholder="Napište heslo">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="submit" value="Přihlásit se"/>
            </div>
          </div>
        </form>
      </section>
    </article>
  </body>
</html>
