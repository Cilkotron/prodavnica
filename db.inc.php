<?php

$conf = require './config.inc.php';


$connection_string = "mysql:host={$conf['dbhost']};dbname={$conf['dbname']};charset=utf8;";

try {

  $db = new PDO($connection_string, $conf['dbuser'], $conf['dbpass']);

  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);

  return $db;

} catch(PDOException $e) {
  var_dump($e);
}
