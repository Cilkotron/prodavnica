<?php

class Product {
  private $db;
  public $id;
  public $cat_id;
  public $title;
  public $description;
  public $price;
  public $img;
  public $image_info;
  public $created_at;
  public $deleted_at;
  private $product_images_path;
  private $allowed_image_types;
  private $max_image_size;

  function __construct($id = null) {
    $this->db = require './db.inc.php';
    require_once './Helper.class.php';
    $conf = require './config.inc.php';

    $this->product_images_path = $conf['product_images_path'];
    $this->allowed_image_types = [
      'image/jpeg',
      'image/png',
      'image/gif'
    ];
    $this->max_image_size = 5 * 1024 * 1024;

    if($id) {
      $this->id = $id;
      $this->loadFromDb();
    }
  }

  public function loadFromDb() {
    $stmt_get = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE `id` = :id
    ");
    $stmt_get->execute([ ':id' => $this->id ]);

    $product = $stmt_get->fetch();

    if($product) {
      foreach($product as $key => $value) {
        $this->$key = $value;
      }
    }
  }

  public function insert() {
    $stmt_insert = $this->db->prepare("
      INSERT INTO `products`
      (`cat_id`, `title`, `description`, `price`)
      VALUES
      (:cat_id, :title, :description, :price)
    ");
    $result =  $stmt_insert->execute([
      ':cat_id' => $this->cat_id,
      ':title' => $this->title,
      ':description' => $this->description,
      ':price' => $this->price
    ]);

    if( $result ) {
      $this->id = $this->db->lastInsertId();
      $this->loadFromDb();
      if( $this->handleUploadedImage() ) {
        return $this->update();
      } else {
        $this->delete();
        return false;
      }
    }
  }

  public function imageIsValid() {

    if ( $this->image_info['size'] > $this->max_image_size ) {
      Helper::addError('Maximum allowed image size is 5MB.');
      return false;
    }

    if ( !in_array($this->image_info['type'], $this->allowed_image_types) ) {
      Helper::addError('Invalid image type.');
      return false;
    }

    return true;
  }
  
  public function handleUploadedImage() {
    if( $this->image_info['error'] != 0 ) {
      Helper::addError('Error uploading image.');
      return false;
    }

    if( !$this->imageIsValid() ) {
      return false;
    }

    if( !file_exists($this->product_images_path) ) {
      mkdir($this->product_images_path, 0777, true);
    }

    $file_info = pathinfo($this->image_info['name']);

    $img_dest = "";
    $img_dest .= $this->product_images_path;
    $img_dest .= $this->id;
    $img_dest .= ".";
    $img_dest .= $file_info['extension'];

    move_uploaded_file($this->image_info['tmp_name'], $img_dest);

    $this->img = $img_dest;
    return true;
  }

  public function update() {
    $stmt_update = $this->db->prepare("
      UPDATE `products`
      SET
        `cat_id` = :cat_id,
        `title` = :title,
        `description` = :description,
        `price` = :price,
        `img` = :img
      WHERE `id` = :id
    ");
    
    if( $this->image_info['error'] == 0 ) {
      $this->handleUploadedImage();
    }

    return $stmt_update->execute([
      ':cat_id' => $this->cat_id,
      ':title' => $this->title,
      ':description' => $this->description,
      ':price' => $this->price,
      ':img' => $this->img,
      ':id' => $this->id
    ]);
  }

  public function delete() {
    $stmt_delete = $this->db->prepare("
      UPDATE `products`
      SET `deleted_at` = now()
      WHERE `id` = :id
    ");
    return $stmt_delete->execute([ ':id' => $this->id ]);
  }

  public function all() {
    $stmt_getAll = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE `deleted_at` IS NULL
    ");
    $stmt_getAll->execute();
    return $stmt_getAll->fetchAll();
  }
 
  public function addToCart($quantity) {
    require_once './User.class.php';
    if( !User::isLoggedIn() ) {
      return false;
    }

    $stmt_getCart = $this->db->prepare("
      SELECT *
      FROM `carts`
      WHERE `user_id` = :user_id
      AND `product_id` = :product_id
    ");

    $stmt_getCart->execute([
      ':user_id' => User::getLoggedInUserId(),
      ':product_id' => $this->id
    ]);

    if ( $stmt_getCart->rowCount() > 0 ) {
      $cartRow = $stmt_getCart->fetch();
      $newQuantity = $cartRow->quantity + $quantity;
      return $this->updateQuantity($newQuantity);
    } else {
      $stmt_addToCart = $this->db->prepare("
        INSERT INTO `carts`
        (`user_id`, `product_id`, `quantity`)
        VALUES
        (:user_id, :product_id, :quantity)
      ");

      return $stmt_addToCart->execute([
        ':user_id' => User::getLoggedInUserId(),
        ':product_id' => $this->id,
        ':quantity' => $quantity
      ]);
    }

  }

  public function updateQuantity($newQuantity) {
    require_once './User.class.php';

    $stmt_updateQuantity = $this->db->prepare("
      UPDATE `carts`
      SET `quantity` = :quantity
      WHERE `user_id` = :user_id
      AND `product_id` = :product_id
    ");
    return $stmt_updateQuantity->execute([
      ':quantity' => $newQuantity,
      ':user_id' => User::getLoggedInUserId(),
      ':product_id' => $this->id
    ]);
  }

  public function removeFromCart($cartId) {
    $stmt_delete = $this->db->prepare("
      DELETE
      FROM `carts`
      WHERE `id` = :cart_id
    ");
    return $stmt_delete->execute([ ':cart_id' => $cartId ]);
  }

  public function search($query) {
    $search = "%$query%";
    $stmt_search = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE (
        `title` LIKE :query
        OR `description` LIKE :query
      )
      AND `deleted_at` IS NULL
    ");
    $stmt_search->execute([ ':query' => $search ]);
    return $stmt_search->fetchAll();
  }

  public function fromCategory($catId) {
    $stmt_get = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE `cat_id` = :cat_id
      AND `deleted_at` IS NULL
    ");
    $stmt_get->execute([ ':cat_id' => $catId ]);
    return $stmt_get->fetchAll();
  }

  public function rate($rating) {
    require_once './User.class.php';

    if( !User::isLoggedIn() ) {
      Helper::addError('You have to login to be able to rate product.');
      return false;
    }

    if( $rating > 5 || $rating < 1 ) {
      Helper::addError("Error! Rating not allowed.");
      return false;
    }

    $stmt_getRating = $this->db->prepare("
      SELECT *
      FROM `product_ratings`
      WHERE `user_id` = :user_id
      AND `product_id` = :product_id
    ");
    $stmt_getRating->execute([
      ':user_id' => User::getLoggedInUserId(),
      ':product_id' => $this->id
    ]);

    $ratingRow = $stmt_getRating->fetch();

    if( $ratingRow ) {
      $stmt_updateRating = $this->db->prepare("
        UPDATE `product_ratings`
        SET `rating` = :rating
        WHERE `user_id` = :user_id
        AND `product_id` = :product_id
      ");
      $stmt_updateRating->execute([
        ':rating' => $rating,
        ':user_id' => User::getLoggedInUserId(),
        ':product_id' => $this->id
      ]);
    } else {
      $stmt_insertRating = $this->db->prepare("
        INSERT INTO `product_ratings`
        (`user_id`, `product_id`, `rating`)
        VALUES
        (:user_id, :product_id, :rating)
      ");
      $stmt_insertRating->execute([
        ':user_id' => User::getLoggedInUserId(),
        ':product_id' => $this->id,
        ':rating' => $rating
      ]);
    }
  }

  public function getRating() {
    $stmt_getRating = $this->db->prepare("
      SELECT AVG(rating) as avg_rating
      FROM `product_ratings`
      WHERE `product_id` = :product_id
    ");

    $stmt_getRating->execute([ ':product_id' => $this->id ]);
    // $row = $stmt_getRating->fetch();
    // return $row->avg_rating;
    return $stmt_getRating->fetch()->avg_rating;
  }

  public function paginate($page = 1) {
    $conf = require './config.inc.php';
    $offset = ( $page - 1 ) * $conf['products_per_page'];
    $stmt_paginate = $this->db->prepare("
      SELECT *
      FROM `products`
      WHERE `deleted_at` IS NULL
      LIMIT {$conf['products_per_page']}
      OFFSET {$offset}
    ");
    $stmt_paginate->execute();
    return $stmt_paginate->fetchAll();
  }

  public function totalNumOfProducts() {
    $stmt_all = $this->db->prepare("
      SELECT count(*) as total_num_of_products
      FROM `products`
      WHERE `deleted_at` IS NULL
    ");
    $stmt_all->execute();
    // $row = $stmt_all->fetch();
    // return $row->total_num_of_products;
    return $stmt_all->fetch()->total_num_of_products;
  }
}
