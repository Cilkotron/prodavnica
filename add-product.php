<?php require './admin_only.php'; ?>

<?php

require_once './Category.class.php';
require_once './Product.class.php';
require_once './Helper.class.php';

$c = new Category();
$categories = $c->all();
$productToEdit = null;

if( isset($_POST['btn_addProduct']) ) {
  $newProduct = new Product();
  $newProduct->title = $_POST['title'];
  $newProduct->cat_id = $_POST['cat_id'];
  $newProduct->price = $_POST['price'];
  $newProduct->description = $_POST['description'];
  $newProduct->image_info = $_FILES['image'];
  if( $newProduct->insert() ) {
    Helper::addMessage('Product added successfully.');
  }
}

if( isset($_GET['id']) ) {
  $productToEdit = new Product($_GET['id']);

  if( !$productToEdit->created_at || $productToEdit->deleted_at ) {
    Helper::addError("Product not found.");
    header("Location: ./products.php");
    die();
  }
}

if( isset($_POST['btn_updateProduct']) ) {
  $productToEdit->title = $_POST['title'];
  $productToEdit->cat_id = $_POST['cat_id'];
  $productToEdit->price = $_POST['price'];
  $productToEdit->description = $_POST['description'];
  $productToEdit->image_info = $_FILES['image'];
  if( $productToEdit->update() ) {
    Helper::addMessage('Product updated successfully.');
  }
}

?>

<?php include './header.layout.php'; ?>

<?php if($productToEdit) { ?>
<h1 class="my-5">Update product details</h1>
<?php } else { ?>
<h1 class="my-5">Add new product</h1>
<?php } ?>

<form action="" method="post" enctype="multipart/form-data">

  <div class="form-row">

    <div class="form-group col-md-6">
      <label for="inputTitle">Title</label>
      <input type="text" name="title" class="form-control" id="inputTitle" placeholder="Product title" value="<?php
      
      if( $productToEdit ) {
        echo $productToEdit->title;
      }

      ?>" required />
    </div>


    <div class="form-group col-md-6">
      <label for="inputCategory">Category</label>
      <select name="cat_id" id="inputCategory" class="form-control">

        <?php foreach($categories as $category) { ?>
          <option value="<?php echo $category->id; ?>"<?php
            
            if( $productToEdit
            && $category->id == $productToEdit->cat_id ) {
              echo " selected";
            }

            ?>>
            <?php echo $category->title; ?>
            </option>
        <?php } ?>

      </select>
    </div>

  </div>

  <div class="form-row">

    <div class="form-group col-md-6">

      <?php if($productToEdit) { ?>
        <img src="<?php echo $productToEdit->img; ?>" class="product-details-img" />
      <?php } ?>

      <label for="inputImage">Image</label>
      <input type="file" name="image" id="inputImage" class="form-control-file" />
    </div>

    <div class="form-group col-md-6">
      <label for="inputPrice">Price</label>
      <input type="number" name="price" id="inputPrice" step="0.01" class="form-control" placeholder="Product price" value="<?php
      
      if( $productToEdit ) {
        echo $productToEdit->price;
      }

      ?>" required />
    </div>

  </div>

  <div class="form-row">

    <div class="form-group col-md-12">
      <label for="inputDescription">Description</label>
      <textarea name="description" class="form-control" id="inputDescription" rows="3" placeholder="Product description..."><?php
      
      if( $productToEdit ) {
        echo $productToEdit->description;
      }

      ?></textarea>
    </div>

  </div>

  <div class="d-flex justify-content-end">
    <?php if($productToEdit) { ?>
      <button name="btn_updateProduct" class="btn btn-outline-primary">
        <i class="fas fa-plus-circle"></i>
        Update product details
      </button>
    <?php } else { ?>
      <button name="btn_addProduct" class="btn btn-outline-primary">
        <i class="fas fa-plus-circle"></i>
        Add product
      </button>
    <?php } ?>
  </div>

</form>

<?php include './footer.layout.php'; ?>
