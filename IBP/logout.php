<?php
  session_start();
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
     <a href="index.php"><span class="glyphicon glyphicon-chevron-left"></span> Zp�t na hlavn� str�nku</a>
   </div>
   <div class="pull-right btn">
     <a href="login.php"><span class="glyphicon glyphicon-log-in"></span> P�ihl�sit se jako jin� u�ivatel</a>
    </div>
    <article>
      <section>
         <hr>
        <h2>Odhl�en� u�ivatele <?php echo($_SESSION['user']);?> prob�hlo �sp�n�.</h2> <!-- tohle nastylovat do chybove hlasky bootstrapu!-->
      </section>
    </article>
  </body>
</html>

<?php
  session_unset();
  session_destroy();
?>
