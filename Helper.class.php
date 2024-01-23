<?php

class Helper {
  // ako je env development log metoda ce upisivati u
  // log.html, ako je bilo sta drugo, preskocice upisivanje
  public static $env = "development";

  public static function sessionStart() {
    if( !isset($_SESSION) ) {
      session_start();
    }
  }

  public static function addMessage($m) {
    self::sessionStart();
    $_SESSION['message'] = $m;
  }

  public static function getMessage() {
    self::sessionStart();
    $m = null;
    if( isset($_SESSION['message']) ) {
      $m = $_SESSION['message'];
      unset($_SESSION['message']);
    }
    return $m;
  }

  public static function addError($e) {
    self::sessionStart();
    $_SESSION['error'] = $e;
  }

  public static function getError() {
    self::sessionStart();
    $e = null;
    if( isset($_SESSION['error']) ) {
      $e = $_SESSION['error'];
      unset($_SESSION['error']);
    }
    return $e;
  }

  public static function log($message) {
    if(self::$env != "development") {
      return;
    }

    date_default_timezone_set("Europe/Belgrade");
    $filename = "./log.html";
    $timestamp = date("Y/m/d H:i:s");

    ob_start();
    var_dump($message);
    $result = ob_get_clean();

    $log = "";
    $log .= PHP_EOL;
    $log .= "<h1>$timestamp</h1>";
    $log .= PHP_EOL;
    $log .= $result;
    $log .= PHP_EOL;
    $log .= "<br />";
    $log .= PHP_EOL;

    $f = fopen($filename, 'a+');
    fwrite($f, $log);
    fclose($f);
  }

  public static function formatNumber($number) {
    return number_format($number, 2, '.', ',');
  }
}
