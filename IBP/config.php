<?php  
  $db = mysql_connect('localhost:/var/run/mysql/mysql.sock', 'xhalik01', 'dajdaj8r');
  if (!$db) die('nelze se pripojit '.mysql_error());
  if (!mysql_select_db('xhalik01', $db)) die('databaze neni dostupna '.mysql_error());
?>
