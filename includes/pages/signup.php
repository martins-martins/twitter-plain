<div class="global-nav">

</div>
<div class="main">
  <div class="login">
    <?php if($created_message != '') { ?>
    <div class="text-success"><?php echo $created_message; ?></div>
    <?php } ?>  
    <h3>Join Twitter</h3>
    <form method="post" action="" role="form">
      <div class="text-danger"><?php echo $errors['fullname']; ?></div>
      <div class="form-group">
        <input name="fullname" class="form-control" placeholder="Full name" value="<?php echo $user['fullname']; ?>">
      </div>
      <div class="text-danger"><?php echo $errors['email']; ?></div>
      <div class="form-group">
        <input name="email" type="email" class="form-control" placeholder="Email" value="<?php echo $user['email']; ?>">
      </div>
      <small>Password must be at least 6 characters long</small>
      <div class="text-danger"><?php echo $errors['password']; ?></div>
      <div class="form-group">
        <input name="password" type="password" class="form-control" placeholder="Password" value="<?php echo $user['password']; ?>">
      </div>
      <div class="form-group">
        <input name="password2" type="password" class="form-control" placeholder="Repeat Password" value="<?php echo $user['password2']; ?>">
      </div>
      <div class="text-danger"><?php echo $errors['username']; ?></div>
      <div class="form-group">
        <input name="username" class="form-control" placeholder="Choose username" value="<?php echo $user['username']; ?>">
      </div>
      <input name="submit" type="submit" class="btn btn-default" value="Create My Account">
    </form>
    <a href="<?php echo $__links['index']; ?>">Have Account ? Log In</a>
  </div>
</div>