<?php

require_once './User.class.php';
require_once './Helper.class.php';

$_loggedInUser = new User();
$_loggedInUser->loadLoggedInUser();

if( $_loggedInUser->acc_type != "admin" ) {
  Helper::addError('You have to be an administrator to access requested page.');
  header("Location: ./index.php");
  die();
}
