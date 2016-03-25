<?php
  include ("config.php");

  if($_GET['item'] == 'teachers')
  {
    $result = mysql_query("SELECT * FROM $_GET[item] WHERE ID = $_GET[id]", $db);
    if (!$result)
    {
      die('Invalid query: ' . mysql_error());
    }
    else //dotaz je v poradku, vybereme odpovidajici radek tabulky
    {
      $items = MySQL_Fetch_Row($result);
    }

    if(count($_POST)) // jestlize byla zmenea a odeslana nova data, updatujeme tabulku
    {
      $name = $_POST['name'];
      $surname = $_POST['surname'];

      if((empty($name)) || (empty($surname)))
      {
        exit;
      }

      // dal by tu mela byt kontrola veskerych integritnich omezeni, ale zatim predpokladame spravda data

      $result = mysql_query("UPDATE $_GET[item] SET name='$name', surname='$surname' WHERE ID = $_GET[id]", $db);
    
      if (!$result) // pokud nenasala chyba pri updatu, muzeme presmerovat zpatky na vypis ucitelu
      {
        die('Invalid query: ' . mysql_error());
      }
      else
      {
        header('Location: admin.php?action=' . $_GET[item]);
      }
    }
  }
  else // druhy pripad - studenti (vice moznosti neni) - rozlisujeme kvuli promennemu poctu polozek
  {
    $result = mysql_query("SELECT * FROM $_GET[item] WHERE ID = $_GET[id]", $db);
    if (!$result)
    {
      die('Invalid query: ' . mysql_error());
    }
    else //dotaz je v poradku, vybereme odpovidajici radek tabulky
    {
      $items = MySQL_Fetch_Row($result);
    }

    if(count($_POST)) // jestlize byla zmenea a odeslana nova data, updatujeme tabulku
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

      $result = mysql_query("UPDATE $_GET[item] SET name='$name', surname='$surname', login='$login', password='$password' WHERE ID = $_GET[id]", $db);
    
      if (!$result) // pokud nenasala chyba pri updatu, muzeme presmerovat zpatky na vypis ucitelu
      {
        die('Invalid query: ' . mysql_error());
      }
      else
      {
        header('Location: admin.php?action=' . $_GET[item]);
      }
    }
  }  
?>

<section>
  <h3>Editace polo¾ky</h3>
  <form class="form-horizontal" method="post">
  <div class="form-group">
    <label class="control-label col-sm-2" for="name">Jméno:</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="name" id="name" aria-describedby="basic-addon3" value="<?php echo($items[1]); ?>">
    </div>
  </div>
  <div class="form-group">
    <label class="control-label col-sm-2" for="surname">Pøíjmení:</label>
    <div class="col-sm-10">
      <input type="text" class="form-control" name="surname" id="surname" aria-describedby="basic-addon3" value="<?php echo($items[2]); ?>">
    </div>
  </div>
  <?php
  if($_GET['item'] == 'students') // jestlize editujeme zaka, zajimaji nas jeste pole "login" a "heslo"
  {
    echo
    ('
    <div class="form-group">
      <label class="control-label col-sm-2" for="login">Login:</label>
      <div class="col-sm-10">
        <input type="text" class="form-control" name="login" id="login" aria-describedby="basic-addon3" value=');echo($items[3]);echo('>
      </div>
    </div>
    <div class="form-group">
      <label class="control-label col-sm-2" for="password">Heslo:</label>
      <div class="col-sm-10">
        <input type="password" class="form-control" name="password" id="password" aria-describedby="basic-addon3">
      </div>
    </div>
    ');
  }
  ?>
  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <input type="submit" value="Ulo¾it zmìny"/>
    </div>
  </div>
</form>
</section>
