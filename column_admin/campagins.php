
<?php
include ('header.php');
include ('../rest/listing_crud.php');
//$campagins = getAllCampaignsFromDB();
	$register = '';
  if(isset($_GET['register'])){
    $register = $_GET['register'];
  }
	//$completeurl = $_SERVER['DOCUMENT_ROOT'].'/csp_portal/rest/listing.xml';
	$url = "https://cspmgmt.managebuilding.com/Resident/PublicPages/XMLRentals.ashx?listings=all";
  $xml = simplexml_load_file($url);
	$xml->asXml("/usr/share/nginx/html/csp_portal/rest/listing.xml");
	$beacons = getAllBeaconsExceptNull();
	$listings = json_decode(file_get_contents('http://experiencepush.com/csp_portal/rest/index.php?PUSH_ID=123&call=getAllListings'), true);	$units = array();
	$count = count($listings);
	$beacon_count = count($beacons);
	for($i=0;$i<$count;$i++){
		$units[$i]=$listings[$i]["unitID"];
	}
	sort($units);
	if($register == 1 && !empty($_POST)){ // Checks if the form is submitted or not
		if(isset($_POST["campaign_name"])&&isset($_POST["beacon_id"])&&isset($_POST["unit_id"])){
			//retrieve all submitted data from the form
			$campaign_name = $_POST["campaign_name"];
			$beacon_id = $_POST["beacon_id"];
			$unit_id = $_POST["unit_id"];
			
			if(setupCampaignWithBeacon($campaign_name,$unit_id,$beacon_id)){
					$register=0;
					Header('Location: '.$_SERVER['PHP_SELF']);
					Exit(); //optional
			}
			else{
				echo '<center>
				<div class="alert">
				  <button type="button" class="close" data-dismiss="alert">&times;</button>
				  <strong>Failed</strong> please contact andrew@experiencepush.com for assistance.
				</div>
				</center>
				';
			}
		}
	}else if($register==1 && $_GET["unlink"]=='true'&&isset($_GET["campaign_name"])){
		
		demolishCampaign($_GET["campaign_name"]);

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
          <a class="navbar-brand" href="index.php"><img src="/csp_portal/column_admin/img/logoSmall.png"> management portal</a>
        </div>
        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse navbar-ex1-collapse">
          <ul class="nav navbar-nav side-nav">
            <li><a href="index.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="beacons.php"><i class="fa fa-bullseye"></i> Beacons</a></li>
			<li class="active"><a href="campagins.php"><i class="fa fa-building-o"></i> Neighborhood Campaigns</a></li>

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
            <h1>Neighborhood Campaigns <small>- Link your neighborhood listings to iBeacons</small></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa  fa-flag"></i> <?php echo $username;?>'s Neighborhood Campaigns:</li>
            </ol>
          </div>
        </div><!-- /.row -->
		<!--<pre><?php print_r(getAllBeaconsFromDB()); ?></pre>-->
		
		<div class="col-lg-6">
			<h1 class="fa fa-bullseye"> Assign beacon to listing</h1>
			<form action="campagins.php?register=1" method="post" name="myForm" onSubmit="return validate(this); return false;">
              <div class="form-group input-group">
                <span class="input-group-addon">Campaign Name</span>
                <input type="text" name="campaign_name" class="form-control" placeholder="e.g. South Danby front door">
              </div>
              <div class="form-group">
                <label>Beacon ID</label>
                <select class="form-control" name="beacon_id">
				          <?php for($j=0;$j<$beacon_count;$j++)echo '<option>'.$beacons[$j]["beacon_id"].': '.$beacons[$j]["identifier"].'</option>'?>
				        </select>
              </div>
              <div class="form-group">
                <label>Unit ID</label>
                <select class="form-control" name="unit_id">
				          <?php for($j=0;$j<$count;$j++)echo '<option>'.$units[$j].'</option>'?>
				        </select>
              </div>
			       <button type="submit" class="btn btn-default">Link Beacon To Unit</button>
            </form>
		    </div>		
        <div class="col-lg-12">
            <h1 class="fa fa-building-o"> Registered Listings</h1>
            <div class="table-responsive">
              <table class="table table-hover table-striped tablesorter">
                <thead>
                  <tr>
                  	<th>Campaign<i class="fa fa-sort"></i></th>
                  	<th>Unit ID <i class="fa fa-sort"></th>
                    <th>Address <i class="fa fa-sort"></th>
                    <th>Property Image</th>
                    <!--<th>Delete <i class="fa fa-short"></i></th>-->
                  </tr>
                </thead>
                <tbody>
                <?php
                $count = count($listings);
                for($i=0;$i<$count;$i++){
                	$index = $listings[$i];
                	$var = getCampaignItem($index["unitID"]);
                	$test = '';
                	if($var[0]==NULL||$var[1]==NULL){
                		$test = '<p class="label label-info">not assigned</p>';
                	}
                	else{
                		$_POST['unlink'] = 'true';
                		$test = '<p class="text-info" >Campaign name: '.$var[0].'</p><p class="text-info">Beacon Identifier: '.$var[1].'</p><br>
               	        <form action="campagins.php?register=1&unlink=true&campaign_name='.$var[0].'" method="POST" name="unlink">
						<button type="submit" class="btn btn-danger">Unlink</button>
			            </form>
			            <?php';				
                	}
					         echo '
		                 <tr>
		                   <td>'.$test.'</td>
		                   <td>'.$index["unitID"].'</td>
		                   <td>'.$index["address"].'</td>
		                   <td><img src="'.$index["listingsImage"][0].'" width=256></td>
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
    <script>
		function validate(f)
		{
			if(f.elements['campaign_name'].value==""){
				alert("Please provide a campaign name for this beacon listings pair.");
				f.elements['campaign_name'].focus()
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
    <script src="js/morris/chart-data-morris.js"></script>
    <script src="js/tablesorter/jquery.tablesorter.js"></script>
    <script src="js/tablesorter/tables.js"></script>

  </body>
</html>

