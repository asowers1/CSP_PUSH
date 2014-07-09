<?php include ('header.php'); ?>
<?php

	//Gets the form submitted data 
	
	/*
	function setupIDDirectory($userid) /*** DEPRICATED ***
	{
		$con1=mysqli_connect("127.0.0.1","root","titan","pool");
		// Check connection
		if (mysqli_connect_errno())
		{
			// -1 : failure state
			echo "-1" . mysqli_connect_error();
		}
		else
		{
			// a unique ID is associated with a incrementing primary key ala MySQL
			$data = uniqid ();
			$file = $data;	// it be a file, yo
			$file_location = "../../usrID/data/"."$file";
			// prepare yourself
			$stmt1 = mysqli_prepare($con1,"INSERT INTO IDDirectory(id, directory) VALUES (?,?)");
			mysqli_stmt_bind_param($stmt1,is,$userid,$data);
			mysqli_stmt_execute($stmt1);
			$oldmask = umask(0);
			mkdir($file_location, 0777);
			umask($oldmask);
			
			//$fileStr = "../../usrID/data/".$file;
			//$filePointer = fopen($fileStr,'w+');
			//fwrite($filePointer,"");
			//fclose($filePointer);
			
	
			mysqli_close ($con1);
		}
	}
	*/
	$register = $_GET['register'];
	
	if($register == 1 && !empty($_POST)) // Checks if the form is submitted or not
	{
	
	//retrieve all submitted data from the form
	
	$username = $_POST['username'];
	$username = strip_tags($username); //strip tags are used to take plain text only, in case the register-er inserts dangours scripts.
	$username = str_replace(' ', '', $username); // to remove blank spaces
	
	$password = $_POST['password'];
	$password = strip_tags($password);
	$password = md5($password); // md5 is used to encrypt your password to make it more secure.
	
	$email = $_POST['email'];
	$email = strip_tags($email);
	
	$firstName = $_POST['firstName'];
	$firstName = strip_tags($firstName);
	
	$lastName = $_POST['lastName'];
	$lastName = strip_tags($lastName);
	
	$address1 = $_POST['address1'];
	$address1 = strip_tags($address1);
	
	$address2 = $_POST['address2'];
	$address2 = strip_tags($address2);
	
	$city = $_POST['city'];
	$city = strip_tags($city);
	
	$state = $_POST['state'];
	$state = strip_tags($state);
	
	$zipcode = $_POST['zipcode'];
	$zipcode = strip_tags($zipcode);
	
	$appKey = $_POST['appKey'];
	$appKey = strip_tags($appKey);
	$appKey = md5($appKey);
	
	$sql1="SELECT id FROM client WHERE username='$username'"; // checking username already exists
	$qry1=mysql_query($sql1);
	$sql2="SELECT id FROM client WHERE email='$email'"; // checking email already exists
	$qry2=mysql_query($sql2);
	
	$num_rows = mysql_num_rows($qry1);
	$num_rows = num_rows+ mysql_num_rows($qry2);
	
	$sql3="SELECT * FROM push_app WHERE push_app_key='$appKey' and id_push_app=1";
	$qry3=mysql_query($sql3);
	$num_rows2 = mysql_num_rows($qry3);
	
	//alert if it already exists
	if($num_rows > 0)
	{
		echo '
		<div class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>username or email already exists in our database!</strong> please use another or contact andrew@experiencepush.com
		</div>
		';
	}
	else if($num_rows2 != 1){
		echo '
		<div class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>pushKey does not match your record in our database!</strong> please use the correct key or contact andrew@experiencepush.com for assistance
		</div>
		';
		
	}
	else
	{
		// if username doesn't exist insert new records to push_interactive database for CSP management
		$success = mysql_query("INSERT INTO client(client_id, client_name, password, email, first_name, last_name, address1, address2, city, state, zipcode) VALUES (DEFAULT,'".$username."', '".$password."', '".$email."', '".$firstName."', '".$lastName."', '".$address1."', '".$address2."', '".$city."', '".$state."', '".$zipcode."')");
	
	 	//messages if the new record is inserted or not
	 	if($success) {
	
	
		// we need the id for linking to users data directory /*** DEPRICATED ***
		//$getNewId=mysql_query("SELECT id FROM client WHERE username='$username'"); 
		//$result1=mysql_result($getNewId, 0);
		//$idInt = intval($result1);
		//setupIDDirectory($idInt);
	
		echo '
			<div class="alert alert-success">
			Registration Successful! please login to your account
			</div>
			';
		}
		
		else {
			echo '
				<div class="alert">
				<strong>failed</strong>
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				</div>
			';
		}
	}
}
?>

