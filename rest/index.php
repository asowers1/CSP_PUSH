<?php

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
 
 
	// Print all promo codes in database
    function redeem() {
    // Check for required parameters
    
    
    if (isset($_POST["push_app_id"]) && isset($_POST["code"]) && isset($_POST["device_id"])&&isset($_POST["PUSH_ID"])) {
 
		
		
        // Put parameters into local variables
        $rw_app_id = $_POST["push_app_id"];
        $code = $_POST["code"];
        $device_id = $_POST["device_id"];
		
 
        // Look up code in database
        $user_id = 0;
        $stmt = $this->db->prepare('SELECT id, unlock_code, uses_remaining FROM push_promo_code WHERE id=? AND code=?');
        $stmt->bind_param("is", $push_apid, $code);
        $stmt->execute();
        $stmt->bind_result($id, $unlock_code, $uses_remaining);
        while ($stmt->fetch()) {
            break;
        }
        $stmt->close();
		
        // Bail if code doesn't exist
        if ($id <= 0) {
            sendResponse(400, 'Invalid code');
            return false;
        }
 
        // Bail if code already used		
        if ($uses_remaining <= 0) {
            sendResponse(403, 'Code already used');
            return false;
        }	
 
        // Check to see if this device already redeemed	
        $stmt = $this->db->prepare('SELECT id FROM push_promo_code_redeemed WHERE device_id=? AND rw_promo_code_id=?');
        $stmt->bind_param("si", $device_id, $id);
        $stmt->execute();
        $stmt->bind_result($redeemed_id);
        while ($stmt->fetch()) {
            break;
        }
        $stmt->close();
 
        // Bail if code already redeemed
        if ($redeemed_id > 0) {
            sendResponse(403, 'Code already used');
            return false;
        }
 
		
    }
    sendResponse(400, 'Invalid request');
    return false;
    }
    
    /*
    *	getBeacon
    *
    *	@PUSH_ID REST API key, @uuid the provided uuid for feting beacon credentials
    *
    *
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
	sendResponse(400, 'Invalid request beacon');
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
				sendResponse(400, 'Invalid code');
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
	    sendResponse(400, 0);
	    return false;
    }
    
    /*
    *	getListingDataFromBeacon
    *
    *	@PUSH_ID key for REST, @uuid beacon uuid, @major beacon major, @minor beacon minor
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
    *	@PUSH_ID key for REST
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
		    $stmt = $this->db->prepare('SELECT * FROM beacon');
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
    
    // end of RestAPI class
}
 
// This is the first thing that gets called when this page is loaded
// Creates a new instance of the RedeemAPI class and calls the redeem method
$api = new RestAPI;
$function = $_REQUEST["call"];
$api->$function();
?>