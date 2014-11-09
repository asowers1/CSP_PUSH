<?hh

	/*
	*	Listing CRUD / Parser for Buildium/CSPMGMT data
	*	
	*	Andrew Sowers - Push Interactive, LLC
	*
	*	July, 2014
	*
	*	Gets all listings and places each listing into a dictionary. Each Dictionary is placed into an array.
	*/

	// get and normalize data: XML -> JSON -> Array
	//$url = "https://cspmgmt.managebuilding.com/Resident/PublicPages/XMLRentals.ashx?listings=all";
    $xml = simplexml_load_file("listing.xml");
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	
	// searches through array and looks for string value. Returns the array that contain the value as new array
	// for later parsing
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
	
	// listings queue for later json encoding
	$listing_array = array();
	// loop through all properties
	$count = count($array["Property"]);
	for($i=0;$i<$count;$i++){
		$buildiumID = NULL;
		$unitID = NULL;
		$listings_image = NULL;
		$address = NULL;
		$available = NULL;
		$listDate = NULL;
		$unavailable = NULL;
		$description = NULL;
		$beds = NULL;
		$baths = NULL;
		$sqft = NULL;
		$rent = NULL;
		$heat = NULL;
		$airConditioning = NULL;
		$balcony = NULL;
		$cable = NULL;
		$carport = NULL;
		$dishwasher = NULL;
		$fenced = NULL;
		$fireplace = NULL;
		$garage = NULL;
		$hardwood = NULL;
		$internet = NULL;
		$laundry = NULL;
		$microwave = NULL;
		$oven = NULL;
		$refrigerator = NULL;
		$walk_closet = NULL;
		
				
		$buildiumID = $array["Property"][$i]["Identification"]["IDValue"];
		$unitID = $array["CustomRecords"][$i]["Record"][4]["Value"];
		$listings_image = array($array["Property"][$i]["Floorplan"]["File"]["Src"]);
		if(!is_string($listings_image[0])){
			$imageCount = count($array["Property"][$i]["Floorplan"]["File"]);
			$imageArray = array();
			for ($j = 0;$j<$imageCount;$j++){
				$imageArray[$j] = $array["Property"][$i]["Floorplan"]["File"][$j]["Src"];
			}
			$listings_image = $imageArray;
		}
		$address = $array["Property"][$i]["PropertyID"]["Address"]["Address"].", ".$array["Property"][$i]["PropertyID"]["Address"]["City"]." ".$array["Property"][$i]["PropertyID"]["Address"]["State"].", ".$array["Property"][$i]["PropertyID"]["Address"]["PostalCode"];
		$available = $array["CustomRecords"][$i]["Record"][1]["Value"];
		$listDate = $array["CustomRecords"][$i]["Record"][2]["Value"];
		$unavailable = $array["CustomRecords"][$i]["Record"][3]["Value"];
		//$description = NULL;
		if(!is_array($array["Property"][$i]["Information"]["LongDescription"]))
			$description = $array["Property"][$i]["Information"]["LongDescription"];
		
		$beds = search_array($array["Property"][$i]["Floorplan"]["Room"],"ed")[0]["Count"];
		$baths = search_array($array["Property"][$i]["Floorplan"]["Room"],"ath")[0]["Count"];
		$sqft = $array["Property"][$i]["Floorplan"]["SquareFeet"]["@attributes"]["Max"];
		$rent = $array["Property"][$i]["Floorplan"]["EffectiveRent"]["@attributes"]["Max"];
		$heat = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"eat")[0]["Description"];
		$airConditioning = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"conditioning")[0]["Description"];
		$balcony = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"alcony")[0]["Description"];
		$cable = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"able")[0]["Description"];
		$carport = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Carport")[0]["Description"];
		$dishwasher = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Dishwasher")[0]["Description"];
		$fenced = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Fenced yard")[0]["Description"];
		$fireplace = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"ireplace")[0]["Description"];
		$garage = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"arage")[0]["Description"];
		$hardwood = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"ardwood")[0]["Description"];
		$internet = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"High speed internet")[0]["Description"];
		$laundry = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Laundry room / hookups")[0]["Description"];
		$microwave = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Microwave")[0]["Description"];
		$oven = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Oven")[0]["Description"];
		$refrigerator = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"Refrigerator")[0]["Description"];
		//$virtual_tour = $array["Property"][0]; // needed?
		$walk_closet = search_array($array["Property"][$i]["Floorplan"]["Amenity"],"closets")[0]["Description"];
		
		//$favorite = $array["Property"][0];
		
		$listing = array("buildiumID"=>$buildiumID,"unitID"=>$unitID,"listingsImage"=>$listings_image,"address"=>$address,"available"=>$available,"listDate"=>$listDate,"unavailable"=>$unavailable,"description"=>$description,"beds"=>$beds,"baths"=>$baths,"sqft"=>$sqft,"rent"=>$rent,"heat"=>$heat,"airConditioning"=>$airConditioning,"balcony"=>$balcony,"cable"=>$cable,"carport"=>$carport,"dishwasher"=>$dishwasher,"fenced"=>$fenced,"fireplace"=>$fireplace,"garage"=>$garage,"hardwood"=>$hardwood,"internet"=>$internet,"laundry"=>$laundry,"microwave"=>$microwave,"oven"=>$oven,"refrigerator"=>$refrigerator,"walkCloset"=>$walk_closet);
		//$data["property".$i]=$listing;
		// push listing to the stack
		$listing_array[$i]=$listing;
		if($address == "1170 East Shore Dr, Ithaca NY, 14850"){


		}

		
		//echo $i.'<br>';	
		//echo "<pre>".print_r($array["Property"][$i]["Information"]["LongDescription"])."</pre>";
	}

	$json_data = json_encode($listing_array,JSON_UNESCAPED_SLASHES);

 
