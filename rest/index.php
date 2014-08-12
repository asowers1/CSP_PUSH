<?hh
/**
*	CSP REST API
*	Andrew Sowers - Push Interactive, LCC
*	June-August 2014
*
*	return types are json values containing arrays, arrays of dictionaries, strings of -1, 0, and 1.
*
*	-1 indicates general failure, 0 indicates false or failure, 1 indicates true or success
**/



include_once "rest.php";

class dbFunction {
	
	/*
	 * Utility function to automatically bind columns from selects in prepared statements to 
	 * an array
	 */
	function bind_result_array($stmt)
	{
	    $meta = $stmt->result_metadata();
	    $result = array();
	    while ($field = $meta->fetch_field())
	    {
	        $result[$field->name] = NULL;
	        $params[] = &$result[$field->name];
	    }
	 
	    call_user_func_array(array($stmt, 'bind_result'), $params);
	    return $result;
	}
}

class RestAPI {
    
    // Main method to redeem a code
    private $db;
 
    // Constructor - open DB connection
    function __construct() {
        $this->db = new mysqli('localhost', 'root', 'titan', 'push_interactive');
        $this->db->autocommit(TRUE);
    }
 
    // Destructor - close DB connection
    function __destruct() {
        $this->db->close();
    }
 
    function checkPushID($id){
		if($id!="123"){
			return false;
		}
		return true;
	}
 
    /*
    *	getBeacon
    *
    *	@PUSH_ID REST API key, @uuid the provided uuid for feting beacon credentials
    *
    *	@return array[array[beacon_id,identifier,uuid,major,minor],...]
    */
    function getBeacon() {
	if (isset($_GET["uuid"])) {
		$uuidIn = $_GET["uuid"];
		$stmt = $this->db->prepare('SELECT * FROM beacon WHERE uuid = ?');
		$stmt->bind_param("s",$uuidIn);
		$stmt->execute();
		$stmt->bind_result($beacon_id,$identifier,$uuid,$major,$minor);
		/* fetch values */
		while ($stmt->fetch()) {
			$output[]=array($beacon_id,$identifier,$uuid,$major,$minor);
		}
	    $stmt->close();
		
		// headers for not caching the results
		header('Cache-Control: no-cache, must-revalidate');
		header('Expires: Mon, 26 Jul 2020 05:00:00 GMT');

		// headers to tell that result is JSON
		header('Content-type: application/json');
		sendResponse(200, json_encode($output));

		return true;
	}
	sendResponse(400, '0');
    return false;
    }
    
    /*
    *	setBeacon
    *
    *	@PUSH_ID key for REST, @beacon_id id of beaocn for query, @identifier the text name for the identifier, @uuid beacon uuid, @major beacon major, @minor beacon minor
    *
    *	@return affected rows from update or error associated with the provided beacon
    */
    function setBeacon() {
	    if(isset($_POST["beacon_id"])&&isset($_POST["identifier"])&&isset($_POST["uuid"])&&isset($_POST["major"])&&isset($_POST["minor"])&&isset($_POST["PUSH_ID"])){
			if(!$this->checkPushID($_POST["PUSH_ID"])){
				sendResponse(400, "-1");
				return false;
			}
		    $beaconIdIn = $_POST["beacon_id"];
		    $uuidIn = $_POST["uuid"];
		    
		    $majorIn = $_POST["major"];
		    $minorIn = $_POST["minor"];
		    
		    $stmt = $this->db->prepare('UPDATE beacon SET uuid=?, major=?, minor=? WHERE beacon_id = ?');
		    $stmt->bind_param('siis',$uuidIn,$majorIn,$minorIn,$beaconIdIn);
		    $stmt->execute(); 
		    // send affected rows, zero if failure 
			sendResponse(200, $stmt->affected_rows);
			$stmt->close();	
			return true;
	    }
	    // send zero; failure
	    sendResponse(400, "0");
	    return false;
    }
    
    /*
    *	getListingDataFromBeacon gets the listings data per beacon.
    *
    *	@super_global_param PUSH_ID:key for REST @super_global_param uuid:beacon uuid @super_global_param major:beacon major
    *	@super_global_param minor:beacon minor
    *
    *	@return JSON object of listing data associated with the provided beacon
    */
    function getListingDataFromBeacon(){
	    if(isset($_GET["uuid"])&&isset($_GET["major"])&&isset($_GET["minor"])&&isset($_GET["PUSH_ID"]))
	    {
		    if(!$this->checkPushID($_GET["PUSH_ID"])){
				sendResponse(400, 'invalid code - ' . $_GET["PUSH_ID"]);
				return false;
			}
			sendResponse(200,'OK');
			return true;
	    }
	    sendResponse(400, 'Invalid param');
		return false;
    }
    
    /*
    *	getAllBeacons
    *
    *	@super_global_param PUSH_ID:key for REST
    *
    *	@return JSON object of all beacon rows from push_interactive DB
    */
    function getAllBeacons(){
    	$json;
	    if(isset($_GET["PUSH_ID"])){
		    if(!$this->checkPushID($_GET["PUSH_ID"])){
				sendResponse(400,json_encode($output));
				return false;   
		    }
		    $stmt = $this->db->prepare("SELECT * FROM beacon WHERE beacon_id != 'null'");
		    $stmt->execute();
			$stmt->bind_result($beacon_id,$identifier,$uuid,$major,$minor,$active);
			/* fetch values */
			while ($stmt->fetch()) {
				$output[]=array($beacon_id,$identifier,$uuid,$major,$minor,$active);
			}
		    $stmt->close();	
			// headers for not caching the results
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 2001 05:00:00 GMT');
			// headers to tell that result is JSON
			header('Content-type: application/json');
			sendResponse(200, json_encode($output));
			return true;
	    }
	    sendResponse(400, json_encode($output)); 	
	    return false;
    }
    
