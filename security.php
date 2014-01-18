<?php

/* authenticate wrapper ***************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

function authenticate() {
		
	$_auth = $_POST;

	//test for the correct API key
	$apiKey = trim($_auth['apiKey']);

	if ( $apiKey != apiKey) {
		
		//API key is wrong. Requesting client probably doesn't belong here
		//tell them nothing... they will continue to think this user/pass is wrong and more on to the next combination
		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Authentication failed', 
			'results' => 'none'
		);

		//offer a little frustration
		$_sleepTime = rand(2, 10);
		sleep($_sleepTime);

		//build additional code to alert admin on brute force style attacks  TODO -

		respond($response);

	} else {

		//they have the proper API key... this is probably not an attack
		//auth the user
		authUser($_auth);	
	}
}




/* authenticate************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

function authUser($auth) {

	/*

		This allows the client to receive an authentication key that permits further access to the API

		Client passes user/password for evaluation. Default expiration value is 86400 seconds (24 hours). This can be adjusted with the keyExpire const. 

	*/
	
	$locationID = sanitize($auth['locationID']);
	$user = sanitize($auth['userName']);
	$pass = sanitize($auth['password']);

	if ( empty($locationID) || empty($user) || empty($pass)  ) {
	
		//respond with error
		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Please include all required parameters', 
			'results' => 'none'
		);

		respond($response);

	} else {

		//try to authenticate based on posted data
		try {

		    $db = new PDO("mysql:host=localhost;dbname=" . dbName, dbUser, dbPass);
			
			$query = <<<EOT

				SELECT 
					uid, 
					securityRole, 
					employeeName 
				FROM 
					staff 
				WHERE 
					locationID = ? 
				AND userName = ? 
				AND password = ? 
				AND status = '1';

EOT;

			$stmt = $db->prepare($query);
			$stmt->bindParam(1, $locationID, PDO::PARAM_STR, 12);
			$stmt->bindParam(2, $user, PDO::PARAM_STR, 12);
			$stmt->bindParam(3, $pass, PDO::PARAM_STR, 12);
			$stmt->execute();

			while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

				$userData = array(
					'uid' => $row["uid"], 
					'securityRole' => $row["securityRole"], 
					'employeeName' => $row["employeeName"]
				);
			}

			//check for valid userData
			if (!is_array($userData)) {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Authentication failed', 
					'results' => 'none'
				);

				respond($response);

				//TODO LOG THIS

			} else {

				//create the authKey and timestamp
				date_default_timezone_set('UTC');

				$timeStamp = time();
				$timeStampExpire = intval($timeStamp) + keyExpire; //expires in 24 hours
				$keySeed = uniqid() . $timeStamp . $timeStampExpire;

				//build a long key
				$key = $timeStamp . sha1($keySeed) . sha1(uniqid());

				$staffID = $userData["uid"];
				$securityRole = $userData["securityRole"];

				//copy userdata array for notes
				$u = $userData;
				$u["ipAddress"] = get_client_ip();

				//build log
				$notes = json_encode($u);

				// recorded in DB
				$query = <<<EOT

					INSERT INTO `api`.`authKeys` (
						`authKey` ,
						`locationID` ,
						`timestamp` ,
						`timestampExpire` ,
						`securityRole` ,
						`notes`
					)
					VALUES (
						'$key', 
						'$locationID', 
						'$timeStamp', 
						'$timeStampExpire', 
						'$securityRole', 
						'$notes'
					);

EOT;

				$stmt = $db->prepare($query);
				$stmt->execute();

				// delivered to the client: authKey and timeStamp
				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'success', 
					'error' => 'false', 
					'msg' => 'authenticated', 
					'results' => array(
						'key' => $key, 
						'timeStamp' => $timeStamp,
						'user' => $userData
					)
				);
				
				respond($response);

				//TODO - LOG THIS
			}

		}
		catch(PDOException $e) {

			//DB issue... report vague message and encourage contacting support
			$response = array(
				'api' => apiName, 
				'version' => apiVersion, 
				'status' => 'fail', 
				'error' => 'true', 
				'msg' => 'Message 104015. Please contact support.', 
				'results' => 'none'
			);

			respond($response);
		} 
	}
}






