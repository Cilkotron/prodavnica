<?php

$db = require './db.inc.php';

/*
*   CREATE USERS TABLE
*/
$stmt_createUsersTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `users` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(30),
    `email` varchar(30),
    `password` varchar(32),
    `acc_type` enum('user', 'admin') DEFAULT 'user',
    `created_at` datetime DEFAULT now(),
    `deleted_at` datetime DEFAULT NULL
  )
");
$stmt_createUsersTable->execute();

/*
*   INSERT ADMIN ACCOUNT
*/
$stmt_getAllUsers = $db->prepare("
  SELECT *
  FROM `users`
");
$stmt_getAllUsers->execute();
// $users = $stmt_getAllUsers->fetchAll();
$numOfUsers = $stmt_getAllUsers->rowCount();

if( $numOfUsers <= 0 ) {
  $stmt_insertAdminAccount = $db->prepare("
    INSERT INTO `users`
    (`name`, `email`, `password`, `acc_type`)
    VALUES
    (:name, :email, :password, :acc_type)
  ");
  $stmt_insertAdminAccount->execute([
    ':name' => "Admin",
    ':email' => "admin@shop.com",
    ':password' => md5("123"),
    ':acc_type' => "admin"
  ]);
}


/*
*   CREATE PRODUCTS TABLE
*/
$stmt_createProductsTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `products` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `cat_id` int,
    `title` varchar(255),
    `description` text,
    `price` decimal(10, 2) DEFAULT 0,
    `img` varchar(255),
    `kolicina_na_stanju` int DEFAULT 0,
    `created_at` datetime DEFAULT now(),
    `deleted_at` datetime DEFAULT NULL
  )
");
$stmt_createProductsTable->execute();


/*
*   CREATE CATEGORIES TABLE
*/
$stmt_createCategoriesTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `categories` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `title` varchar(255)
  )
");
$stmt_createCategoriesTable->execute();


/*
*   CREATE CARTS TABLE
*/
$stmt_createCartsTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `carts` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `user_id` int,
    `product_id` int,
    `quantity` int,
    `created_at` datetime DEFAULT now()
  )
");
$stmt_createCartsTable->execute();

/**
 *  CREATE PRODUCT_RATINGS TABLE
 */
$stmt_createProductRatingsTable = $db->prepare("
  CREATE TABLE IF NOT EXISTS `product_ratings` (
    `id` int AUTO_INCREMENT PRIMARY KEY,
    `user_id` int,
    `product_id` int,
    `rating` int,
    `created_at` datetime DEFAULT now()
  )
");
$stmt_createProductRatingsTable->execute();