    function getAllListings(){
    	$json;
	    if(isset($_GET["PUSH_ID"])){
		    if(!$this->checkPushID($_GET["PUSH_ID"])){
				sendResponse(400,json_encode($json));
				return false;   
		    }
			include("listing_crud.php");
			sendResponse(200, $json_data);
			return true;
	    }
	    sendResponse(400, json_encode($json));
	    return false;
    }

	/*
	*	get list of favorites from anonymous user uuid.
	*
	*	@param PUSH_ID:key @param uuid:anonymous user uuid
	*/
	function getUserFavorites(){
		if(isset($_GET["PUSH_ID"])&&isset($_GET["uuid"])){
		    if(!$this->checkPushID($_GET["PUSH_ID"])){
				sendResponse(400,json_encode('test1'));
				return false;   
		    }
			$uuid = stripslashes(strip_tags($_GET["uuid"]));
			$stmt = $this->db->prepare('select favorite_id from user_favorite where user_user_id = (select user_id from user where uuid=?)');
			$stmt->bind_param("s",$uuid);
		    $stmt->execute();
			$stmt->bind_result($favorite);
			/* fetch values */
			$i=0;
			$output = array();
			while ($stmt->fetch()) {
				$output[$i]=$favorite;
				$i++;
			}
		    $stmt->close();	
			// headers for not caching the results
			header('Cache-Control: no-cache, must-revalidate');
			header('Expires: Mon, 26 Jul 2001 05:00:00 GMT');
			// headers to tell that result is JSON
			header('Content-type: application/json');
			sendResponse(200, json_encode($output));
			return true;
		}
		sendResponse(400, json_encode('test2'));
		return false;
	}
	
	/*
	*	add a user favorite by favorite_id
	*
	*	@param PUSH_ID:push rest key @param uuid: anonymous user id @param favorite_id: item to be favorited
	*/
	function addUserFavorite(){
		if(isset($_POST["PUSH_ID"])&&isset($_POST["uuid"])&&isset($_POST["favorite_id"])){
		    if(!$this->checkPushID($_POST["PUSH_ID"])){
				sendResponse(400,"-1");
				return false;   
		    }
		    $user_user_id = stripslashes(strip_tags($_POST["uuid"]));
		    $favorite_id  = stripslashes(strip_tags($_POST["favorite_id"]));
			$stmt = $this->db->prepare("SELECT * FROM user_favorite WHERE favorite_id = ? AND user_user_id = ?");
			$stmt->bind_param("ss",$favorite_id,$user_user_id);
			$stmt->execute();
			$stmt->store_result();
		    $rows = $stmt->num_rows;
		    if($rows>0){
			    sendResponse(400, '-1');
			    return false;
		    }
			$stmt = $this->db->prepare('INSERT INTO user_favorite (user_user_id,favorite_id) VALUES ((SELECT user_id FROM user WHERE uuid = ?),?)');
			$stmt->bind_param("ss", $user_user_id, $favorite_id);
		    $stmt->execute();

			sendResponse(200, "1");
			return true;
		}	
		sendResponse(400,"0");
		return false;
	}
	/*
	*	remove a user favorite by favorite_id
	*
	*	@param PUSH_ID:push rest key @param uuid: anonymous user id @param favorite_id: item to be favorited
	*/
	function removeUserFavorite(){
		if(isset($_POST["PUSH_ID"])&&isset($_POST["uuid"])&&isset($_POST["favorite_id"])){
		    if(!$this->checkPushID($_POST["PUSH_ID"])){
				sendResponse(400,"-1");
				return false;   
		    }
		    $user_user_id = stripslashes(strip_tags($_POST["uuid"]));
		    $favorite_id  = stripslashes(strip_tags($_POST["favorite_id"]));
			$stmt = $this->db->prepare("DELETE FROM user_favorite WHERE favorite_id = ?");
			$stmt->bind_param("s", $favorite_id);
		    $stmt->execute();
			$stmt->store_result();
		    $rows = $stmt->affected_rows;
		    if($rows<=0){
			    sendResponse(400, '-1');
			    return false;
		    }
			sendResponse(200, "1");
			return true;
		}	
		sendResponse(400,"0");
		return false;		
	}
	
	/*
	*	Adds new anonymous user to the user database
	*
	*	@super_global_param PUSH_ID:Push rest key @super_global_param uuid:anonymous uuid for user
	*/
	function addNewAnonUser(){
	    $json;
		if(isset($_POST["PUSH_ID"])&&isset($_POST["uuid"])){
		    if(!$this->checkPushID($_POST["PUSH_ID"])){
				sendResponse(400,'-1');
				return false;   
		    }
		    $uuid = stripslashes(strip_tags($_POST["uuid"]));
		    
		    $stmt = $this->db->prepare("SELECT * FROM user WHERE uuid = ?");
		    $stmt->bind_param("s",$uuid);
		    $stmt->execute();
		    $stmt->store_result();
		    $rows = $stmt->num_rows;
		    if($rows>0){
			    sendResponse(400, '-1');
			    return false;
		    }
		    
		    $stmt = $this->db->prepare('INSERT INTO user (uuid) values(?)');
		    $stmt->bind_param("s",$uuid);
		    $stmt->execute();
			sendResponse(200, '1');
			return true;
	    }
	    sendResponse(400, '0'); 	
	    return false;
    }

    // end of RestAPI class
}
 
// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new RestAPI;
$function = $_REQUEST["call"];
$api->$function();
