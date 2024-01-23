<?php

  require_once './user_only.php';
  require_once './User.class.php';
  require_once './Product.class.php';
  require_once './Helper.class.php';
  
  $u = new User();
  $p = new Product();

  if( isset($_POST['btn_removeFromCart']) ) {
    if ( $p->removeFromCart($_POST['cart_id']) ) {
      Helper::addMessage("Product removed from cart.");
    } else {
      Helper::addError("Failed to remove product from cart.");
    }
  }

  if( isset($_POST['btn_updateQuantity']) ) {
    $p->id = $_POST['product_id'];
    if( $p->updateQuantity($_POST['new_quantity']) ) {
      Helper::addMessage("Quantity updated.");
    }
  }

  $products = $u->getCart();

?>
<?php include './header.layout.php'; ?>

<h1 class="my-5">Cart</h1>

<table class="table">

  <thead>
    <tr>
      <th>Product title</th>
      <th>Quantity</th>
      <th>Price</th>
      <th>Total price</th>
      <th>Actions</th>
    </tr>
  </thead>

  <tbody>
  <?php $total = 0; ?>
  <?php foreach($products as $product) { ?>
  <?php $total += $product->quantity * $product->price; ?>
    <tr>
      <th><?php echo $product->title; ?></th>
      <td>
        <form action="" method="post">
          <div class="input-group input-group-sm">
            <input type="number" name="new_quantity" class="form-control" value="<?php echo $product->quantity; ?>" min="1" placeholder="Enter new quantity" />
            <input type="hidden" name="product_id" value="<?php echo $product->product_id; ?>" />
            <div class="input-group-append">
              <button name="btn_updateQuantity" class="btn btn-outline-primary">Update</button>
            </div>
          </div>
        </form>
      </td>
      <td><?php echo $product->price; ?> RSD</td>
      <td><?php echo $product->quantity * $product->price; ?> RSD</td>
      <td>
        <form action="" method="post">
          <input type="hidden" name="cart_id" value="<?php echo $product->cart_id; ?>" />
          <button name="btn_removeFromCart" class="btn btn-outline-danger btn-sm">Remove from cart</button>
        </form>
      </td>
    </tr>
    <?php } ?>

  </tbody>

  <tfoot>
    <tr>
      <td></td>
      <td></td>
      <td><b>Total:</b></td>
      <td><?php echo $total; ?> RSD</td>
      <td></td>
    </tr>
  </tfoot>

</table>

<?php include './footer.layout.php'; ?>