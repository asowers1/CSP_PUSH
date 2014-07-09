<?php
/*
*  Author: Andrew Sowers, Push Interactive
*  May - June 2014
*/

include ('header.php');
session_start();
//checks if the login session is true
if($_SESSION["username"]!=NULL){
	header("location:column_admin/index.php");
}
?>

      <div class="masthead">

      <h3 class="muted" style="color:#fff;">Let's get to work</h3>
      </div>
      <hr>
      <div style="background-color: rgba(255,255,255,0.9);" class="jumbotron">
      <!--background-color:#295174;-->
	<div id="box" style="">
		<center>
		<img src="../img/push.png" alt="Push Interactive" style="height: 256px; width: 256px;">
		</center>
        </div>
	<br/>

		<?php $message = $_GET['message'];

		//Alert messages based on integers
		if($message == 1) {
		echo '
		<div class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>Invalid username or password</strong>
		</div>
		';
		}

		else if($message == 2) {
		echo '
		<div class="alert alert-success">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>You have successfully logged out! </strong>
		</div>
		';
		}

		?>

		<form action="auth_check.php" method="post" class="form-signin">
        <h2 class="form-signin-heading">Management Portal sign in</h2>
        <input name="username" type="text" class="input-block-level" placeholder="Username">
        <input name="password" type="password" class="input-block-level" placeholder="Password">
        <button class="btn btn-large btn-primary" type="submit">Sign in</button>
      </form>

        <a class="btn btn-large btn-success" href="register.php">Register New User</a>
      </div>


<?php include ('footer.php'); ?>
