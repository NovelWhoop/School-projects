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
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
  <script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
  </head>
  <body>
    <header class="navbar-fixed-top">
      <nav class="nav nav-tabs pull-left">
        <a href="index.php?action=about" <?php if(count($_GET) && ($_GET['action']) == 'about') echo('class="active"')?> ><h1>INFORMACE</h1></a>
        <a href="index.php?action=courses" <?php if(count($_GET) && ($_GET['action']) == 'courses') echo('class="active"')?> ><h1>KLASICK� KURZY</h1></a>
        <a href="index.php?action=training" <?php if(count($_GET) && ($_GET['action']) == 'training') echo('class="active"')?> ><h1>PROFESN� �KOLEN�</h1></a>
        <a href="#contacts"><h1>KONTAKTY</h1></a>
      </nav>
        <div class="pull-right btn">
          <a href="login.php"><span class="glyphicon glyphicon-log-in"></span> P�ihl�sit se</a>
        </div>
    </header> 
    <article>
      <div class="jumbotron">
        <h1>Auto�kola V&V</h1>
      </div>
      <?php
        if(count($_GET) > 0)
        {
          switch ($_GET["action"]) 
          {
            case "about":
            case "courses":
            case "training":
              include $_GET["action"].".php";
            break;
            default: include "about.php";
          }
        }
        else include "about.php";
      ?>
    </article>
    <footer class="row">
      <h2 id="contacts">KONTAKTY</h2>
      <div class="col-md-3">
        <h4>Kancel�� a u�ebna:</h4>
        <ul>
          <li>Lidick� 17</li>
          <li>Brno, 602 00</li>
          <li>50 m od hotelu SLOVAN</li>
          <li>tramvaj �.: 1, 3, 5, 6, 7, 11</li>
        </ul>
        <h4>Telefon:</h4>
        <ul>
          <li>+420 541 215 033</li>
          <li>+420 603 385 324</li>
        </ul>
      </div>
      <div class="col-md-4">
        <h4>E-mail:</h4>
        <ul>
          <li><a href="mailto:Eva.Vosinkova@seznam.cz">Eva.Vosinkova@seznam.cz</a></li>
        </ul>
        <h4>��edn� hodiny:</h4>
        <ul>
          <li>Po a� P� 8:00 - 21:00</li>
          <li>P�izp�sob�me se Va�im �asov�m mo�nostem!</li>
        </ul>
      </div>
      <div class="col-md-5">
        <h4>Mapa:</h4>
        <iframe src="//api.mapy.cz/frame?params=%7B%22x%22%3A16.608049981231176%2C%22y%22%3A49.200629570214176%2C%22base%22%3A%221%22%2C%22layers%22%3A%5B%5D%2C%22zoom%22%3A16%2C%22url%22%3A%22https%3A%2F%2Fmapy.cz%2Fs%2FuaR1%22%2C%22mark%22%3A%7B%22x%22%3A%2216.60717021667427%22%2C%22y%22%3A%2249.201596989506704%22%2C%22title%22%3A%22Lidick%C3%A1%20699%2F17%2C%20Brno%22%7D%2C%22overview%22%3Afalse%7D&amp;width=440&amp;height=300" width="440" height="300" style="border:none" frameBorder="0"></iframe>
      </div>
    </footer>
  </body>
</html>
