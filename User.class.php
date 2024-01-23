<?php

class User {
  private $db;
  public $id;
  public $name;
  public $email;
  public $password;         // md5 verzija sifre
  public $new_password;     // sifra u obliku obicnog teksta
  public $password_repeat;  // samo za proveru da li se sifre poklapaju
  public $acc_type;
  public $created_at;
  public $deleted_at;

  function __construct($id = null) {
    $this->db = require './db.inc.php';
    require_once './Helper.class.php';

    if( $id ) {
      $this->id = $id;
      $this->loadFromDb();
    }
  }

  public function loadFromDb() {
    $stmt_load = $this->db->prepare("
      SELECT *
      FROM `users`
      WHERE `id` = :id
    ");
    $stmt_load->execute([
      ':id' => $this->id
    ]);
    $user = $stmt_load->fetch();

    if ( $user ) {
      $this->name = $user->name;
      $this->email = $user->email;
      $this->password = $user->password;
      $this->acc_type = $user->acc_type;
      $this->created_at = $user->created_at;
      $this->deleted_at = $user->deleted_at;
    }
  }

  public function passwordIsValid() {
    if( $this->new_password == "" ) {
      Helper::addError("Password is empty.");
      return false;
    }

    if( $this->new_password != $this->password_repeat ) {
      Helper::addError("Passwords do not match.");
      return false;
    }

    // if( strlen($this->new_password) < 6 ) {
    //   Helper::addError("Password min length is 6.");
    //   return false;
    // }

    return true;
  }

  public function nameIsValid() {
    if( $this->name == "" ) {
      Helper::addError("Name can not be empty.");
      return false;
    }

    return true;
  }

  public function emailIsValid() {
    if( $this->email == "" ) {
      Helper::addError("Email can not be empty.");
      return false;
    }

    if( !filter_var($this->email, FILTER_VALIDATE_EMAIL) ) {
      Helper::addError("Not a valid email.");
      return false;
    }

    $stmt_getUserByEmail = $this->db->prepare("
      SELECT *
      FROM `users`
      WHERE `email` = :email
    ");
    $stmt_getUserByEmail->execute([ ':email' => $this->email ]);

    $user = $stmt_getUserByEmail->fetch();

    // $user                                    - email postoji u bazi
    // $user->id != self::getLoggedInUserId()   - nije moja email adresa
    if( $user && $user->id != self::getLoggedInUserId() ) {
      Helper::addError("Email is already taken.");
      return false;
    }

    return true;
  }

  public function insert() {

    if( !$this->emailIsValid() ) {
      return false;
    }

    if( !$this->nameIsValid() ) {
      return false;
    }

    if( !$this->passwordIsValid() ) {
      return false;
    }

    $this->password = md5($this->new_password);

    $stmt_insert = $this->db->prepare("
      INSERT INTO `users`
      (`name`, `email`, `password`)
      VALUES
      (:name, :email, :password)
    ");
    $result = $stmt_insert->execute([
      ':name' => $this->name,
      ':email' => $this->email,
      ':password' => $this->password
    ]);
    if($result) {
      $this->id = $this->db->lastInsertId();
      $this->loadFromDb();
    }
    return $result;
  }

  public function update() {
    if( !$this->nameIsValid() ) {
      return false;
    }

    if( !$this->emailIsValid() ) {
      return false;
    }

    if( $this->new_password && $this->passwordIsValid() ) {
      $this->password = md5($this->new_password);
    }

    $stmt_update = $this->db->prepare("
      UPDATE `users`
      SET
        `name` = :name,
        `email` = :email,
        `password` = :password,
        `acc_type` = :acc_type
      WHERE `id` = :id
    ");

    return $stmt_update->execute([
      ':name' => $this->name,
      ':email' => $this->email,
      ':password' => $this->password,
      ':acc_type' => $this->acc_type,
      ':id' => $this->id
    ]);
  }

  public function delete() {
    $stmt_delete = $this->db->prepare("
      UPDATE `users`
      SET `deleted_at` = now()
      WHERE `id` = :id
    ");
    return $stmt_delete->execute([
      ':id' => $this->id
    ]);
  }

  public function login() {
    $stmt_login = $this->db->prepare("
      SELECT *
      FROM `users`
      WHERE `email` = :email
      AND `password` = :password
    ");
    $stmt_login->execute([
      ':email' => $this->email,
      ':password' => md5($this->password)
    ]);

    $user = $stmt_login->fetch();

    if( $user ) {
      Helper::addMessage('Login successfull!');
      Helper::sessionStart();
      $_SESSION['user_id'] = $user->id;
      return true;
    }

    Helper::addError('Invalid credentials.');
    return false;
  }

  public static function isLoggedIn() {
    require_once './Helper.class.php';

    Helper::sessionStart();

    return isset($_SESSION['user_id']);
  }

  public static function getLoggedInUserId() {
    require_once './Helper.class.php';

    Helper::sessionStart();

    if( isset($_SESSION['user_id']) ) {
      return $_SESSION['user_id'];
    } else {
      return false;
    }
  }
  
  public function loadLoggedInUser() {
    require_once './Helper.class.php';

    Helper::sessionStart();

    if( isset($_SESSION['user_id']) ) {
      $this->id = $_SESSION['user_id'];
      $this->loadFromDb();
    }
  }

  public function getCart() {
    $stmt_getCart = $this->db->prepare("
      SELECT
        `carts`.`id` as cart_id,
        `products`.`id` as product_id,
        `products`.`title`,
        `carts`.`quantity`,
        `products`.`price`
      FROM `carts`, `products`
      WHERE `carts`.`user_id` = :user_id
      AND `products`.`id` = `carts`.`product_id`
    ");
    $stmt_getCart->execute([
      ':user_id' => self::getLoggedInUserId()
    ]);
    return $stmt_getCart->fetchAll();
  }
}



// // obicna - unutar klase
// $this->nazivObicneMedote();

// // obicna - van klase
// $objekatKlase->nazivObicneMetode();

// // staticka - unutar klase
// self::nazivStatickeMetode();

// //stasticka - van klase
// NazivKlase::nazivStatickeMetode();
