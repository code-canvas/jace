<?php

/* CONTROLLER**************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
***************************************************************************
**************************************************************************/

function buildModel(){

	//evaluate the connection against thresholds
	$connection = connectionMonitor($_POST["auth"]);

	if ( $connection == "success" ) {

		//get the directive
		$do = trim($_POST["do"]); 

		//route the request
		if ( $do == "buildModel" ) {
			
			//authenticate the request 
			$auth = authKey($_POST["auth"], 1);

			if ( $auth == "success" ) {
				
				//get data model for request
				include './models/model_build.php';

				respond($response);

			} else {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Your authentication has failed for this action', 
					'results' => 'none'
				);
				
				respond($response);
			}

		}

	} else {

		//failed the connection threshold test
		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Too many connections to the service.', 
			'results' => 'none'
		);
		
		respond($response);	
	}
}


/*

******************************************************
******************************************************
******************************************************
******************************************************
******************************************************
******************************************************
******************************************************
******************************************************

*/


function staff() {

	//evaluate the connection against thresholds
	$connection = connectionMonitor($_POST["auth"]);

	if ( $connection == "success" ) {

		//get the directive
		$do = trim($_POST["do"]); 

		//route the request
		if ( $do == "listStaff" ) {
			
			//authenticate the request 
			$auth = authKey($_POST["auth"], 3);

			if ( $auth == "success" ) {
				
				//get data model for request
				include './models/model_staff_list.php';
				
				$_msgSuccess = "List of staff";

				respond($response);

			} else {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Your authentication has failed for this action', 
					'results' => 'none'
				);
				
				respond($response);
			}

		/*

		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************

		*/

		} else if ( $do == "addStaff" ) {
			
			//authenticate the request 
			$auth = authKey($_POST["auth"], 3);

			if ( $auth == "success" ) {
				
				//get data model for request
				include './models/model_staff_add.php';

				$_msgSuccess = "Added staff member";

				respond($response);

			} else {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Your authentication has failed for this action', 
					'results' => 'none'
				);
				
				respond($response);
			}


		/*

		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************
		******************************************************

		*/

		} else if ( $do == "updateStaff" ) {
			
			//authenticate the request 
			$auth = authKey($_POST["auth"], 3);

			if ( $auth == "success" ) {
				
				//get data model for request
				include './models/model_staff_update.php';

				$_msgSuccess = "Updated staff member";

				respond($response);

			} else {

				$response = array(
					'api' => apiName, 
					'version' => apiVersion, 
					'status' => 'fail', 
					'error' => 'true', 
					'msg' => 'Your authentication has failed for this action', 
					'results' => 'none'
				);
				
				respond($response);
			}

		} else {

			$response = array(
				'api' => apiName, 
				'version' => apiVersion, 
				'status' => 'fail', 
				'error' => 'true', 
				'msg' => 'Please include a valid DO directive', 
				'results' => 'none'
			);
			
			respond($response);
		}

	} else {

		//failed the connection threshold test
		$response = array(
			'api' => apiName, 
			'version' => apiVersion, 
			'status' => 'fail', 
			'error' => 'true', 
			'msg' => 'Too many connections to the service.', 
			'results' => 'none'
		);
		
		respond($response);	
	}
}
?>
