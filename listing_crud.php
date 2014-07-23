<?php 

	$url = "https://cspmgmt.managebuilding.com/Resident/PublicPages/XMLRentals.ashx?listings=all";
    $xml = simplexml_load_file($url);	
	$json = json_encode($xml);
	$array = json_decode($json,TRUE);
	
	
	function search_array($array, $needle) {
	    $results = array();
	
	    foreach ($array as $subarray) {
	        $hasValue = false;
	        foreach($subarray as $value){
	            if(is_string($value) && strpos($value,$needle) !== false)
	                $hasValue = true;
	        }
	        if($hasValue)
	            $results[] = $subarray;
	    }
	
	    return $results;
	}
	
	
	$buildium_id = $array["Property"][0]["Identification"]["IDValue"];
	$unit_id = $array["CustomRecords"][0]["Record"][4]["Value"];
	$listings_image = $array["Property"][0]["Floorplan"]["File"]["Src"];
	$address = $array["Property"][0]["PropertyID"]["Address"]["Address"].", ".$array["Property"][0]["PropertyID"]["Address"]["City"]." ".$array["Property"][0]["PropertyID"]["Address"]["State"].", ".$array["Property"][0]["PropertyID"]["Address"]["PostalCode"];
	$available = $array["CustomRecords"][0]["Record"][1]["Value"];
	$description = $array["Property"][0]["Information"]["LongDescription"];
	$beds = search_array($array["Property"][0]["Floorplan"]["Room"],"Bed")[0]["Count"];
	$baths = search_array($array["Property"][0]["Floorplan"]["Room"],"Bath")[0]["Count"];
	$sqft = $array["Property"][0]["Floorplan"]["SquareFeet"]["@attributes"]["Max"];
	$rent = $array["Property"][0]["Floorplan"]["EffectiveRent"]["@attributes"]["Max"];
	$heat = search_array($array["Property"][0]["Floorplan"]["Amenity"],"eat")[0]["Description"];

	$air_conditioning = search_array($array["Property"][0]["Floorplan"]["Amenity"],"conditioning")[0]["Description"];
	$balcony = search_array($array["Property"][0]["Floorplan"]["Amenity"],"alcony")[0]["Description"];
	$cable = search_array($array["Property"][0]["Floorplan"]["Amenity"],"able")[0]["Description"];
	$carport = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Carport")[0]["Description"];
	$dishwasher = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Dishwasher")[0]["Description"];
	$fenced = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Fenced yard")[0]["Description"];
	$fireplace = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Fireplace")[0]["Description"];
	$garage = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Fireplace")[0]["Description"];
	$hardwood = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Garage parking")[0]["Description"];
	$internet = search_array($array["Property"][0]["Floorplan"]["Amenity"],"High speed internet")[0]["Description"];
	$laundry = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Laundry room / hookups")[0]["Description"];
	$microwave = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Microwave")[0]["Description"];
	$oven = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Oven")[0]["Description"];
	$refrigerator = search_array($array["Property"][0]["Floorplan"]["Amenity"],"Refrigerator")[0]["Description"];
	//$virtual_tour = $array["Property"][0]; // needed?
	$walk_closet = search_array($array["Property"][0]["Floorplan"]["Amenity"],"closets")[0]["Description"];
	//$favorite = $array["Property"][0]; // done in database
	
	
	echo "buildium_id: ".$buildium_id .'<br>';
	echo "unit_id: ".$unit_id.'<br>';
	echo "listings_image: ".$listings_image.'<br>';
	echo "address: ".$address.'<br>';
	echo "available: ".$available.'<br>';
	echo "description: ".$description.'<br>';
	echo "beds: ".$beds.'<br>';
	echo "baths: ".$baths.'<br>';
	echo "sqft: ".$sqft.'<br>';
	echo "rent: ".$rent.'<br>';
	echo "heat: ".$heat.'<br>';
	echo "air_conditioning: ".$air_conditioning.'<br>';
	echo "balcony: ".$balcony.'<br>';
	echo "cable: ".$cable.'<br>';
	echo "carport: ".$carport.'<br>';
	echo "dishwasher: ".$dishwasher.'<br>';
	echo "fenced: ".$fenced.'<br>';
	echo "fireplace: ".$fireplace.'<br>';
	echo "garage: ".$garage.'<br>';
	echo "hardwood: ".$hardwood.'<br>';
	echo "internet: ".$internet.'<br>';
	echo "microwave: ".$microwave.'<br>';
	echo "oven: ".$oven.'<br>';
	echo "refrigerator: ".$refrigerator.'<br>';
	echo "walk_closet: ".$walk_closet.'<br>';
	

	
	echo "<br><br><pre>";
	var_dump($array["Property"]);
	//echo "</pre>";
    
?>
