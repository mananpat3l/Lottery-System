<link href="css/forms.css" rel="stylesheet">
<link href="css/svg.css" rel="stylesheet">
<script type="text/javascript" src="js/storage.js"></script>
<div id="login">
  <form class="col s12" action="controller.php" method="post" onsubmit="storeName()">
    <div>
      <div class="input-field s12">
        <input placeholder="Enter your email" id="email" name="email" type="email" class="validate" required>
        <label for="email" class="active">Email</label>
      </div>
    </div>
    <div>
      <div class="input-field s12">
        <label for="password">Password: </label>
        <input id="password" type="password" name="password" class="validate" placeholder="Enter your password" required>
      </div>
    </div>
    <input type="hidden" name="login" value="true">
    <button class="btn waves-effect waves-light" type="submit" name="action">Login
      <i class="material-icons right">send</i>
    </button>
    <?php
    if(isset($_GET['login']) && $_GET['login'] == 'failed') {
      echo 'Email does not exist or password is incorrect.';
    }
     ?>
  </form>
</div>
<script type="text/javascript"> getName(); </script>
