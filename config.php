<?hh
/*
*	config.php - My CSP Management portal business logic
*
*	Andrew Sowers - Push Interactive, LLC
*
*	may - august 2014
*/


//Connection to the MySQL Server by Andrew Sowers, for Push Interactive LLC
define('DB_SERVER', 'localhost'); // Mysql hostname
define('DB_USERNAME', 'root'); // Mysql username
define('DB_PASSWORD', 'titan'); // Mysql password
define('DB_DATABASE', 'push_interactive'); // Mysql database name
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());

function cleanVariable($variable){
	return stripcslashes(strip_tags(stripcslashes($variable)));
}

//$DBH = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_DATABASE.", ".DB_USERNAME.", ".DB_PASSWORD."");
	

function getAllBeaconsFromDB(){
	$result = mysql_query("SELECT * FROM beacon");
	while( $row = mysql_fetch_assoc( $result)){
    	$new_array[] = $row; // Inside while loop
	}
	return $new_array;
}

/*
*	getAllBeaconsExceptNULL
*
*	@param Nil
*
*	get every beacon from the database, except for the token NULL beacon (empty placeholder beacon)
*/
function getAllBeaconsExceptNull(){
	$result = mysql_query("SELECT * FROM beacon where beacon_id != 0 AND active = 1");
	while($row = mysql_fetch_assoc($result)){
		$new_array[] = $row;
	}
	return $new_array;
}

/*
*	getBeaconIdentFromCampaignID
*
*	@param Int: $campaingID
*
*	get the beacon identifier associated with a registered campaign
*/
function getBeaconIdentFromCampaignID($campaignID){
	$campaignID = stripcslashes(strip_tags($campaignID));
	$result = mysql_query("select identifier from beacon where beacon_id = (select beacon_id from campaign where campaign_id = '$campaignID')");
	return mysql_result($result, 0);
}

/*
*	getAllRealityFromDB
*
*	@param nil
*
*	get all reaity itmes from database and puts into basic array
*
*	**POTENTIALLY DEPRECATED**
*/
function getAllRealityFromDB(){
	$result = mysql_query("SELECT * FROM reality");
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[] = $row;
	}
	return $new_array;
}

/*
*	getAllCampaignsFromDB
*
*	@param nil
*
*	get every campaign form the database and put into basic array
*/
function getAllCampaignsFromDB(){
	$result = mysql_query("SELECT * FROM campaign");
	$new_arary = array();
	$i=0;
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[$i] = $row;
		$i++;
	}
	return $new_array;
}

/*
*	addNewBeaconToDB
*
*	@param String: $indentifier, String: $uuid, Int $major, Int: $minor
*
*	adds new beacon to the database with beacon credentials
*/
function addNewBeaconToDB($identifier,$uuid,$major,$minor){
	$identifier = stripcslashes(strip_tags($identifier));
	$uuid  = stripcslashes(strip_tags($uuid));
	$major = stripcslashes(strip_tags($major));
	$minor = stripcslashes(strip_tags($minor));
	$result = mysql_query("INSERT INTO beacon (beacon_id,uuid,major,minor) VALUES(DEFAULT,'$identifier','$uuid',".$major.",".$minor.")");
}

/*
*	registerBeaconFromDB
*
*	@param String: $beacon_id, String: $identifier
*
*	registers beacon from database and adds engish name / enables beacons use for application
*/
function registerBeaconFromDB($beacon_id,$identifier){
	$beacon_id  = cleanVariable($beacon_id);
	$identifier = cleanVariable($identifier); 
	$result = mysql_query("SELECT * FROM beacon WHERE beacon_id = '$beacon_id'");
	if($row = mysql_fetch_assoc($result)){
		$result = mysql_query("UPDATE beacon SET identifier='$identifier', active=1 WHERE beacon_id = '$beacon_id'");
		return true;
	}
	return false;
}

/*
*	deregisterBeaconFromDB
*
*	@param String: $beacon_id, String: $identifier
*
*	deregisters beacon from database and removes engish name / disables beacons use for application
*/
function deregisterBeaconFromDB($beacon_id,$identifier){
	$beacon_id  = cleanVariable($beacon_id);
	$identifier = cleanVariable($identifier); 
	$result = mysql_query("SELECT * FROM beacon WHERE beacon_id = '$beacon_id' AND identifier = '$identifier'");
	if($row = mysql_fetch_assoc($result)){
		$result = mysql_query("UPDATE beacon SET identifer='NULL', active=0 WHERE beacon_id = '$beacon_id'");
		return true;
	}
	return false;
}

