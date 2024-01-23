<?php

require_once './User.class.php';

$loggedInUser = new User();
$loggedInUser->loadLoggedInUser();

?>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand" href="./index.php">WebShop</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav mr-auto">
        <li class="nav-item active">
          <a class="nav-link" href="./index.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./products.php">Products</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="./contact.php">Contact</a>
        </li>
      </ul>

      <form action="./products.php" method="get" class="form-inline my-2 my-lg-0">
        <input name="search" class="form-control mr-sm-2" type="search" placeholder="Search" aria-label="Search">
        <button class="btn btn-outline-success my-2 my-sm-0">Search</button>
      </form>

      <ul class="navbar-nav ml-auto">

      <?php if(User::isLoggedIn()) { ?>
        <li class="nav-item">
          <a href="#" class="nav-link show-popup">
            <i class="fas fa-user"></i>
          </a>
        </li>
        <li class="nav-item">
          <a href="./cart.php" class="nav-link">
            <i class="fas fa-shopping-cart"></i>
          </a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user"></i>
            <?php echo $loggedInUser->name; ?>
            (<?php echo $loggedInUser->email; ?>)
          </a>
          <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">
            <a class="dropdown-item" href="./update-profile.php">
              <i class="fas fa-user-circle"></i>
              Update profile
            </a>

            <?php if( $loggedInUser->acc_type == "admin" ) { ?>
              <h6 class="dropdown-header">Administration</h6>
              <a class="dropdown-item" href="./manage-categories.php">
                <i class="far fa-edit"></i>
                Manage categories
              </a>
              <a class="dropdown-item" href="./add-product.php">
                <i class="far fa-plus-square"></i>
                Add Product
              </a>
            <?php } ?>
            
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="./logout.php">
              <i class="fas fa-sign-out-alt"></i>
              Log out
              </a>
          </div>
        </li>
      <?php } else { ?>
        <li class="nav-item">
          <a href="./login.php" class="nav-link">Login</a>
        </li>
        <li class="nav-item">
          <a href="./register.php" class="nav-link">Register</a>
        </li>
      <?php } ?>

      </ul>
    </div>

  </div>
</nav>