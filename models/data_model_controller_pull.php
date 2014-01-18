<?php  

/*
	
	The data controller is included by a data model template and can process 2 types of query requests: custom and named.

	Custom requests from the client use the default template and can specify:	
		- fields to return
		- parameters
		- order by preferences
		- limits and pages

	Named queries run as defined and return predefined output

*/

//eval the queryModel
if (!isset($_POST["queryModel"])) {
	
	//doesn't exist... load the default
	$_queryModel = "default";	

}else{
	
	//the client did include a request... 
	$_queryModel = trim($_POST["queryModel"]);
	
	//eval for validity
	if (empty($_queryModel)) {

		$_queryModel = "default";	
	}
}

//attempt to load up the requested queryModel
if ( !isset( $_queryModels[$_queryModel] ) ) {
	
	//not a valid queryModel name... load the default
	$_query = $_queryModels["default"];

} else {

	//this name exists... load it up
	$_query = $_queryModels[$_queryModel];
}

//init the query fields variable
$_queryFields = "";

//eval the fields that should be returned
if ( $_queryModel == "default" ) {
	
	//default queryModel accepts specific fields
	//get the return fields
	$_returnFields = $_POST["returnFields"];

	//did the client specify return fields?
	if ( !is_array($_returnFields) ) {
		
		//no... just send them everything available
		$_fields = $_dataModel["fields"];

		//loop through the dataModel and see what fields can be used
		foreach ($_fields as $key => $value) {
			
			//can this field be used by the model?
			$canReturn = $value["rules"]["canReturn"];

			//eval
			if ($canReturn == "true") {
				
				//yes... add it to the field string
				$_queryFields .= $value["field"] . ",";
			}
		}
		
	} else {

		//the client has provided fields to return

		//get the fields from the data model to evaluate against
		$_fields = $_dataModel["fields"];

		//get each requested field and evaluate against data model
		foreach ($_returnFields as $_requestedField) {

			foreach ($_fields as $key => $value) {
			
				//can this field be used by the model?
				$canReturn = $value["rules"]["canReturn"];

				//eval
				if ($canReturn == "true") {
					
					//does this field match the requested field?
					if ( strtolower(trim($_requestedField)) == strtolower(trim($value["field"]))  ) {
						//if ( strtolower($_requestedField) == strtolower($value["field"])  ) {
					
						//yes... add it to the field string
						$_queryFields .= $value["field"] . ",";
							
					}
				}
			}
		}
	}

	//strip the trailing comma on the string
	$_queryFields = rtrim($_queryFields, ',');

	//get the parameters
	$_parameters = $_POST["parameters"];

	//init the parameter array variable
	$_parameterArray = "";

	//did the client specify parameter fields?
	if ( !is_array($_parameters) ) {

		//no WHERE clause... they want everything back
		$_where = "";

	} else {

		//get the fields from the data model to evaluate against
		$_fields = $_dataModel["fields"];

		//set counter
		$_cnt = 1;

		//init parm str
		$_parmStr = "";

		//get each requested parm and evaluate against data model
		foreach ($_parameters as $k => $_requestedParm) {

			foreach ($_fields as $key => $value) {
			
				//can this field be used by the model?
				$canReturn = $value["rules"]["canReturn"];

				//eval
				if ($canReturn == "true") {
					
					//does this field match the requested field?
					if ( strtolower(trim($_requestedParm["field"])) == strtolower(trim($value["field"]))  ) {
						
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
							$_parmStr .= " " . $_operator . " :" . trim($_requestedParm["field"]) . " ";

						} else if ($_operator == "LIKE") {

							//$_parmStr .= " " . $_operator . " %?% ";
							$_parmStr .= " " . $_operator . " %" . ":" . trim($_requestedParm["field"]) . "% ";
						}
						
						//get the variable of the field name for simplicity 
						$fieldName = $_requestedParm["field"];

						//build the placeholder
						$_placeholder = ":" . $fieldName;

						//add to parameter array
						$_parameterArray[$_placeholder] = $_requestedParm["value"];

						//increment count
						$_cnt++;		
					}
				}
			}
		}
	}

	//get the order by
	$_orderBy = $_POST["orderBy"];

	//did the client specify order by fields?
	if ( !is_array($_orderBy) ) {

		//no order by... just return everything db defaults
		$_orderByStr = "";

	} else {

		//get the fields from the data model to evaluate against
		$_fields = $_dataModel["fields"];

		//init parm str
		$_orderByStr = "";

		//set counter
		$_cnt = 1;

		//get each requested item and evaluate against data model
		foreach ($_orderBy as $k => $_requestedOrderBy) {

			foreach ($_fields as $key => $value) {
			
				//can this field be used by the model?
				$canReturn = $value["rules"]["canReturn"];

				//eval
				if ($canReturn == "true") {
					
					//does this field match the requested field?
					if ( strtolower(trim($_requestedOrderBy["field"])) == strtolower(trim($value["field"]))  ) {

						//get the direction
						$_orderDirection = strtoupper(trim($_requestedOrderBy["direction"]));

						//validate request is AND or OR?
						if ( $_orderDirection != "ASC" && $_orderDirection != "DESC") {
							$_orderDirection = "ASC"; //set default value
						}

						//include ORDER BY to string?
						if ($_cnt == 1) {
							
							//build the condition into the parm
							$_orderByStr .= "ORDER BY " . trim($_requestedOrderBy["field"] . " " . $_orderDirection . ",");

						}else{

							//build the condition into the parm
							$_orderByStr .=  trim($_requestedOrderBy["field"] . " " . $_orderDirection . ",");

						}

						//increment count
						$_cnt++;	
					}
				}
			}
		}

		//strip the trailing comma on the string
		$_orderByStr = rtrim($_orderByStr, ',');
	}

	//limits
	$_limitPageStart = $_POST["pageStart"];
	$_limitPagesReturn = $_POST["pageLimit"];

	//validate
	if ( !is_numeric($_limitPageStart) ) {
		$_limitPageStart = "0";
	}

	//check to see if the client is within our limits
	if (intval($_limitPageStart) < 0) {
		$_limitPageStart = "0";
	}

	//validate
	if ( !is_numeric($_limitPagesReturn) ) {
		$_limitPagesReturn = "100";
	}

	//check to see if the client is within our limits
	if (intval($_limitPagesReturn) > 100) {
		$_limitPagesReturn = "100";
	}

	//build the limits (i.e. LIMIT 0, 100)
	$_limits = "LIMIT " . $_limitPageStart . ", " . $_limitPagesReturn;

	//build the statement

	$_query = str_replace("<<FIELDS>>", $_queryFields, $_query);
	$_query = str_replace("<<CONDITION>>", $_parmStr, $_query);
	$_query = str_replace("<<ORDERBY>>", $_orderByStr, $_query);
	$_query = str_replace("<<LIMITS>>", $_limits, $_query);


	//get the data
	try {

	    $db = new PDO("mysql:host=localhost;dbname=" . dbName, dbUser, dbPass);

		$stmt = $db->prepare($_query, array(PDO::ATTR_CURSOR => PDO::CURSOR_FWDONLY)); 

		if ( !is_array($_parameterArray)) {
			
			$stmt->execute();

		}else{
			
			$stmt->execute($_parameterArray);
		}

		$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

		if (count($results) == 0) {
			$results = "";
		}

		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'success', 
			'error' => 'false', 
			'msg' => $_msgSuccess, 
			'results' => $results
		);

	}
	catch(PDOException $e) {

		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Error 100220. Please seek support.', 
			'results' => 'none'
		);
	}

} else {

	//NOT DEFAULT 

	//Named queries allow for complex joins. Allowed parms are based on the default queryModel only
	//limits are allowed


}

?>