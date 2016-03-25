<section>
  <div class="navbar">
    <div class="pull-left nav nav-tabs">
      <nav class="nav nav-tabs pull-left">
        <a href="index.php?action=about" <?php if(count($_GET) && ($_GET['action']) == 'about') echo('class="active"')?> ><h1>INFORMACE</h1></a>
        <a href="index.php?action=courses" <?php if(count($_GET) && ($_GET['action']) == 'courses') echo('class="active"')?> ><h1>KLASICKÉ KURZY</h1></a>
        <a href="index.php?action=training" <?php if(count($_GET) && ($_GET['action']) == 'training') echo('class="active"')?> ><h1>PROFESNÍ ©KOLENÍ</h1></a>
        <a href="#contacts"><h1>KONTAKTY</h1></a>
      </nav>
    </div>
  </div>
  <h2>EDITACE STRÁNKY</h2>
</section>