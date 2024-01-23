<?php require_once './Helper.class.php'; ?>
<?php require_once './Product.class.php'; ?>
<?php require_once './User.class.php'; ?>

<?php

if( !isset($_GET['id']) ) {
  Helper::addError("The product ID is missing.");
  header("Location: ./products.php");
  die();
}

$loggedInUser = new User();
$loggedInUser->loadLoggedInUser();
$product = new Product($_GET['id']);

if( !$product->created_at || $product->deleted_at ) {
  Helper::addError("Product not found.");
  header("Location: ./products.php");
  die();
}

if( isset($_POST['btn_addToCart']) ) {
  $addedToCart = $product->addToCart($_POST['quantity']);
  if( $addedToCart ) {
    Helper::addMessage('Product added to cart successfully.');
  } else {
    Helper::addError('Failed to add product to cart.');
  }
}

if( isset($_POST['btn_rating']) ) {
  $product->rate($_POST['btn_rating']);
}


?>

<?php include './header.layout.php'; ?>

<h1 class="my-5"><?php echo $product->title; ?></h1>

<div class="row">

  <div class="col-md-5">
    <img src="<?php echo $product->img; ?>" class="product-details-img" />
  </div>

  <div class="col-md-7">
    <h3 class="mb-5">Description</h3>
    <p class="mb-5">
    <?php echo $product->description; ?>
    </p>

    <div class="d-flex flex-column align-items-end">
      <h5 class="mt-5"><?php echo $product->price; ?> RSD</h5>

      <form action="" method="post">
        <div class="input-group mt-3">
          <input type="number" name="quantity" class="form-control" value="1" min="1" />
          <div class="input-group-append">
            <button name="btn_addToCart" class="btn btn-outline-primary">Add to cart</button>
          </div>
        </div>
      </form>

      <?php if( $loggedInUser->acc_type == "admin" ) { ?>
        <a href="./add-product.php?id=<?php echo $product->id; ?>" class="btn btn-outline-success mt-3">Update product details</a>
      <?php } ?>
    </div>
  </div>

</div>

<div class="row">
  <div class="col-md-12 my-5">

    <h5>Rating:</h5>

    <form action="" method="post">
      <button name="btn_rating" value="1" class="btn_rating fas fa-star"></button>
      <button name="btn_rating" value="2" class="btn_rating fas fa-star"></button>
      <button name="btn_rating" value="3" class="btn_rating fas fa-star"></button>
      <button name="btn_rating" value="4" class="btn_rating fas fa-star"></button>
      <button name="btn_rating" value="5" class="btn_rating fas fa-star"></button>
      
      ( <b>
        <?php echo number_format($product->getRating(), 2, '.', ','); ?>
      </b> )
    </form>

  </div>
</div>


<?php include './footer.layout.php'; ?>
