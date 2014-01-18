<?php  

//model for listing staff ******************************************************************************

//3 types of models.... ADD, UPDATE and PULL
//this is an UPDATE model
//data model shows all fields available, default values AND what is required

//******************************************************************************************************

$_modelType = "update";

$_dataModel = array(

	'fields' => array(

		array(

			'field' => 'uid',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'false' 
			)
		),
		array(

			'field' => 'locationID',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'false' 
			)
		),
		array(

			'field' => 'employeeName',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'true'
			)
		),
		array(

			'field' => 'userName',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'true',
				'notes' => 'unique values only. will return error 23000 if duplicate is posted'
			)
		),
		array(

			'field' => 'password',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'true'
			)
		),
		array(

			'field' => 'securityRole',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'true'
			)
		),
		array(

			'field' => 'status',
			'rules' => array(
				'type' => 'string', 
				'default' => '',
				'canUpdate' => 'true'
			)
		)
	)
);

//define the queryModels
$_queryModels["default"] = <<<EOT
	UPDATE staff
		SET <<FIELDS>>
	<<CONDITION>>
EOT;

include_once 'data_model_controller.php';

?>