<?php

include ('config.php');
// Getting username and password from login form
$username = $_POST['username'];
$password = md5($_POST['password']);


// To protect MySQL injection
$username = stripslashes($username);
$password = stripslashes($password);
$username = mysql_real_escape_string($username);
$password = mysql_real_escape_string($password);

$sql="SELECT client_name FROM client WHERE client_name='$username' and password='$password'";
$result1=mysql_query($sql);
$user = mysql_fetch_row($result1);
$count=mysql_num_rows($result1);

if($count==1){
session_start();

$_SESSION["username"]= $user[0]; // storing username in session
header('location:column_admin/index.php');
}
//if the username and password doesn't match redirect to homepage with message=1
else {
    echo '
    <script language="javascript" type="text/javascript">
window.location.href="index.php?message=1";
</script>';

}
?>
