<?hh
//Connection to the MySQL Server by Andrew Sowers, for Push Interactive LLC
define('DB_SERVER', 'localhost'); // Mysql hostname
define('DB_USERNAME', 'root'); // Mysql username
define('DB_PASSWORD', 'titan'); // Mysql password
define('DB_DATABASE', 'push_interactive'); // Mysql database name
$connection = mysql_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD) or die(mysql_error());
$database = mysql_select_db(DB_DATABASE) or die(mysql_error());


//$DBH = new PDO("mysql:host=".DB_SERVER.";dbname=".DB_DATABASE.", ".DB_USERNAME.", ".DB_PASSWORD."");

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



