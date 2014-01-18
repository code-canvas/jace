<?php  

//define what controller to use
if ( $_modelType == "add" ) {
	
	include 'data_model_controller_add.php';

} else if ( $_modelType == "update" ) {
	
	include 'data_model_controller_update.php';

} else if ( $_modelType == "pull" ) {
	
	include 'data_model_controller_pull.php';
} 

?>