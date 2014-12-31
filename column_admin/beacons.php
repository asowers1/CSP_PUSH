<?php
include ('header.php');

// gets all beacons for viewing later in the table; excludes the null beacon marker (beacon_id 0)
  $beacons = getAllBeaconsExceptNull();
	$register = '';
  if(isset($_GET['register'])){
    $register = $_GET['register'];
  }
	if($register == '1' && !empty($_POST)) // Checks if the form is submitted or not
	{
	
	//retrieve all submitted data from the form
	$identifier = $_POST["identifier"];
  $beaconID = $_POST["beaconID"];

	// checking beacon exists
	$qry1=mysql_query("SELECT * FROM beacon WHERE beacon_id = '".$beaconID."'");
	$num_rows = mysql_num_rows($qry1);
	
	//alert if it already exists
	if($num_rows == 1)
	{
    registerBeaconFromDB($beaconID,$identifier);
    $register=0;
    Header('Location: '.$_SERVER['PHP_SELF']);
    Exit(); //optional
	}
	else
	{

    echo '<center>
    <div class="alert">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <strong>This Beacon or identifier already exists in the database!</strong> please use another or contact andrew@experiencepush.com for assistance.
    </div>
    </center>
    ';
	}
	}

?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Dashboard - SB Admin</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">

    <!-- Add custom CSS here -->
    <link href="css/sb-admin.css" rel="stylesheet">
    <link rel="stylesheet" href="font-awesome/css/font-awesome.min.css">
    <!-- Page Specific CSS -->
    <link rel="stylesheet" href="http://cdn.oesmith.co.uk/morris-0.4.3.min.css">
  </head>
  <body>
    <div id="wrapper">

      <!-- Sidebar -->
      <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="index.php"><img src="/csp_portal/column_admin/img/logoSmall.png"> management Portal</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav side-nav">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li class="active"><a href="beacons.php"><i class="fa fa-bullseye"></i> Beacons</a></li>
			<li><a href="campagins.php"><i class="fa fa-building"></i> Neighborhood Campaigns</a></li>

          </ul>

        <ul class="nav navbar-nav navbar-right navbar-user">
          <li class="dropdown user-dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $username;?><b class="caret"></b></a>
            <ul class="dropdown-menu">
               <li><a href="../settings.php"><i class="fa fa-gear"></i> Settings</a></li>
               <li class="divider"></li>
               <li><a href="../logout.php"><i class="fa fa-power-off"></i> Log Out</a></li>
            </ul>
          </li>
        </ul>        </div><!-- /.navbar-collapse -->
      </nav>

      <div id="page-wrapper">

        <div class="row">
          <div class="col-lg-12">
            <h1>Beacons <small>Administrate and deploy your beacons</small></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa fa-bullseye"></i> <?php echo $username;?>'s Beacons</li>
            </ol>
            
          </div>
        </div><!-- /.row -->
		<!--<pre><?php print_r(getAllBeaconsFromDB()); ?></pre>-->
        <div class="col-lg-6">
            <h1>Deployed beacons</h1>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                  <tr>
                    <th>Beacon ID <i class="fa fa-sort"></i></th>
                    <th>Identifier <i class="fa fa-sort"></i></th>
                    <!--<th>Delete <i class="fa fa-short"></i></th>-->
                  </tr>
                </thead>
                <tbody>
                <?php
                
                // print all beacons into the table
                for($i=0;$i<count($beacons);$i++){
                	$index = $beacons[$i];
	                echo '
	                  <TR>
	                    <TD>'.$index["identifier"].'</TD>
	                    <TD>'.$index["beacon_id"].'</TD>
	                  </TR> ';
	            }
                ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-lg-6">
            <h1>Add or modify Beacon</h1>
            <form action="beacons.php?register=1" method="POST" name="myForm" onSubmit="return validate(this); return false;">
              <div class="form-group input-group">
                <span class="input-group-addon">Identifier</span>
                <input type="text" name="identifier" class="form-control" placeholder="e.g. South Danby front door">
              </div>
              <div class="form-group input-group">
                <span class="input-group-addon">Beacon ID</span>
                <input type="text" name="beaconID" class="form-control" placeholder="e.g. 7A9A">
              </div>
			       <button type="submit" class="btn btn-default">Submit Beacon</button>
            </form>
          </div>
	    </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
    </div><!-- /#wrapper -->
    <!-- JavaScript -->
    <script type="text/javascript">
		function validate(f)
		{
			if( document.myForm.identifier.value == "")
			{
				alert( "Please provide an identifier");
				document.myForm.identifier.focus();
				return false;
			}

      else if( document.myForm.beaconID.value == "")
      {
        alert("Please provide a beacon ID");
        document.myForm.beaconID.focus();
        return false;
      }else{
            f.submit();
          return true;
        }
		}
  </script>
  <script src="js/jquery-1.10.2.js"></script>
  <script src="js/bootstrap.js"></script>
  <!-- Page Specific Plugins -->
  <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
  <script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
  </body>
</html>

