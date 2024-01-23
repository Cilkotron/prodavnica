<?php

class Category {
  private $db;
  public $id;
  public $title;

  function __construct($id = null) {
    $this->db = require './db.inc.php';

    if( $id ) {
      $this->id = $id;
      $this->loadFromDb();
    }
  }

  public function loadFromDb() {
    $stmt_load = $this->db->prepare("
      SELECT *
      FROM `categories`
      WHERE `id` = :id
    ");
    $stmt_load->execute([
      ':id' => $this->id
    ]);
    $category = $stmt_load->fetch();

    if ( $category ) {
      $this->title = $category->title;
    }
  }

  public function insert() {
    $stmt_insert = $this->db->prepare("
      INSERT INTO `categories`
      (`title`)
      VALUES
      (:title)
    ");
    return $stmt_insert->execute([
      ':title' => $this->title
    ]);
  }

  public function update() {
    $stmt_update = $this->db->prepare("
      UPDATE `categories`
      SET `title` = :title
      WHERE `id` = :id
    ");

    return $stmt_update->execute([
      ':title' => $this->title,
      ':id' => $this->id
    ]);
  }

  public function delete() {
    $stmt_delete = $this->db->prepare("
      DELETE
      FROM `categories`
      WHERE `id` = :id
    ");
    return $stmt_delete->execute([
      ':id' => $this->id
    ]);
  }

  public function all() {
    $stmt_all = $this->db->prepare("
      SELECT
        `id`,
        `title`,
        (
          SELECT count(*)
          FROM `products`
          WHERE `cat_id` = `categories`.`id`
          AND `deleted_at` IS NULL
        ) as num_of_products
      FROM `categories`
    ");
    $stmt_all->execute();
    return $stmt_all->fetchAll();
  }
}


// SELECT
//   categories.id,
//   categories.title,
//   (
//     SELECT count(*)
//     FROM products
//     WHERE products.cat_id = categories.id
//   ) as num_of_products
// FROM categories