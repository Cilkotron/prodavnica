<?php

require_once './Helper.class.php';

$messageToShow = Helper::getMessage();
$errorToShow = Helper::getError();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>WebShop</title>
  <link rel="stylesheet" href="./css/bootstrap.min.css" />
  <link rel="stylesheet" href="./css/index.css" />
  <link rel="stylesheet" href="./css/all.min.css" />
</head>
<body>

  <?php include './navbar.inc.php'; ?>

  <div class="container">
  
    <div class="row">
    
      <div class="col-md-3">
        <div class="Sidebar">
          <?php include './sidebar.inc.php'; ?>
        </div>
      </div>
      
      <div class="col-md-9">
        <div class="Content">

        <?php if( $errorToShow ) { ?>
          <div class="alert alert-danger my-4">
            <b>Error!</b>
            <?php echo $errorToShow; ?>
          </div>
        <?php } ?>

        <?php if( $messageToShow ) { ?>
          <div class="alert alert-success my-4">
            <b>Success!</b>
            <?php echo $messageToShow; ?>
          </div>
        <?php } ?>