/* authKey*****************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

function authKey($auth, $securityRole){

	/*
		this method validates the user for a particular action
		
		authKey is validated by 4 factors... authKey(itself), apiKey, locationID and timestamp
		
	*/

	if ( !is_array( $auth ) ) {
		return "notArray";
	}
	
	//convert parameters to variables
	$authKey = "" . sanitize($auth['authKey']);
	$authTimeStamp = "" . sanitize($auth['authTimeStamp']);
	$apiKey = "" . sanitize($auth['apiKey']);
	$locationID = "" . sanitize($auth['locationID']);

	//check for nothing in one or more variables
	if ( empty($authKey) || empty($authTimeStamp) || empty($apiKey) || empty($locationID)  ) {
		return "emptyVariable";
	}	

	//check the timestamp
	if ( !is_numeric( $authTimeStamp ) ) {
		return "isNumericTimeStamp";
	}

	//did we get the correct API key?
	if ( apiKey != $apiKey ) {
		return "invalidApiKey";
	}

	//get the current time stamp
	date_default_timezone_set('UTC');

	$currentTimeStamp = time();

	//validate the auth data
	try {

	    $db = new PDO("mysql:host=localhost;dbname=" . dbName, dbUser, dbPass);
		
		$query = <<<EOT

			SELECT 
				`securityRole`, timestampExpire 
			FROM 
				`authKeys` 
			WHERE 
				`authKey` = ?
			AND `locationID` = ?
			AND `timestamp` = ? 
			AND `timestampExpire` > ?

EOT;

		$stmt = $db->prepare($query);
		$stmt->bindParam(1, $authKey, PDO::PARAM_STR, 12);
		$stmt->bindParam(2, $locationID, PDO::PARAM_STR, 12);
		$stmt->bindParam(3, $authTimeStamp, PDO::PARAM_INT);
		$stmt->bindParam(4, $currentTimeStamp, PDO::PARAM_INT);
		$stmt->execute();

		while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

			$authData = array(
				'securityRole' => $row["securityRole"],
				'timestampExpire' => $row["timestampExpire"]
			);
		}

		//check for valid data
		if ( is_array($authData) ) {

			//evaluate the security role
			$myRole = $authData["securityRole"];

			//check for numeric value
			if ( !is_numeric( $myRole ) ) { 
				return "isNumericMyRole";
			}

			//set the type for eval
			$myRole = intval( $myRole );

			if ( $myRole > $securityRole ) {
				//not enough permission
				return "invalidPermission";
			}

			//everything looks good
			return "success";

		} else {

			return "nothingReturned";
		}
	}
	catch(PDOException $e) {

		return "pdoFail";
	}
}






/* connectionMonitor*******************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/


function connectionMonitor($auth){

	//if the connections session exists... do garbage collection on expired connections
	if (isset($_SESSION["connections"])) {
		
		$timestamp = time();

		//garbage collection
		foreach ($_SESSION["connections"] as $key => $value) {
			
			$expire = $value["expire"];

			//not an int... just expire it
			if (!is_numeric($expire)) {
				$expire == 0;
			}

			$expire = intval($expire);

			//evaluate expire date
			if ($timestamp > $expire) {
				unset($_SESSION["connections"][$key]);
			}
		}
	}

	//evaluate the client request... get the API key they are using
	$_identifier = $auth['authKey'];

	if ( trim($_identifier) != "" ) {
			
		//check to see if the IP is already listed
		if (isset( $_SESSION["connections"][$_identifier])) {
			
			//evaluate thresholds
			$_clientRequest = $_SESSION["connections"][$_identifier];

			//increment counter
			$_SESSION["connections"][$_identifier]["visitCount"]++;

			//eval
			if ( intval($_SESSION["connections"][$_identifier]["visitCount"]) > connectionThresholdInt ) {

				return "failed";

			} else {

				return "success";
			}

		} else {

			//create a new entry for this IP
			$timestamp = time();

			//create timestamp length based on threshold
			$timestamp = intval($timestamp) + connectionTimer;

			//first request.. add client
			$_clientRequest = array(
				'expire' => $timestamp,
				'visitCount' => 1
			);

			//add to array by IP address
			$_SESSION["connections"][$_identifier] = $_clientRequest;

			return "success";
		}

	} else {

		//empty AUTH ID : fail

		return "failed";

	}
}





/* get_client_ip***********************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

function get_client_ip() {

	$ipaddress = '';

	if ($_SERVER['HTTP_CLIENT_IP'])

		$ipaddress = $_SERVER['HTTP_CLIENT_IP'];

	else if($_SERVER['HTTP_X_FORWARDED_FOR'])

		$ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];

	else if($_SERVER['HTTP_X_FORWARDED'])

		$ipaddress = $_SERVER['HTTP_X_FORWARDED'];

	else if($_SERVER['HTTP_FORWARDED_FOR'])

		$ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];

	else if($_SERVER['HTTP_FORWARDED'])

		$ipaddress = $_SERVER['HTTP_FORWARDED'];

	else if($_SERVER['REMOTE_ADDR'])

		$ipaddress = $_SERVER['REMOTE_ADDR'];

	else
		$ipaddress = 'UNKNOWN';

	return $ipaddress; 
}

?>
