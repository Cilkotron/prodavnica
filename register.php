<?php

require_once './User.class.php';
require_once './Helper.class.php';

if ( isset($_POST['btn_createAccount']) ) {
  $newUser = new User();
  $newUser->name = $_POST['name'];
  $newUser->email = $_POST['email'];
  $newUser->new_password = $_POST['password'];
  $newUser->password_repeat = $_POST['password_repeat'];

  // $success = $newUser->insert();
  // if($success) {
  //     Helper::addMessage("Registration successfull!");
  // }

  if( $newUser->insert() ) {
    Helper::addMessage("Registration successfull!");
  }
}

?>

<?php include './header.layout.php'; ?>

<h1 class="my-5">Register</h1>


<div class="row justify-content-center">
  <div class="col-md-8">
  
    <form action="" method="post">
      <div class="form-group">
        <label for="inputName">Name</label>
        <input type="text" name="name" class="form-control" id="inputName" placeholder="Enter name">
      </div>
      <div class="form-group">
        <label for="inputEmail">Email address</label>
        <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Enter email">
      </div>
      <div class="form-group">
        <label for="inputPassword">Password</label>
        <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
      </div>
      <div class="form-group">
        <label for="inputPasswordRepeat">Password repeat</label>
        <input type="password" name="password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password again">
      </div>

      <div class="d-flex justify-content-end">
        <button class="btn btn-primary" name="btn_createAccount">Create account</button>
      </div>
    </form>

  </div>
</div>

<?php include './footer.layout.php'; ?>