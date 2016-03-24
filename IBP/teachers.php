<?php
  include ("config.php");

  if(count($_POST))
  {
    $name = $_POST['name'];
    $surname = $_POST['surname'];

    if((empty($name)) || (empty($surname)))
    {
      exit;
    }

    // dal by tu mela byt kontrola veskerych integritnich omezeni, ale zatim predpokladame spravda data

    //vlozeni dat do databaze
    $result =  mysql_query("INSERT INTO teachers VALUES ('NULL', '$name', '$surname')", $db);
    if (!$result){
      die('Invalid query: ' . mysql_error());
    }
  }
?>

<section>
  <h2>Uèitelé</h2>
  <h3>Pøidat uèitele</h3>
  <hr>
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
      <th class="table_headers">Akce</th>
    </tr>
    <tr>
      <?php
        $query = MySQL_Query("SELECT * FROM teachers", $db);

        while($teachers = MySQL_Fetch_Row($query))
        {
          echo("<td>" . $teachers[0] . "</td>" . "<td>" . $teachers[1] . "</td>" . "<td>" . $teachers[2] . "</td>"); ?>            	
          <td>
          	<a href='#' title="Editovat uèitele"><span class='glyphicon glyphicon-pencil'></span></a>&nbsp;
          	<a href='#' onClick="confirm('Opravdu chcete smazat uèitele?');" title="Smazat uèitele"><span class='glyphicon glyphicon-remove'></span></a>
          </td>
          <?php echo("</tr>");
        }
        ?>
    </tr>
  </table>
  <hr>
  <ul>
  	<li>Asi v zakladu tabulka vypisu ucitelu</li>
  	<li>zrejme nejake filtry</li>
  	<li>moznost smazani celeho radku (ucitele)</li>
  	<li>moznost editace</li>
  	<li>moznost hledani s presmerovanim na editaci?</li>
  	<li>moznost pridani noveho zaka, asi taky presmerovani na stranku s formularem</li>
  </ul>
</section>