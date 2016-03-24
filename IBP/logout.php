<?php
  session_start();
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
     <a href="index.php"><span class="glyphicon glyphicon-chevron-left"></span> Zpět na hlavní stránku</a>
   </div>
   <div class="pull-right btn">
     <a href="login.php"><span class="glyphicon glyphicon-log-in"></span> Přihlásit se jako jiný uživatel</a>
    </div>
    <article>
      <section>
         <hr>
        <h2>Odhlášení uživatele <?php echo($_SESSION['user']);?> proběhlo úspěšně.</h2> <!-- tohle nastylovat do chybove hlasky bootstrapu!-->
      </section>
    </article>
  </body>
</html>

<?php
  session_unset();
  session_destroy();
?>