<br/>
   <div style="float:right; "> <a class="btn" href="index.php" > <i class="icon-home icon-black"></i> Home </a>  </div>
   <br/>
<?php
//hiding form once the registration is successful
 if(!$success) {
 ?>
<form action="register.php?register=1" method="post" name="myForm" onsubmit="return(validate());">
	<fieldset>
		<legend>Sign Up Form</legend>
		<label>Username *</label>
		<br/>
		<input name="username" type="text" placeholder="your login username">
		<br/>
		<label>Password *</label>
		<br/>
		<input name="password" type="password" placeholder="Login password">
		<br/>
		<input name="passwordTwo" type="password" placeholder="duplicate check">
		<br/>
		<label>Email *</label>
		<br/>
		<input name="email" type="text" placeholder="Incase we need to reset your password">
		<br/>
		<input name="emailTwo" type="text" placeholder="duplicate check">
		<br/>
		<label>First Name *</label>
		<br/>
		<input name="firstName" type="text" placeholder="">
		<br/>
		<label>Last Name *</label>
		<br/>
		<input name="lastName" type="text" placeholder="">
		<br/>
		<label>Address1 *</label>
		<br/>
		<input name="address1" type="text" placeholder="">
		<br/>
		<label>Address2</label>
		<br/>
		<input name="address2" type="text" placeholder="">
		<br/>
		<label>City *</label>
		<br/>
		<input name="city" type="text" placeholder="your current city">
		<br/>
		<label>State *</label>
		<br/>
		<input name="state" type="text" placeholder="your current state">
		<br/>
		<label>Zip code *</label>
		<br/>
		<input name="zipcode" type="text" placeholder="your postal code">
		</br>
		</br>
		<label>Push Application Key *</label>
		</br>
		<input name="pushKey" type="text" placeholder="Your Push App key">
		</br>
		

  <!-- Ready made validation script, if you want any mandatory fields  (optional) -->
 
<script type="text/javascript">

function validate()
{
	if(document.myForm.pushKey.value==""){
		alert("Please provide your Push App Key!");
		document.myFrom.pushKey.focus();
		return false;
	}
   if( document.myForm.username.value == "" )
   {
     alert( "Please provide your username!" );
     document.myForm.username.focus() ;
     return false;
   }
	if( document.myForm.username.value == "")
	{
		alert( "Please provide your email address!");
		document.myForm.email.focus();
		return false;
	}

   if( document.myForm.password.value == "" )
   {
     alert( "Please provide your password!" );
     document.myForm.password.focus() ;
     return false;
   }

   if( document.myForm.full_name.value == "" )
   {
     alert( "Please provide your full name!" );
     document.myForm.full_name.focus() ;
     return false;
   } 

   if( document.myForm.passwordTwo.value != document.myForm.password.value)
   {
     alert( "Please provide matching passwords!" );
     document.myForm.password.focus();
     return false;
   }
   
   if (document.myForm.emailTwo.value != document.myForm.email.value)
   {
	   alert( "Please provide matching email accounts!");
	   document.myForm.email.focus();
	   return false;
   }
   
   if (document.myForm.address1.vaue == "")
   {
	   alert( "Please provide at least 1 address")
	   document.myForm.address1.focus();
	   return false;
   }

   return( true );
}
</script>

	<br/>
    <button type="submit" class="btn">Signup</button>
  </fieldset>
</form>
<?php } ?>















<?php include ('footer.php'); ?> 