/*
*	addNewCampaign
*
*	@param Int: $applicaiton_id, String: $unit_id, String: $campaign_name
*
*	Add new campaign associated with application_id, unit_id, and campaign_name in database
*/
function addNewCampaign($application_id,$unit_id,$campaign_name){
	$application_id = cleanVariable($application_id);
	$campaign_name = cleanVariable($campaign_name);
	$unit_id = cleanVariable($unit_id);
	$result = mysql_query("select campaign_name from campaign where campaign_name ='$campaign_name'");
	if($row = mysql_fetch_assoc($result)){
		return false;
	}
	else{
		$result = mysql_query("INSERT INTO campaign (application_id,item_name,campaign_name) VALUES ('$application_id','$unit_id','$campaign_name')");
		return true;
	}
}

/*
*	linkBeaconToCampaign
*
*	@param String: $campaign_name, String: $beacon_id
*
*	links campaign with beacon in campaign_has_beacon table of the database
*/
function linkBeaconToCampaign($campaign_name,$beacon_id){
	$campaign_id = cleanVariable($campaign_name);
	$beacon_id = cleanVariable($beacon_id);
	$result = mysql_query("INSERT INTO campaign_has_beacon (campaign_campaign_id, beacon_beacon_id) VALUES ((SELECT campaign_id from campaign WHERE campaign_name ='$campaign_name'),'$beacon_id')");
	return true;
}

/*
*	setupCampaignWithBeacon
*
*	@param String: $campaign_name, String: $unit_id, String: $beacon_id
*
*
*/
function setupCampaignWithBeacon($campaign_name,$unit_id,$beacon_id){
	$bool1 = false;
	$bool2 = false;
	if (addNewCampaign('2',$unit_id,$campaign_name))$bool1=true;
	if (linkBeaconToCampaign($campaign_name,$beacon_id))$bool2=true;
	if ($bool1&&$bool2) return true;
	return false;
}

/*
*	demolishCampaign
*
*	@param String: $campaign_name 
*
*	removes a campaign from the database by name
*/
function demolishCampaign($campaign_name){
	$campaign_id = cleanVariable($campaign_name);
	$result = mysql_query("DELETE FROM campaign_has_beacon WHERE campaign_campaign_id = (SELECT campaign_id FROM campaign WHERE campaign_name ='$campaign_name')");
	$result = mysql_query("DELETE FROM campaign WHERE campaign_name = '$campaign_name'");
}

/*
*	getCampaignsItem
*
*	@param String: $item_name
*
*	get item from campaign, return as array
*/
function getCampaignItem($item_name){
	$item_name = cleanVariable($item_name);
	$result = mysql_query("SELECT campaign_name from campaign WHERE item_name = '$item_name'");
	$campaign_name = mysql_fetch_row($result)[0];
	$result = mysql_query("SELECT identifier FROM beacon WHERE (SELECT beacon_beacon_id FROM campaign_has_beacon WHERE campaign_campaign_id = (SELECT campaign_id FROM campaign WHERE campaign_name = '$campaign_name'))");
	$identifier = mysql_fetch_row($result)[0];
	return array($campaign_name,$identifier);
}

/*
*	getUserFavorites
*
*	get all user favorites from database, returns as basic array
*/
function getUserFavorites(){
	$result = mysql_query('SELECT * FROM user_favorite');
	$new_arary = array();
	$i=0;
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[$i] = $row;
		$i++;
	}
	return $new_array;
}

/*
*	getTodaysUserFavorites
*
*	@param Nil
*
*	gets todays favorites and puts them into an array
*/
function getTodaysUserFavorites(){
	$result = mysql_query('SELECT * FROM user_favorite WHERE date(triggered) = current_date()');
	$new_arary = array();
	$i=0;
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[$i] = $row;
		$i++;
	}
	return $new_array;
}

/*
*	getUserFavoritesWithTrimmedDates
*
*	gets user_id, favorite_id, date of favorite from database, puts contents into basic array
*/
function getUserFavoritesWithTrimmedDates(){
	$result = mysql_query('SELECT user_user_id, favorite_id, date(triggered) FROM user_favorite');
	$new_arary = array();
	$i=0;
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[$i] = $row;
		$i++;
	}
	return $new_array;
}

/*
*	arrayCountOfFavoritesByDay
*
*	grabs all favorites with trimmed date, counts by date, returns array of favorites by day 
*/
function arrayCountOfFavoritesByDay(){
	$userFavorites = getUserFavoritesWithTrimmedDates();
	$favoritesCount = count($userFavorites);
	if($favoritesCount>0){
		$result = array(array("date"=>$userFavorites[0]["date(triggered)"],"count"=>1));
		for($i=1;$i<$favoritesCount;$i++){
			$d1 = new DateTime($userFavorites[$i-1]["date(triggered)"]);
	        $d2 = new DateTime($userFavorites[$i]["date(triggered)"]);
	        $diff=$d1->diff($d2);
	        if($diff->format('%d')==0){
	        	$result[count($result)-1]["count"]++;
	        }else{
	          	array_push($result,array("date"=>$userFavorites[$i]["date(triggered)"],"count"=>1));
			}
		}
		return $result;
	}else{
		return array();
	}
}

