<div class="global-nav">

</div>
<div class="main">
  <div class="login">
    <?php if($success_message != '') { ?>
      <div class="text-success"><?php echo $success_message; ?></div>
    <?php } 
          if($fail_message != '') { ?>
    <div class="text-danger"><?php echo $fail_message; ?></div>
    <?php } ?> 
    <h3>Welcome to Twitter</h3>
    <form method="post" action="">
      <div class="form-group">
        <input name="username" class="form-control" placeholder="Username">
      </div>
      <div class="form-group">
        <input name="password" type="password" class="form-control" placeholder="Password">
      </div> 
      <input name="submit" type="submit" class="btn btn-default" value="Log in">
    </form>
    <a href="<?php echo $__links['signup']; ?>">Sign up</a>
  </div>
</div>