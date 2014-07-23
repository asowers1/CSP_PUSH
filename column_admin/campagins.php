
<?php
include ('header.php');
//$campagins = getAllCampaignsFromDB();
$listings  = getAllRealityFromDB();
	$register = $_GET['register'];
	
	if($register == 1 && !empty($_POST)) // Checks if the form is submitted or not
	{
	
	//retrieve all submitted data from the form
	$area = stripcslashes(strip_tags($_POST["area"]));
	$address = stripcslashes(strip_tags($_POST["address"]));
	$rent = stripcslashes(strip_tags($_POST["rent"]));
	$beds = stripcslashes(strip_tags($_POST["beds"]));
	$baths= stripcslashes(strip_tags($_POST["baths"]));
	$availible = stripcslashes(strip_tags($_POST["available"]));
	$pets = stripcslashes(strip_tags($_POST["pets"]));
	$parking = stripcslashes(strip_tags($_POST["parking"]));
	$laundry = stripcslashes(strip_tags($_POST["laundry"]));
	$utilities = stripcslashes(strip_tags($_POST["utilities"]));
	$furnished = stripcslashes(strip_tags($_POST["furnished"]));
	$description = stripcslashes(strip_tags($_POST["description"]));
	$beaconIdentifier = stripcslashes(strip_tags($_POST["beaconIdentifier"]));
	$startTime = stripcslashes(strip_tags($_POST["startTime"]));
	$endTime = stripcslashes(strip_tags($_POST["endTime"]));
	$sql1="SELECT beacon_id FROM beacon WHERE identifier=".$identifier.""; // checking if beacon exists
	$qry1=mysql_query($sql1);
	$num_rows = mysql_num_rows($qry1);
	//if it already exists
	if($num_rows > 0)
	{
	
		// return to page after business logic
		$register=0;
		Header('Location: '.$_SERVER['PHP_SELF']);
		Exit(); //optional
	}
	else
	{
		echo '<center>
		<div class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>This Beacon Identifier does not exists in the database!</strong> please use another known identifier or contact andrew@experiencepush.com for assistance.
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
            <li><a href="beacons.php"><i class="fa fa-bar-chart-o"></i> Beacons</a></li>
			<li class="active"><a href="campagins.php"><i class="fa fa-edit"></i> Neighborhood Campaigns</a></li>

          </ul>

          <ul class="nav navbar-nav navbar-right navbar-user">
            <li class="dropdown user-dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $username;?><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#"><i class="fa fa-gear"></i> Settings</a></li>
                <li class="divider"></li>
                <li><a href="../logout.php"><i class="fa fa-power-off"></i> Log Out</a></li>
              </ul>
            </li>
          </ul>
        </div><!-- /.navbar-collapse -->
      </nav>
      <div id="page-wrapper">
        <div class="row">
          <div class="col-lg-12">
            <h1>Neighborhood Campaigns <small>- Link your neighborhood campaigns and listings iBeacons</small></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-bar-chart-o"></i> <?php echo $username;?>'s Neighborhood Campaigns</li>
            </ol>
          </div>
        </div><!-- /.row -->
		<!--<pre><?php print_r(getAllBeaconsFromDB()); ?></pre>-->
		
		<div class="col-lg-12">
			<h1>Add new campaign</h1>
			<form action="campaigns.php?register=1" method="POST" name="myForm" onsubmit="return(validate());">
              <div class="form-group input-group">
                <span class="input-group-addon">Identifier</span>
                <input type="text" name="identifier" class="form-control" placeholder="e.g. South Danby front door">
              </div>
              <div class="form-group input-group">
                <span class="input-group-addon">UUID</span>
                <input type="text" name="uuid" class="form-control" placeholder="e.g. EE1A782C-9CD0-470C-88C4-BD52704B7A9A">
              </div>
              <div class="form-group input-group">
                <span class="input-group-addon">Major</span>
                <input type="text" name="major" class="form-control" placeholder="e.g. 1">
              </div>
              <div class="form-group input-group">
                <span class="input-group-addon">Minor</span>
                <input type="text" name="minor" class="form-control" placeholder="e.g. 1">
              </div>
			<button type="submit" class="btn btn-default">Submit Beacon</button>
            </form>

		</div>
		
		
        <div class="col-lg-12">
            <h1>Registered Listings</h1>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                  <tr>
                  	<th>CSP Beacon Identifier<i class="fa fa-sort"></i></th>
                    <th>Address</th>
                    <!--<th>Delete <i class="fa fa-short"></i></th>-->
                  </tr>
                </thead>
                <tbody>
                <?php
                // print relivan campaign data into the table
                for($i=0;$i<count($listings);$i++){
                	$index = $listings[$i];
	                echo '
	                  <tr>
	                  	<td>'.getBeaconIdentFromCampaignID($index["campaign_id"]).'</td>
	                    <td>'.$index["address"].'</td>
	                    
	                  </tr> ';
	            }
                ?>
                </tbody>
              </table>
            </div>
          </div>
	    </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
    </div><!-- /#wrapper -->

    <!-- JavaScript -->
    <script type="text/javascript">
		function validate()
		{

		}
	</script>
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>

    <!-- Page Specific Plugins -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
    <script src="js/morris/chart-data-morris.js"></script>
    <script src="js/tablesorter/jquery.tablesorter.js"></script>
    <script src="js/tablesorter/tables.js"></script>

  </body>
</html>