/*
*	arrayTodaysFavorites
*
*	@param Nil
*
*	gets todays favorites from database and puts into sorted array
*/
function arrayTodaysFavorites($listings){
	$favortes = getTodaysUserFavorites();
	$favoriteCount = count($favortes);
	$result = array();
	foreach ($favortes as $array) {
		array_push($result,$array["favorite_id"]);
	}
	sort($result);
	$resultCount = count($result);
	$return = array();
	if($resultCount>0){
		array_push($return,array("label"=>$result[0],"value"=>1));
		for($i=1;$i<$favoriteCount;$i++){
			if($result[$i]==$result[$i-1]){
				$return[count($return)-1]["value"]++;
			}else{
				array_push($return,array("label"=>$result[$i],"value"=>1));
			}
		}
		$returnCount = count($return);
		$listingsCount = count($listings);
		usort($return, function ($a, $b) { return $b['value'] - $a['value']; });
		for($i=0;$i<$returnCount;$i++){
			for($j=0;$j<$listingsCount;$j++){

				if($return[$i]["label"]==$listings[$j]["unitID"]){
					$address =$listings[$j]["address"];
					$commaPos = strpos($address,',');

					$return[$i]["label"]=substr($address,0,$commaPos);
					break;
				}
			}
		}
		return $return;
	}else{
		return $return;
	}
}

/*
*	arrayTopFiveFavoritesAllTime
*
*	@param Int: $limit, Multidimensional Array: $listings
*
*	parses listings and picks the highest favorites
*/
function arrayTopFavoritesAllTime($limit,$listings){
	$favorites = getUserFavorites();
	$favoritesCount = count($favorites);
	$result = array();
	foreach ($favorites as $array) {
		array_push($result,$array["favorite_id"]);
	}
	sort($result);
	$resultCount = count($result);
	$return = array();
	if($resultCount>0){
		array_push($return,array("label"=>$result[0],"value"=>1));
		for($i=1;$i<$favoritesCount;$i++){
			if($result[$i]==$result[$i-1]){
				$return[count($return)-1]["value"]++;
			}else{
				array_push($return,array("label"=>$result[$i],"value"=>1));
			}
		}
		$returnCount = count($return);
		$listingsCount = count($listings);
		usort($return, function ($a, $b) { return $b['value'] - $a['value']; });
		for($i=0;$i<$returnCount;$i++){
			for($j=0;$j<$listingsCount;$j++){

				if($return[$i]["label"]==$listings[$j]["unitID"]){
					$address =$listings[$j]["address"];
					$commaPos = strpos($address,',');

					$return[$i]["label"]=substr($address,0,$commaPos);
					break;
				}
			}
		}
		return $return;
	}else{
		return $return;
	}
}

/*
*	Class: alphanumaricAsciiConverter
*
*	encode/decode alphanumaric ident based on ascii values
*
*/
class alphanumaricAsciiConverter {

	private function asciiToAlphanum($char){
		$digit = ord($char);
		if ($digit>=48&&$digit<=57){
			return $digit;
		}else if($digit>=65&&$digit<=90){
			return $digit-55;
		}else if($digit>=97&&$digit<=122){
			return $digit-61;
		}
	}
	
	private function alphanumToAscii($digit){
		if ($digit>=0&&$digit<=9){
			return chr($digit+48);
		}else if($digit>=10&&$digit<=35){
			return chr($digit+55);
		}else if($digit>=36&&$digit<=61){
			return chr($digit+61);
		}
	}
	
	public function encodeAlphanumaricToAscii($array){
		$char0=$this->alphanumToAscii($array[0]);
		$char1=$this->alphanumToAscii($array[1]);
		$char2=$this->alphanumToAscii($array[2]);
		$char3=$this->alphanumToAscii($array[3]);
		return array($char0,$char1,$char2,$char3);
	}
	
	public function decodeAsciiToAlphanumaric($array){
		$digit0=$this->asciiToAlphanum($array[0]);
		$digit1=$this->asciiToAlphanum($array[1]);
		$digit2=$this->asciiToAlphanum($array[2]);
		$digit3=$this->asciiToAlphanum($array[3]);
		return array($digit0,$digit1,$digit2,$digit3);
	}
}
