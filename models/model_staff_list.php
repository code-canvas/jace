<?php  

//model for listing staff ******************************************************************************

//3 types of models.... ADD, UPDATE and PULL
//this is a PULL model
//data model shows all fields available, any fields that need masked, fields that can't be returned
//also shows fields that can be queried

//******************************************************************************************************

$_modelType = "pull";

$_dataModel = array(

	'fields' => array(

		array(

			'field' => 'uid',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'false', 
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
				'required' => 'false',
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
				'required' => 'false', 
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
				'required' => 'false', 
				'canLike' => 'true', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		),
		array(

			'field' => 'password',
			'rules' => array(
				'type' => 'string', 
				'query' => 'true', 
				'required' => 'false',
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
				'canLike' => 'false', 
				'canReturn' => 'true', 
				'masked' => 'false'
			)
		)
	)
);

//define the queryModels
$_queryModels["default"] = <<<EOT
	SELECT <<FIELDS>>
	FROM 
		staff
		<<CONDITION>>
	<<ORDERBY>>
	<<LIMITS>>
EOT;

include_once 'data_model_controller.php';

?>