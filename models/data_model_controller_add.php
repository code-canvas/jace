<?php  

/*

	Data Controller for INSERT statements. Uses: Add Data Models

*/

//define query model
$_queryModel = "default";

//set the query
$_query = $_queryModels[$_queryModel];

//get the fields from the data model to evaluate against
$_fields = $_dataModel["fields"];

//get the posted fields from client
$_addFields = $_POST["addFields"];

//define the array that will hold the fields and values
$_queryParts = array();

//the unique identifier for the new record
$_uid = "";

//eval posted data from client
if ( is_array($_addFields) ) {
	
	//set the flag in case we have to exit the upcoming loop
	$_exitLoop = false;

	//get all fields from model... eval against posted decide if requiremenets are met
	foreach ($_fields as $key => $value) {

		//is this required?
		$_required = $value["rules"]["required"];

		//define the default value if not required and nothing offered
		$_defaultValue = $value["rules"]["default"];

		//reset the matched flag
		$_matched = false;

		//run through the posted fields
		foreach ($_addFields as $add_key => $add_value) {
			
			//trim the key name
			$add_key = trim($add_key);

			//did we get a match?
			if ($add_key == $value["field"]) {
				
				//matched
				$_matched = true;

				//does the field model require an auto generatec unique ID?
				if ($_defaultValue == "AUTOGEN") {
					
					//build unique ID
					$_uid = time();
					$_uid = uniqid() . $_uid;

					//set the unique ID
					$add_value = $_uid;
				}

				//is there data?
				if (trim($add_value) == "") {
					
					//no value... set the default value
					$add_value = $_defaultValue;
				}

				$_queryItem = array(
					'fieldName' => $add_key, 
					'value' => $add_value
				);

				//get the variable of the field name for simplicity 
				$fieldName = $add_key;

				//build the placeholder
				$_placeholder = ":" . $fieldName;

				//add to parameter array
				$_parameterArray[$_placeholder] = $add_value;

				//add to array
				array_push($_queryParts, $_queryItem);
			}
		}

		//did we match the model field in the POSTED data?
		if ($_matched == false) {
			
			//was it required?
			if ($_required == "true") {
				
				//if required then kick error
				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Please include all required fields.', 
					'results' => ''
				);

				$_exitLoop = true;

				//break out of loop with an error
				break;

			}
		}
	}

	if ($_exitLoop == false) {
		
		//found everything we needed and didn't exit the discover loop prematurely
		//build the query

		$_queryFields == "";
		$_queryValues == "";

		//build fields
		foreach ($_queryParts as $key => $value) {
			
			$_queryFields .= $value["fieldName"] . ",";
			$_queryValues .= ":" . $value["fieldName"] . ",";
		}

		//strip the trailing comma on the string
		$_queryFields = rtrim($_queryFields, ',');
		$_queryValues = rtrim($_queryValues, ',');

		$_query = str_replace("<<FIELDS>>", $_queryFields, $_query);
		$_query = str_replace("<<VALUES>>", $_queryValues, $_query);

		//return the unique ID 
		$results["uid"] = $_uid;

		//create the data
		try {

		    $db = new PDO("mysql:host=localhost;dbname=" . dbName, dbUser, dbPass, array(PDO::MYSQL_ATTR_FOUND_ROWS => true));

			$stmt = $db->prepare($_query); 

			$stmt->execute($_parameterArray);

			$count = $stmt->rowCount();

			if($count != 0) {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'success', 
					'error' => 'false', 
					'msg' => "Added Record", 
					'results' => $results
				);

			} else {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Record not added. ' . $stmt->errorCode(),  
					'results' => 'none'
				);
			}
		}
		catch(PDOException $e) {

			$response = array(
				'api' => apiName, 
				'version' => apiVersion, 
				'status' => 'fail', 
				'error' => 'true', 
				'msg' => 'Error: ' . $e->getCode(), // . " " . $e->getMessage(), 
				'results' => 'none'
			);
		}
	}

} else {

	//no posted fields from client
	//kick error

	$response = array(
		'api' => apiName, 
		'version' => apiVersion, 
		'status' => 'fail', 
		'error' => 'true', 
		'msg' => 'Please include all required fields.', 
		'results' => 'none'
	);

}
?>