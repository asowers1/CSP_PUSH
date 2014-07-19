
<?php
include ('header.php');
//
?>

<!DOCTYPE html>

<!-- get data vars per username -->
<?php
$simple = 150;

$appId;
$appName;
$numberOfApps;
$numberOfUsers;
$currentCampaign;

$appArray = array();

function test_input($data)
{
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

?>

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
            <li class="active"><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="beacons.php"><i class="fa fa-bar-chart-o"></i> Beacons</a></li>
            <li><a href="campagins.php"><i class="fa fa-edit"></i> Neighborhood Campaigns</a></li>
            <li><a href="appManeger.php"><i class="fa fa-wrench"></i> App Content</a></li>

          </ul>

          <ul class="nav navbar-nav navbar-right navbar-user">
            <li class="dropdown user-dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $username;?><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#"><i class="fa fa-user"></i> Profile</a></li>
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
            <h1>Dashboard <small>Display Your Data</small></h1>
            <ol class="breadcrumb">
              <li><i class="fa fa-dashboard"></i><?php echo " ".$username."'s";?> Dashboard</li>
            </ol>
            <div class="alert alert-info alert-dismissable">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
              The Dashboard allows you to get realtime data analytics from your Beacons and neighborhood campaigns.
            </div>
          </div>
        </div><!-- /.row -->
        <div class="row">
        	<div class="col-lg-12">
        	<form action="index.php" method="post">
              <div class="form-group">
                <label>Select neighborhood Campaign</label>
                <select class="form-control" name="selection">
                <?php
                	// TODO: POST with new beacon campaign
                	for($i=0;$i<$numberOfApps;$i++){
                		$currentValue = $appArray[($i)][1];
	                	echo "<option value=".$currentValue.">".$currentValue."</option>";
                	}
                ?>
                </select>
              </div>
              <button type="submit" class="btn btn-default">Load neighborhood campaign</button>
        	</form>
        	</div>
        </div>
        <br>
        <?php
        echo '<pre>';
		echo 'Current Campaign: '.$currentCampaign;
        echo '</pre>';

	// get data from current campaign and populate javascript graphs

	
	//$csv->auto('../../../usrID/data/'.$IDDirectory.'/'.$currentCampaign.'/1/beacons.csv');


	?>
        <div class="row">
          <div class="col-lg-12">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-bar-chart-o"></i> Total Beacon Exposure</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-area"></div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->

        <div class="row">
          <div class="col-lg-4">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Donut Chart Example</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-donut"></div>
                <div class="text-right">
                  <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Line Graph Example with Tooltips</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-line"></div>
                <div class="text-right">
                  <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-4">
            <div class="panel panel-primary">
              <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-long-arrow-right"></i> Bar Graph Example</h3>
              </div>
              <div class="panel-body">
                <div id="morris-chart-bar"></div>
                <div class="text-right">
                  <a href="#">View Details <i class="fa fa-arrow-circle-right"></i></a>
                </div>
              </div>
            </div>
          </div>
        </div><!-- /.row -->


      </div><!-- /#page-wrapper -->

    </div><!-- /#wrapper -->

    <!-- data pass to charts -->
    <script type="text/javascript">
    var simple = <?php echo($simple);?>;
	</script>

    <!-- JavaScript -->
    <script src="js/jquery-1.10.2.js"></script>
    <script src="js/bootstrap.js"></script>

    <!-- Page Specific Plugins -->
    <script src="http://cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
    <script src="http://cdn.oesmith.co.uk/morris-0.4.3.min.js"></script>
    <script src="js/morris/chart-data-morris.js"></script>
    <script src="js/tablesorter/jquery.tablesorter.js"></script>
    <script src="js/tablesorter/tables.js"></script>
    <!--[if lte IE 8]><script src="js/excanvas.min.js"></script><![endif]-->
	<script src="js/flot/jquery.flot.js"></script>
	<script src="js/flot/jquery.flot.tooltip.min.js"></script>
	<script src="js/flot/jquery.flot.resize.js"></script>
	<script src="js/flot/jquery.flot.pie.js"></script>
	<script src="js/flot/chart-data-flot.js"></script>
	
	<!--PapaParse-->
	<script src="https://github.com/mholt/PapaParse/blob/master/papaparse.min.js"></script>

  </body>
</html>

