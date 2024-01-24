<?php

require_once './Product.class.php';

$p = new Product();

if( isset($_GET['page']) ) {
  $page = $_GET['page'];
} else {
  $page = 1;
}



if( isset($_GET['search']) ) {
  $products= $p->search($_GET['search']);
} else if (isset($_GET['cat_id'])) {
  $products = $p->fromCategory($_GET['cat_id']);
} else {
  $products = $p->paginate($page);
}

$totalNumOfProducts = $p->totalNumOfProducts();
$conf = require './config.inc.php';
$numOfPages = ceil($totalNumOfProducts / $conf['products_per_page']);


if( $page <= 1 ) {
  $prev = 1;
  // $prev = $numOfPages;
} else {
  $prev = $page - 1;
}

if( $page >= $numOfPages ) {
  $next = $numOfPages;
  // $next = 1;
} else {
  $next = $page + 1;
}

?>

<?php include './header.layout.php'; ?>

<?php if( isset($_GET['search']) && $_GET['search'] != "" ) { ?>
<h1 class="my-5">Search results for "<?php echo $_GET['search'] ?>"</h1>
<?php } else { ?>
<h1 class="my-5">Products</h1>
<?php } ?>

<div class="row">

  <?php foreach($products as $product) { ?>
    <div class="col-md-4">
      <div class="card mb-4">
        <img class="card-img-top products-img" src="<?php echo $product->img; ?>" />
        <div class="card-body">
          <h5 class="card-title">
            <?php echo $product->title; ?>
          </h5>
          <p class="card-text">
            <strong>Price:</strong>
            <?php echo $product->price; ?>
          </p>
          <div class="d-flex justify-content-end">
            <a href="./product-details.php?id=<?php echo $product->id; ?>" class="btn btn-primary">Details</a>
          </div>
        </div>
      </div>
    </div>
  <?php } ?>

</div>

<nav aria-label="Page navigation example">
  <?php if(count($products) > 0) : ?> 
  <ul class="pagination justify-content-center">
    <li class="page-item">
      <a class="page-link" href="./products.php?page=<?php echo $prev; ?>" aria-disabled="true">Previous</a>
    </li>

    <?php for($i = 1; $i <= $numOfPages; $i++) { ?>
      <li class="page-item<?php
          if( $page == $i ) {
            echo " active";
          }
        ?>">
        <a
          class="page-link"
          href="./products.php?page=<?php echo $i; ?>">
          <?php echo $i; ?>
        </a>
      </li>
    <?php } ?>
    <li class="page-item">
      <a class="page-link" href="./products.php?page=<?php echo $next; ?>">Next</a>
    </li>
  </ul>
  <?php endif; ?> 
</nav>

<?php include './footer.layout.php'; ?>