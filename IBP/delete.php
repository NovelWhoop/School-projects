<?php
  include ("config.php");

  $result = mysql_query("DELETE FROM $_GET[item] WHERE ID = $_GET[id]", $db);
  if (!$result)
  {
    die('Invalid query: ' . mysql_error());
  }
  else
  {
    header('Location: admin.php?action=' . $_GET[item]);
  }
?>
