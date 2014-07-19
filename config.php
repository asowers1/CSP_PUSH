<?php
//Connection to the MySQL Server by Andrew Sowers, for Push Interactive LLC
define('DB_SERVER', 'localhost'); // Mysql hostname
define('DB_USERNAME', 'root'); // Mysql username
define('DB_PASSWORD', 'titan'); // Mysql password
define('DB_DATABASE', 'push_interactive'); // Mysql database name
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());

function getAllBeaconsFromDB(){
	$result = mysql_query("SELECT * FROM beacon");
	while( $row = mysql_fetch_assoc( $result)){
    	$new_array[] = $row; // Inside while loop
	}
	return $new_array;
}

function getAllBeaconsExceptNull(){
	$result = mysql_query("SELECT * FROM beacon where beacon_id != 0");
	while($row = mysql_fetch_assoc($result)){
		$new_array[] = $row;
	}
	return $new_array;
}

function getBeaconIdentFromCampaignID($campaignID){
	$campaignID = stripcslashes(strip_tags($campaignID));
	$result = mysql_query("select identifier from beacon where beacon_id = (select beacon_id from campaign where campaign_id = '$campaignID')");
	return mysql_result($result, 0);
}

function getAllRealityFromDB(){
	$result = mysql_query("SELECT * FROM reality");
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[] = $row;
	}
	return $new_array;
}

function getAllCampaignsFromDB(){
	$result = mysql_query("SELECT * FROM campaign");
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[] = $row;
	}
	return $new_array;
}

function addNewBeaconToDB($identifier,$uuid,$major,$minor){
	$identifier = stripcslashes(strip_tags($identifier));
	$uuid  = stripcslashes(strip_tags($uuid));
	$major = stripcslashes(strip_tags($major));
	$minor = stripcslashes(strip_tags($minor));
	$result = mysql_query("INSERT INTO beacon (beacon_id,identifier,uuid,major,minor) VALUES(DEFAULT,'$identifier','$uuid',".$major.",".$minor.")");
	
}
?>

