<?php
  include ("config.php");
  session_start();
  if(!isset($_SESSION['user']))
  {
    header("Location: login.php?Err=2", TRUE, 303);
    exit;
  }//pokud neni promenna naplena (uzivatel neprihlasen) presmeruju ho s odpovidajici chybovou hlaskou, at se laskave prihlasi
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
    <header class="navbar-fixed-top">
       <div class="pull-left nav nav-tabs">
        <nav>
          <a href="admin.php?action=schedule" <?php if(count($_GET) && ($_GET['action']) == 'schedule') echo('class="active"')?> ><h1>Rozvrh j�zd</h1></a>
          <a href="admin.php?action=edit_page" <?php if(count($_GET) && ($_GET['action']) == 'edit_page') echo('class="active"')?> ><h1>Editace webu</h1></a>
          <a href="admin.php?action=students" <?php if(count($_GET) && ($_GET['action']) == 'students') echo('class="active"')?> ><h1>��ci</h1></a>
          <a href="admin.php?action=teachers" <?php if(count($_GET) && ($_GET['action']) == 'teachers') echo('class="active"')?> ><h1>U�itel�</h1></a>
        </nav>
      </div>
      <div class="btn pull-right">
        <a href="logout.php">Odhl�sit se&nbsp;<span class="glyphicon glyphicon-log-out"></span></a>
      </div>
    </header>
    <article class="administration">
      <?php
        if(count($_GET) > 0)
        {
          switch ($_GET["action"]) 
          {
            case "schedule":
            case "edit":
            case "edit_page":
            case "students":
            case "teachers":
              include $_GET["action"].".php";
            break;
            default: include "schedule.php";  // defaultni text ve vsech pripadech
          }
        }
        else include ".php";  // defaultni text ve vsech pripadech
      ?>
        <!--Vzhled cele strany (zalozky) se musi menit v zavislosti na uzivateli! Zak opravdu nesmi mit pristup k tabulkam zaku, ucitelu, a editaci webu a jizd.. pouze staticky pohled na jizdy a moznost se prihlasit - zrejme uplne samostatna stranka, bude to nejjednodussi. -->
    </article>
  </body>
</html>
