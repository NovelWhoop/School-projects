<?php
  include ("config.php");

  if(count($_POST))
  {
    $name = $_POST['name'];
    $surname = $_POST['surname'];
    $login = $_POST['login'];
    $password = sha1($_POST['password']);

    if((empty($name)) || (empty($surname)) || (empty($login)) || (empty($password)))
    {
      exit;
    }

    // dal by tu mela byt kontrola veskerych integritnich omezeni, ale zatim predpokladame spravda data

    //vlozeni dat do databaze
    $result =  mysql_query("INSERT INTO students VALUES ('NULL', '$name', '$surname', '$login', '$password')", $db);
    if (!$result){
      die('Invalid query: ' . mysql_error());
    }
  }
?>

<script>
  function delete_student()
  {
  	var result = confirm('Opravdu chcete smazat ��ka?');
    if(result) alert("BAF!");
    //if(result)
    //{
      //<?php 
      //  $result =  mysql_query("DELETE FROM students WHERE ID = '4'", $db);
      //  if (!$result){
      //    die('Invalid query: ' . mysql_error());
      //  }
      //?>
    //}
  }
</script>

<section>
  <h2>��ci</h2>
  <h3>P�idat ��ka</h3>
  <hr>
  <form class="form-horizontal" method="post">
  	<div class="form-group">
      <label class="control-label col-sm-2" for="name">Jm�no:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" id="name" aria-describedby="basic-addon3" placeholder="Napi�te jm�no ��ka">
      </div>
    </div>
  	<div class="form-group">
      <label class="control-label col-sm-2" for="surname">P��jmen�:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="surname" id="surname" aria-describedby="basic-addon3" placeholder="Napi�te p��jmen� ��ka">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="login">Login:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="login" id="login" aria-describedby="basic-addon3" placeholder="Napi�te ��k�v login (rodn� ��slo bez '/')">
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
        <input type="submit" value="Vlo�it ��ka do syst�mu"/>
      </div>
    </div>
  </form>
  <hr>
  <h3>V�pis ��k�</h3>
  <form class="form-horizontal" method="post">
  	<div class="form-group">
      <label class="control-label col-sm-2" for="name">Vyhled�v�n�:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="name" id="name" aria-describedby="basic-addon3" placeholder="Napi�te jm�no, p��jmen� nebo login">
      </div>
    </div>
  </form>

  <table border="1" class="center_text col-sm-offset-2">
    <tr>
      <th class="table_headers">ID</th>
      <th class="table_headers">Jm�no</th>
      <th class="table_headers">P��jmen�</th>
      <th class="table_headers">Login</th>
      <th class="table_headers">Akce</th>
    </tr>
    <tr>
      <?php
        $query = MySQL_Query("SELECT * FROM students", $db);

        while($students = MySQL_Fetch_Row($query))
        {
          echo("<td>" . $students[0] . "</td>" . "<td>" . $students[1] . "</td>" . "<td>" . $students[2] . "</td>" . "<td>" .  $students[3] . "</td>"); ?>            	
          <td>
          	<a href='#' title="Editovat ��ka"><span class='glyphicon glyphicon-pencil'></span></a>&nbsp;
          	<span class='glyphicon glyphicon-remove' onClick="delete_student()" title="Smazat ��ka"></span>
            <!-- echo '<script type="text/javascript"> doDecryption(' . $myValue . '); </script>'; -->
          </td>
          <?php echo("</tr>");
        }
        ?>
    </tr>
  </table>
  <hr>
  <ul>
  	<li>Asi v zakladu tabulka vypisu studentu</li>
  	<li>zrejme nejake filtry</li>
  	<li>moznost smazani celeho radku (studenta)</li>
  	<li>moznost editace</li>
  	<li>moznost hledani s presmerovanim na editaci?</li>
  	<li>moznost pridani noveho zaka, asi taky presmerovani na stranku s formularem</li>
  </ul>
</section>