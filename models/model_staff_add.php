<?php  

//model for listing staff ******************************************************************************

//3 types of models.... ADD, UPDATE and PULL
//this is an ADD model
//data model shows all fields available, default values AND what is required

//******************************************************************************************************

$_modelType = "add";

$_dataModel = array(

	'fields' => array(

		array(

			'field' => 'uid',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'true', 
				'default' => 'AUTOGEN',
				'canLike' => 'false', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		),
		array(

			'field' => 'locationID',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'true',
				'default' => '',
				'canLike' => 'false', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		),
		array(

			'field' => 'employeeName',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'true', 
				'default' => '',
				'canLike' => 'true', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		),
		array(

			'field' => 'userName',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'true',
				'default' => '', 
				'canLike' => 'true', 
				'canReturn' => 'true', 
				'masked' => 'false',
				'notes' => 'unique values only. will return error 23000 if duplicate is posted'
			)
		),
		array(

			'field' => 'password',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'true',
				'default' => '',
				'canLike' => 'false', 
				'canReturn' => 'true', 
				'masked' => 'true'
			)
		),
		array(

			'field' => 'securityRole',
			'rules' => array(
				'type' => 'integer', 
				'query' => 'true', 
				'required' => 'false',
				'default' => '10',
				'canLike' => 'false', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		),
		array(

			'field' => 'status',
			'rules' => array(
				'type' => 'integer', 
				'query' => 'true', 
				'required' => 'false', 
				'default' => '0',
				'canLike' => 'false', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		)
	)
);

//define the queryModels
$_queryModels["default"] = <<<EOT
	INSERT INTO staff
		(<<FIELDS>>)VALUES(<<VALUES>>);
EOT;

include_once 'data_model_controller.php';

?>