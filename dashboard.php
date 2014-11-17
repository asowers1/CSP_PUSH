<?php
//place this code on top of all the pages which you need to authenticate
//--- Authenticate code begins here ---
session_start();
//checks if the login session is true
if($_SESSION["username"]==NULL){
	header("location:index.php");
}
$username = $_SESSION["username"];
// --- Authenticate code ends here ---

?>
<?php include ('header.php'); ?>

<script>
$(function(){
        $('div.product-chooser').not('.disabled').find('div.product-chooser-item').on('click', function(){
                $(this).parent().parent().find('div.product-chooser-item').removeClass('selected');
                $(this).addClass('selected');
                $(this).find('input[type="radio"]').prop("checked", true);

        });
});
</script>

<br/>
 <div style="float:right"> <a class="btn btn-info" href="settings.php" > Account </a>  <a class="btn btn-danger logout" href="logout.php" > Logout</a> </div>
 <fieldset>
    <legend style="color:#fff;">Welcome <?php echo $username; ?>, </legend>

	<br/>
	<br/>
 </fieldset>
 <script>
 $('.logout').click(function(){
    return confirm("Are you sure you want to Logout?");
})
</script>
<?php include ('footer.php'); ?>
