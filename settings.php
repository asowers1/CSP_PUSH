<?php
//place this code in the pages, which you need to authenticate
session_start();
//checks if the login session is true
if($_SESSION["username"]==NULL){
        header("location:index.php");
}
$username = $_SESSION["username"];
// --- Authenticate code ends here ---


include ('header.php');  
?>

<br/>

 <div style="float:right"> <a class="btn btn-info" href="column_admin/dashboard.php" >Back to Dashboard</a>  <a class="btn btn-danger logout" href="logout.php" >Logout</a> </div>

 <fieldset>
    <legend>Welcome <?php echo $username; ?>, </legend>

	<br/>
	<br/>
	<div style="float:left"> <a class="btn btn-info" href="password.php" >Update password</a> 

  </fieldset>
</form>
</fieldset>


 <!--
 Similarly you can also add password change field, I suggest to create separate form for this, 
 just make sure your encrypt the password using md5 before you save to database.

 -->
 <script>

 function validate()
{
   if( document.myForm.full_name.value == "" )
   {
     alert( "Please provide your full name!" );
     document.myForm.full_name.focus() ;
     return false;
   }
   return( true );
}


 $('.logout').click(function(){
    return confirm("Are you sure you want to Logout?");
})
</script>
<?php include ('footer.php'); ?>
