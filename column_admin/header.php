<?php 
/*
Managment_portal header.php
Push Interactive LLC
Author: Andrew Sowers
*/

// /var/usrID/data/5390febe966ad/CSP/1/info.csv

//place this code on top of all the pages which you need to authenticate
//--- Authenticate code begins here ---

include ('../config.php');

session_start();
//checks if the login session is true
if($_SESSION["username"]==NULL){
        header("location:../index.php");
}
$username = $_SESSION["username"];
// --- Authenticate code ends here ---

//$document_get = mysql_query("SELECT * FROM client WHERE username='$username'");
//$match_value = mysql_fetch_array($document_get);

$result = mysql_query("SELECT client_id FROM client where client_name = '$username'");
if (!$result) {
    die('Could not query:' . mysql_error());
}
$id = mysql_result($result, 0);

/*
$result = mysql_query("SELECT directory FROM IDDirectory where id = '$id'");
if (!$result){
	die('Could not query:' . mysql_error());
}
$IDDirectory = mysql_result($result,0);
*/

?>

<!DOCTYPE html>

<html lang="en">

  <head>
    <meta charset="utf-8">
    <title>Experience: Push Interactive, iBeacon Management Portal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <link href="//netdna.bootstrapcdn.com/font-awesome/4.1.0/css/font-awesome.min.css" rel="stylesheet">
    <!--<link href="../css/stylish-portfolio.css" rel="stylesheet">-->
    <link href="bootstrap/css/bootstrap.css" rel="stylesheet">
    <script src="http://code.jquery.com/jquery.js"></script>
    <script src="bootstrap/js/bootstrap.min.js"></script>
    <style type="text/css">
	/* Custom styles for this project  */
      body {
      	background-color:#fff;
        padding-top: 20px;
        padding-bottom: 40px;
      }

      /* Custom container */
      .container-narrow {
        margin: 0 auto;
        max-width: 95%;
      }
      .container-narrow > hr {
        margin: 30px 0;
      }

      /* Main marketing message and sign up button */
      .jumbotron {
        margin: 60px 0;
        text-align: center;
      }
      .jumbotron h1 {
        font-size: 72px;
        line-height: 1;
      }
      .jumbotron .btn {
        font-size: 21px;
        padding: 14px 24px;
      }

      /* Supporting marketing content */
      .marketing {
        margin: 60px 0;
      }

      .marketing p + h4 {
        margin-top: 28px;
      }

	   .form-signin {
        max-width: 300px;
        padding: 19px 29px 29px;
        margin: 0 auto 20px;
        background-color: #fff;
        border: 1px solid #e5e5e5;
        -webkit-border-radius: 5px;
           -moz-border-radius: 5px;
                border-radius: 5px;
        -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.05);
           -moz-box-shadow: 0 1px 2px rgba(0,0,0,.05);
                box-shadow: 0 1px 2px rgba(0,0,0,.05);
      }
      .form-signin .form-signin-heading,
      .form-signin .checkbox {
        margin-bottom: 10px;
      }
      .form-signin input[type="text"],
      .form-signin input[type="password"] {
        font-size: 16px;
        height: auto;
        margin-bottom: 15px;
        padding: 7px 9px;
      }

    </style>
  </head>


