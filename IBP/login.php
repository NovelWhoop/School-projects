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
  <title>Auto�kola V&V</title>
  <meta name="author" content="Roman Hal�k">
  <meta name="description" content="Autoko�kola V&V. Va�e auto�kola v Brn� na Lidick� ulici.">
  <meta name="keywords" content="auto�kola, auto, motorka, �idi�sk� pr�kaz, �idi��k, Vosinkov�, Brno, Lidick�, �kol�c� centrum">
  <link rel="stylesheet" type="text/css" href="bootstrap/css/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
  </head>
  <body>
    <div class="pull-left btn">
      <a href="index.php"><span class="glyphicon glyphicon-chevron-left"></span> Zp�t</a>
    </div>
    <article>
      <section>
        <h2>P�ihl�sit se do syst�mu</h2>
        <hr>
        <h3>Registrovan� u�ivatel�</h3>
        <?php
          $query = MySQL_Query("SELECT * FROM users", $db);

          while($users = MySQL_Fetch_Row($query))
            echo($users[1]."<br>");

          if($loginErr=1) echo('<hr><div class="alert alert-danger"><strong>Chyba!</strong> �patn� login nebo heslo.</div>');
        ?>
        <hr>
        <form class="form-horizontal" method="post">
          <div class="form-group">
            <label class="control-label col-sm-2" for="login">Login:</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" name="login" id="login" aria-describedby="basic-addon3" placeholder="Napi�te sv�j login">
            </div>
          </div>
          <div class="form-group">
            <label class="control-label col-sm-2" for="password">Heslo:</label>
            <div class="col-sm-10">
              <input type="password" class="form-control" name="password" id="password" aria-describedby="basic-addon3" placeholder="Napi�te heslo">
            </div>
          </div>
          <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
              <input type="submit" value="P�ihl�sit se"/>
            </div>
          </div>
        </form>
      </section>
    </article>
  </body>
</html>
