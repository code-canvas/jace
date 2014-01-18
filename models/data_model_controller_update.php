<?php  

/*

	Data Controller for UPDATE statements. Uses: Update Data Models

*/

//define query model
$_queryModel = "default";

//set the query
$_query = $_queryModels[$_queryModel];

//get the fields from the data model to evaluate against
$_fields = $_dataModel["fields"];

//get the posted fields from client
$_updateFields = $_POST["updateFields"];

//init the query fields variable
$_queryFields = "";

//init the parameter array variable
$_parameterArray = "";

//did the client specify update fields?
if ( is_array($_updateFields) ) {

	//the client has provided fields to update

	//get the fields from the data model to evaluate against
	$_fields = $_dataModel["fields"];

	//get each requested field and evaluate against data model
	foreach ($_updateFields as $_updateKey => $_updateField) {

		//run through the data model fields looking for a match
		foreach ($_fields as $key => $value) {
		
			//can this field be used by the model?
			$_canUpdate = $value["rules"]["canUpdate"];

			//eval
			if ($_canUpdate == "true") {
				
				//does this field match the requested field?
				if ( strtolower(trim($_updateKey) ) == strtolower(trim($value["field"]))  ) {
				
					//yes... add it to the field string
					$_queryFields .= $value["field"] . " = :" . $value["field"] . ",";

					//build the placeholder
					$_placeholder = ":" . $value["field"];

					//add to parameter array
					$_parameterArray[$_placeholder] = $_updateField;
						
				}
			}
		}
	}

	//strip the trailing comma on the string
	$_queryFields = rtrim($_queryFields, ',');

	//get the parameters
	$_parameters = $_POST["parameters"];

	//did the client specify parameter fields?
	if ( is_array($_parameters) ) {

		//get the fields from the data model to evaluate against
		$_fields = $_dataModel["fields"];

		//set counter
		$_cnt = 1;

		//init parm str
		$_parmStr = "";

		//get each requested parm and evaluate against data model
		foreach ($_parameters as $k => $_requestedParm) {

			//run through the data model fields looking for a match
			foreach ($_fields as $key => $value) {
			
				//does this field match the requested field?
				if ( strtolower(trim($_requestedParm["field"])) == strtolower(trim($value["field"])) ) {
					
					//use WHERE?
					if ($_cnt == 1) {
						
						$_parmStr .= "WHERE " . trim($_requestedParm["field"]);

					} else {

						//get the conditional operator
						$_condOperator = strtoupper(trim($_requestedParm["condOper"]));

						//validate request is AND or OR?
						if ( $_condOperator != "AND" && $_condOperator != "OR") {

							$_condOperator = "AND"; //set default value
						}

						//build the condition into the parm
						$_parmStr .= $_condOperator . " " . trim($_requestedParm["field"]);
					}

					//build operator
					$_operator = strtoupper(trim($_requestedParm["operator"]));

					//validate
					if ( $_operator != "=" && $_operator != "!=" && $_operator != "<" && $_operator != "<=" && $_operator != ">" && $_operator != ">=" && $_operator != "LIKE" && $_operator != "NOT LIKE" && $_operator != "IS NULL" && $_operator != "IS NOT NULL" && $_operator != "IN" && $_operator != "NOT IN") {
						
						$_operator = "="; //set default value
					}

					//define operator structure
					if ( $_operator == "=" || $_operator == "!=" || $_operator == "<" || $_operator == "<=" || $_operator == ">" || $_operator == ">=" || $_operator == "NOT LIKE" || $_operator == "IS NULL" || $_operator == "IS NOT NULL" || $_operator == "IN" || $_operator == "NOT IN") {
						
						//$_parmStr .= " " . $_operator . " ? ";
						$_parmStr .= " " . $_operator . " :p_" . trim($_requestedParm["field"]) . " ";

					} else if ($_operator == "LIKE") {

						//$_parmStr .= " " . $_operator . " %?% ";
						$_parmStr .= " " . $_operator . " %" . ":p_" . trim($_requestedParm["field"]) . "% ";
					}
					
					//get the variable of the field name for simplicity 
					$fieldName = $_requestedParm["field"];

					//build the placeholder... assign 'p_' to differentiate from values being set
					$_placeholder = ":p_" . $fieldName;

					//add to parameter array
					$_parameterArray[$_placeholder] = $_requestedParm["value"];

					//increment count
					$_cnt++;		
				}
			}
		}

		$_query = str_replace("<<FIELDS>>", $_queryFields, $_query);
		$_query = str_replace("<<CONDITION>>", $_parmStr, $_query);

		//update the data
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
					'msg' => 'Updated Record', 
					'results' => $results
				);

			} else {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Record not updated. ' . $stmt->errorCode(),
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

	} else {

		//no posted parameters from client
		//kick error

		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Please include required parameters.', 
			'results' => 'none'
		);
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