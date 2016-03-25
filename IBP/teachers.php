<section>
  <h2>Uèitelé</h2>
  <?php
    include ("config.php");

    if(count($_POST))
    {
    $name = $_POST['name'];
    $surname = $_POST['surname'];

    if((empty($name)) || (empty($surname)))
    {
      echo('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;<strong>Chyba!</strong> Prosím, vyplòtì v¹echna pole.</div>');
    }
    else
    {
      echo('<div class="alert alert-success"><span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<strong>Ulo¾eno!</strong> Záznam byl úspì¹nì vlo¾en do databáze.</div>');
      // dal by tu mela byt kontrola veskerych integritnich omezeni, ale zatim predpokladame spravda data

      //vlozeni dat do databaze
      $result = mysql_query("INSERT INTO teachers VALUES ('NULL', '$name', '$surname')", $db);
      if (!$result)
      {
        die('Invalid query: ' . mysql_error());
      }
    }
    }
  ?>
  <h3>Pøidat uèitele</h3>
  <form class="form-horizontal" method="post">
  	<div class="form-group">
      <label class="control-label col-sm-2" for="name">Jméno:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" id="name" aria-describedby="basic-addon3" placeholder="Napi¹te jméno uèitele">
      </div>
    </div>
  	<div class="form-group">
      <label class="control-label col-sm-2" for="surname">Pøíjmení:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="surname" id="surname" aria-describedby="basic-addon3" placeholder="Napi¹te pøíjmení uèitele">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-offset-2 col-sm-10">
        <input type="submit" value="Vlo¾it uèitele do systému"/>
      </div>
    </div>
  </form>
  <hr>
  <h3>Výpis uèitelù</h3>
  <form class="form-horizontal" method="post">
  	<div class="form-group">
      <label class="control-label col-sm-2" for="name">Vyhledávání:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" id="name" aria-describedby="basic-addon3" placeholder="Napi¹te jméno nebo pøíjmení">
      </div>
    </div>
  </form>
  <table border="1" class="center_text col-sm-offset-2">
    <tr>
      <th class="table_headers">ID</th>
      <th class="table_headers">Jméno</th>
      <th class="table_headers">Pøíjmení</th>
      <th class="table_headers">Upravit</th>
      <th class="table_headers">Smazat</th>
    </tr>
    <tr>
      <?php
        $query = MySQL_Query("SELECT * FROM teachers ORDER BY ID DESC", $db);

        while($teachers = MySQL_Fetch_Row($query))
        {
          echo("<td>" . $teachers[0] . "</td>" . "<td>" . $teachers[1] . "</td>" . "<td>" . $teachers[2] . "</td>"); ?>            	
            <td><a href='admin.php?action=edit&item=teachers&id=<?php echo($teachers[0]); ?>'><span class="glyphicon glyphicon-pencil" title="Editovat uèitele"></span></a></td>
          	<td><a href='delete.php?item=teachers&id=<?php echo($teachers[0]); ?>'><span class="glyphicon glyphicon-remove" title="Smazat uèitele"></span></a></td>
          <?php echo("</tr>");
        }
      ?>
    </tr>
  </table>
</section>