<?php

require_once './User.class.php';
require_once './Helper.class.php';


if( !User::isLoggedIn() ) {
  Helper::addError('You have to login to access requested page.');
  header("Location: ./login.php");
  die();
}
