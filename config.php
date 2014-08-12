<?hh
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
	
function search_array($haystack, $needle) {
    $results = array();
	
    foreach ($haystack as $subarray) {
        $hasValue = false;
	        
        foreach($subarray as $value){
            if(is_string($value) && strpos($value,$needle) !== false){
                $hasValue = true;
            }
        }
        if($hasValue)
            $results[] = $subarray;
    }

    return $results;
}

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
	$new_arary = array();
	$i=0;
	while( $row = mysql_fetch_assoc( $result)){
		$new_array[$i] = $row;
		$i++;
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


function linkBeaconToCampaign($campaign_name,$beacon_id){
	$campaign_id = cleanVariable($campaign_name);
	$beacon_id = cleanVariable($beacon_id);
	$result = mysql_query("INSERT INTO campaign_has_beacon (campaign_campaign_id, beacon_beacon_id) VALUES ((SELECT campaign_id from campaign WHERE campaign_name ='$campaign_name'),'$beacon_id')");
	return true;
}


function setupCampaignWithBeacon($campaign_name,$unit_id,$beacon_id){
	$bool1 = false;
	$bool2 = false;
	if (addNewCampaign('2',$unit_id,$campaign_name))$bool1=true;
	if (linkBeaconToCampaign($campaign_name,$beacon_id))$bool2=true;
	if ($bool1&&$bool2) return true;
	return false;
}

function demolishCampaign($campaign_name){
	$campaign_id = cleanVariable($campaign_name);
	$result = mysql_query("DELETE FROM campaign_has_beacon WHERE campaign_campaign_id = (SELECT campaign_id FROM campaign WHERE campaign_name ='$campaign_name')");
	$result = mysql_query("DELETE FROM campaign WHERE campaign_name = '$campaign_name'");
}

function getCampaignItem($item_name){
	$item_name = cleanVariable($item_name);
	$result = mysql_query("SELECT campaign_name from campaign WHERE item_name = '$item_name'");
	$campaign_name = mysql_fetch_row($result)[0];
	$result = mysql_query("SELECT identifier FROM beacon WHERE (SELECT beacon_beacon_id FROM campaign_has_beacon WHERE campaign_campaign_id = (SELECT campaign_id FROM campaign WHERE campaign_name = '$campaign_name'))");
	$identifier = mysql_fetch_row($result)[0];
	return array($campaign_name,$identifier);
}


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



