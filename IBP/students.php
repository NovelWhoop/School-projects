<section>
  <h2>��ci</h2>
  <?php
    include ("config.php");

    if(count($_POST))
    {
      $name = $_POST['name'];
      $surname = $_POST['surname'];
      $login = $_POST['login'];
      $password = sha1($_POST['password']);
      $password_check = sha1($_POST['password_check']);

      if((empty($name)) || (empty($surname)) || (empty($login)) || (empty($password)) || (empty($password_check)))
      {
        echo('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;<strong>Chyba!</strong> Pros�m, vypl�t� v�echna pole.</div>');
      }
      else if($password != $password_check)
      {
        echo('<div class="alert alert-danger"><span class="glyphicon glyphicon-exclamation-sign"></span>&nbsp;<strong>Chyba!</strong> Hesla se neshoduj�!</div>'); 
      }
      else
      {
        echo('<div class="alert alert-success"><span class="glyphicon glyphicon-ok-circle"></span>&nbsp;<strong>Ulo�eno!</strong> Z�znam byl �sp�n� vlo�en do datab�ze.</div>');
      // dal by tu mela byt kontrola veskerych integritnich omezeni, ale zatim predpokladame spravda data
        //vlozeni dat do databaze
        $result = mysql_query("INSERT INTO students VALUES ('NULL', '$name', '$surname', '$login', '$password')", $db);
        if (!$result)
        {
          die('Invalid query: ' . mysql_error());
        }  
      }
    }
  ?>
  <h3>P�idat ��ka</h3>
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
        <input type="text" class="form-control" name="login" id="login" aria-describedby="basic-addon3" placeholder="Napi�te ��k�v login (rodn� ��slo bez lom�tka)">
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="password">Heslo:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="password" id="password" aria-describedby="basic-addon3" placeholder="Napi�te heslo">
      </div>
    </div>
    <div class="form-group">
      <div class="col-sm-10 col-sm-offset-2">
        <input type="password" class="form-control" name="password_check" id="password_check" aria-describedby="basic-addon3" placeholder="Kontrola hesla">
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
      <th class="table_headers">Upravit</th>
      <th class="table_headers">Smazat</th>
    </tr>
    <tr>
      <?php
        $query = MySQL_Query("SELECT * FROM students ORDER BY ID DESC", $db);

        while($students = MySQL_Fetch_Row($query))
        {
          echo("<td>" . $students[0] . "</td>" . "<td>" . $students[1] . "</td>" . "<td>" . $students[2] . "</td>" . "<td>" .  $students[3] . "</td>"); ?>            	
          <td><a href='admin.php?action=edit&item=students&id=<?php echo($students[0]); ?>'><span class="glyphicon glyphicon-pencil" title="Editovat studenta"></span></a></td>
          <td><a href='delete.php?item=students&id=<?php echo($students[0]); ?>'><span class="glyphicon glyphicon-remove" title="Smazat ��ka"></span></a></td>
          <?php echo("</tr>");
        }
      ?>
    </tr>
  </table>
</section>