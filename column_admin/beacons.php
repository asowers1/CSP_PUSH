
<?php
include ('header.php');
$beacons = getAllBeaconsFromDB();
	$register = $_GET['register'];
	
	if($register == 1 && !empty($_POST)) // Checks if the form is submitted or not
	{
	
	//retrieve all submitted data from the form
	$identifier = $_POST["identifier"];
	$uuid = $_POST["uuid"];
	$major = $_POST["major"];
	$minor = $_POST["minor"];
	
	
	$sql1="SELECT * FROM beacon WHERE uuid='$uuid' and major=".$major." and minor=".$minor." or identifier=".$identifier.""; // checking beacon already exists
	$qry1=mysql_query($sql1);

	
	$num_rows = mysql_num_rows($qry1);
	
	
	//alert if it already exists
	if($num_rows > 0)
	{
		echo '<center>
		<div class="alert">
		  <button type="button" class="close" data-dismiss="alert">&times;</button>
		  <strong>This Beacon or identifier already exists in the database!</strong> please use another or contact andrew@experiencepush.com for assistance.
		</div>
		</center>
		';
	}
	else
	{
		addNewBeaconToDB($identifier,$uuid,$major,$minor);
		$register=0;
		Header('Location: '.$_SERVER['PHP_SELF']);
		Exit(); //optional
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
            <li class="active"><a href="beacons.php"><i class="fa fa-bar-chart-o"></i> Beacons</a></li>
			<li><a href="campagins.php"><i class="fa fa-edit"></i> Neighborhood Campaigns</a></li>
            <li><a href="appManeger.php"><i class="fa fa-wrench"></i> App Content</a></li>

          </ul>

          <ul class="nav navbar-nav navbar-right navbar-user">
            <li class="dropdown messages-dropdown">
              <ul class="dropdown-menu">
                <li class="dropdown-header">0 New Messages</li>
                <li class="message-preview">
                  <a href="#">
                    <span class="avatar"><img src="http://placehold.it/50x50"></span>
                    <span class="name"><?php echo $username;?></span>
                    <span class="message">Hey there, I wanted to ask you something...</span>
                    <span class="time"><i class="fa fa-clock-o"></i> 4:34 PM</span>
                  </a>
                </li>
                <li class="divider"></li>
                <li class="message-preview">
                  <a href="#">
                    <span class="avatar"><img src="http://placehold.it/50x50"></span>
                    <span class="name">John Smith:</span>
                    <span class="message">Hey there, I wanted to ask you something...</span>
                    <span class="time"><i class="fa fa-clock-o"></i> 4:34 PM</span>
                  </a>
                </li>
                <li class="divider"></li>
                <li class="message-preview">
                  <a href="#">
                    <span class="avatar"><img src="http://placehold.it/50x50"></span>
                    <span class="name">John Smith:</span>
                    <span class="message">Hey there, I wanted to ask you something...</span>
                    <span class="time"><i class="fa fa-clock-o"></i> 4:34 PM</span>
                  </a>
                </li>
                <li class="divider"></li>
                <li><a href="#">View Inbox <span class="badge">0</span></a></li>
              </ul>
            </li>
            <li class="dropdown user-dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="fa fa-user"></i> <?php echo $username;?><b class="caret"></b></a>
              <ul class="dropdown-menu">
                <li><a href="#"><i class="fa fa-user"></i> Profile</a></li>
                <li><a href="#"><i class="fa fa-envelope"></i> Inbox <span class="badge">0</span></a></li>
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
            <h1>Beacons <small>Administrate and deploy your beacons</small></h1>
            <ol class="breadcrumb">
              <li class="active"><i class="fa fa-bar-chart-o"></i> <?php echo $username;?>'s Beacons</li>
            </ol>
            
          </div>
        </div><!-- /.row -->
		<!--<pre><?php print_r(getAllBeaconsFromDB()); ?></pre>-->
        <div class="col-lg-8">
            <h1>Deployed beacons</h1>
            <div class="table-responsive">
              <table class="table table-bordered table-hover table-striped tablesorter">
                <thead>
                  <tr>
                    <th>CSP Beacon ID <i class="fa fa-sort"></i></th>
                    <th>CSP Identifier <i class="fa fa-sort"></i></th>
                    <th>Universally unique identifier (UUID) <i class="fa fa-sort"></i></th>
                    <th>Major <i class="fa fa-sort"></i></th>
                    <th>Minor <i class="fa fa-sort"></i></th>
                    <!--<th>Delete <i class="fa fa-short"></i></th>-->
                  </tr>
                </thead>
                <tbody>
                <?php
                
                // print all beacons into the table
                for($i=0;$i<count($beacons);$i++){
                	$index = $beacons[$i];
	                echo '
	                  <tr>
	                    <td>'.$index["beacon_id"].'</td>
	                    <td>'.$index["identifier"].'</td>
	                    <td>'.$index["uuid"].'</td>
	                    <td>'.$index["major"].'</td>
	                    <td>'.$index["minor"].'</td>
	                  </tr> ';
	            }
                ?>
                </tbody>
              </table>
            </div>
          </div>
          <div class="col-lg-4">
            <h1>Add new Beacon</h1>
            <form action="beacons.php?register=1" method="POST" name="myForm" onsubmit="return(validate());">
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
	    </div><!-- /.row -->
      </div><!-- /#page-wrapper -->
    </div><!-- /#wrapper -->

    <!-- JavaScript -->
    <script type="text/javascript">
		function validate()
		{
			if( document.myForm.identifier.value == "")
			{
				alert( "Please provide an identifier");
				document.myForm.identifier.focus();
				return false;
			}
		
			if( document.myForm.uuid.value == "")
			{
				alert( "Please provide correct UUID");
				document.myForm.uuid.focus();
				return false;
			}
		
		   if( document.myForm.major.value == "" )
		   {
		     alert( "Please provide correct Major ID" );
		     document.myForm.major.focus() ;
		     return false;
		   }
		
		   if( document.myForm.minor.value == "" )
		   {
		     alert( "Please provide correct Minor ID" );
		     document.myForm.minor.focus() ;
		     return false;
		   } 
		
		   return( true );
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

