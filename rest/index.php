<?hh
/**
*	CSP REST API
*	Andrew Sowers - Push Interactive, LCC
*	June-August 2014
*
*	RETURN SCHEMA:
*	return types are json values containing arrays, arrays of dictionaries, strings of -1, 0, and 1.
*
*	-1 indicates general falsity or failure (maybe a duplicate record), 0 indicates failure to supply proper parameters, 1 indicates true or success
*	no response indicates no action took place
**/



include_once "rest.php";

// helper dbClass - may not be needed.
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

/*
*	RestAPI
*
*	the RESTful API for interfacing with the front end app and backend db
*
*/
class RestAPI {
    
    // Main database object
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
		    $stmt = $this->db->prepare("SELECT beacon_id, identifier, uuid, major, minor FROM beacon WHERE beacon_id != 'null' AND active != 0");
		    $stmt->execute();
			$stmt->bind_result($beacon_id,$identifier,$uuid,$major,$minor);
			/* fetch values */
			while ($stmt->fetch()) {
				$output[]=array($beacon_id,$identifier,$uuid,$major,$minor);
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
				sendResponse(400,json_encode(null));
				return false;   
		    }
			$uuid = stripslashes(strip_tags($_GET["uuid"]));
			$stmt = $this->db->prepare('SELECT favorite_id FROM user_favorite WHERE user_user_id = (SELECT user_id FROM user WHERE uuid=?)');
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
		sendResponse(400, json_encode(null));
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

    /*
    *	registerBeacon
    *
    *	registers beacon
    *
    *	@super_global_param PUSH_ID: Push rest key, beacon_id: beacon code identy, identifier: beacon english name
    */
    function registerBeacon(){
		if(isset($_POST["PUSH_ID"])&&isset($_POST["beacon_id"])&&isset($_POST["identifier"])){
		    if(!$this->checkPushID($_POST["PUSH_ID"])){
				sendResponse(400,'-1');
				return false;   
		    }
		    $beacon_id  = stripslashes(strip_tags($_POST["beacon_id"]));
		    $identifier = stripcslashes(strip_tags($_POST["identifier"]));

		    $stmt = $this->db->prepare("SELECT * FROM beacon WHERE beacon_id = ? and active = 1");
		    $stmt->bind_param("s",$beacon_id);
		    $stmt->execute();
		    $stmt->store_result();
		    $rows = $stmt->num_rows;
		    if($rows>0){
			    sendResponse(400, '-1');
			    return false;
		    }
		    
		    $stmt = $this->db->prepare('UPDATE beacon SET identifier=?, active=1 WHERE beacon_id = ?');
		    $stmt->bind_param("ss",$identifier,$beacon_id);
		    $stmt->execute();
			sendResponse(200, '1');
			return true;
	    }
	    sendResponse(400, '0'); 	
	    return false;
    }

    /*
    *	deregisterBeacon
    *
    *	deregisters beacon
    *
    *	@super_global_param PUSH_ID: Push rest key, beacon_id: beacon code identy, identifier: beacon english name
    *
    *	TODO - not needed, for now.
    */
    function deregisterBeacon(){

    }

    /*
    *	getCampaignHasBeacon
    *
    *	@super_global_param PUSH_ID: Push rest key
    *
    *	gets a multidimentional array of campaigns tied to units and beacons: [[campaign_id,unit_id,beacon_id],...]
    */
    function getCampaignHasBeacon(){
		if(isset($_GET["PUSH_ID"])){
		    if(!$this->checkPushID($_GET["PUSH_ID"])){
				sendResponse(400,"0");
				return false;   
		    }
			$stmt = $this->db->prepare('SELECT campaign_campaign_id,(SELECT item_name FROM campaign WHERE campaign_id = campaign_campaign_id),beacon_beacon_id  FROM campaign_has_beacon');
		    $stmt->execute();
			$stmt->bind_result($campaign_id,$item_name,$beacon_beacon_id);
			/* fetch values */
			$i=0;
			$output = array();
			while ($stmt->fetch()) {
				$output[$i]=array($campaign_id,$item_name,$beacon_beacon_id);
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
		sendResponse(400, "-1");
		return false;
    }

    /*
    *	registerTriggeredBeaconAction
    *
    *	@super_global_param PUSH_ID: Push rest key, campaign_id: name of campaign, clicked: 1(yes) or 0(no), uuid: anonymous user uuid
    *
	*	register an action tied to beacon and record clickthrough
	*/
	function registerTriggeredBeaconAction(){
		if(isset($_POST["PUSH_ID"])&&isset($_POST["campaign_id"])&&isset($_POST["action_type"])&&isset($_POST["clicked"])&&isset($_POST["uuid"])){
		    if(!$this->checkPushID($_POST["PUSH_ID"])){
				sendResponse(400,'-1');
				return false;   
		    }
		    $campaign_id = stripslashes(strip_tags($_POST["campaign_id"]));
		    $action_type = stripslashes(strip_tags($_POST["action_type"]));
		    $clicked     = stripslashes(strip_tags($_POST["clicked"]));
			$uuid        = stripslashes(strip_tags($_POST["uuid"]));
		    
		    $stmt = $this->db->prepare("INSERT INTO action (campaign_id,action_type,clicked,user_user_id) VALUES (?,?,?,(SELECT user_id FROM user WHERE uuid=?))");
		    $stmt->bind_param("ssis",$campaign_id,$action_type,$clicked,$uuid);
		    $stmt->execute();
		    $stmt->store_result();
		    //$rows = $stmt->num_rows;
		    //if($rows<=0){
			//    sendResponse(400, '-1');
			//    return false;
		    //}

			sendResponse(200, '1');
			return true;
	    }
	    sendResponse(400, '0'); 	
	    return false;	
	}


    // end of RestAPI class
}
 
// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RestAPI class and executes the rest method in the $_REQUEST super global array
$api = new RestAPI;
$api->$_REQUEST["call"]();
