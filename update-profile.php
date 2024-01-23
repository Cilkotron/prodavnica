<?php

require_once './User.class.php';
require_once './Helper.class.php';

$loggedInUserInfo = new User();
$loggedInUserInfo->loadLoggedInUser();

if ( isset($_POST['btn_updateGeneralInfo']) ) {
  $loggedInUserInfo->name = $_POST['name'];
  $loggedInUserInfo->email = $_POST['email'];
  if ( $loggedInUserInfo->update() ) {
    Helper::addMessage("Profile updated.");
  }
}

if ( isset($_POST['btn_updatePassword']) ) {
  $loggedInUserInfo->new_password = $_POST['password'];
  $loggedInUserInfo->password_repeat = $_POST['password_repeat'];
  if ( $loggedInUserInfo->update() ) {
    Helper::addMessage("Password updated.");
  }
}

?>

<?php include './header.layout.php'; ?>

<h1 class="my-5">Update profile</h1>

<ul class="nav nav-tabs" id="myTab" role="tablist">

  <li class="nav-item">
    <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General information</a>
  </li>

  <li class="nav-item">
    <a class="nav-link" id="password-tab" data-toggle="tab" href="#password" role="tab" aria-controls="password" aria-selected="false">Update password</a>
  </li>

</ul>

<div class="tab-content" id="myTabContent">

  <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">

    <div class="row justify-content-center mt-5">
      <div class="col-md-8">
      
        <form action="" method="post">
          <div class="form-group">
            <label for="inputName">Name</label>
            <input type="text" name="name" class="form-control" id="inputName" placeholder="Enter name" value="<?php echo $loggedInUserInfo->name; ?>" />
          </div>
          <div class="form-group">
            <label for="inputEmail">Email address</label>
            <input type="email" name="email" class="form-control" id="inputEmail" placeholder="Enter email" value="<?php echo $loggedInUserInfo->email; ?>" />
          </div>

          <div class="d-flex justify-content-end">
            <button class="btn btn-outline-primary" name="btn_updateGeneralInfo">Update general information</button>
          </div>
        </form>

      </div>
    </div>

  </div>

  <div class="tab-pane fade" id="password" role="tabpanel" aria-labelledby="password-tab">

    <div class="row justify-content-center mt-5">
      <div class="col-md-8">
      
        <form action="" method="post">
          <div class="form-group">
            <label for="inputPassword">Password</label>
            <input type="password" name="password" class="form-control" id="inputPassword" placeholder="Password">
          </div>
          <div class="form-group">
            <label for="inputPasswordRepeat">Password repeat</label>
            <input type="password" name="password_repeat" class="form-control" id="inputPasswordRepeat" placeholder="Password again">
          </div>

          <div class="d-flex justify-content-end">
            <button class="btn btn-outline-primary" name="btn_updatePassword">Update password</button>
          </div>
        </form>

      </div>
    </div>

  </div>

</div>

<?php include './footer.layout.php'; ?>
