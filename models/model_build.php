<?php  

/*

	Quickly build data models from a table

*/

//define acceptable table names
$_tables = array(

	'staff',
	'location'
);

if (isset($_POST["tableName"])) {

	$_table = trim($_POST["tableName"]);

	$_flag = 0;

	//did the client pass a valid table name?
	foreach ($_tables as $_tableName) {
		
		if ($_tableName == $_table) {
			$_flag = 1;
		}
	}

	if ($_flag == 1) {

		$_query = <<<EOT
		DESCRIBE $_table
EOT;

		try {
			
		    $db = new PDO("mysql:host=localhost;dbname=" . dbName, dbUser, dbPass);

   			$stmt = $db->query($_query); 
   			$results = $stmt->fetchAll(PDO::FETCH_COLUMN);

			$response = array(
				'api' => apiName, 
				'version' => apiVersion, 
				'status' => 'success', 
				'error' => 'false', 
				'msg' => 'Table: ' . $_table . ' columns returned', 
				'results' => $results
			);

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

		//not a valid table name
		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Please include all required fields', 
			'results' => 'none'
		);
	}

} else {

	//not a valid request
	$response = array(
		'api' => apiName, 
		'version' => apiVersion, 
		'status' => 'fail', 
		'error' => 'true', 
		'msg' => 'Please include all required fields', 
		'results' => 'none'
	);
}
?>