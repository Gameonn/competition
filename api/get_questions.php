<?php
//this is an api to get feeds

// +-----------------------------------+
// + STEP 1: include required files    +
// +-----------------------------------+
require_once("../php_include/db_connection.php");
require_once("../classes/AllClasses.php");

$success=$msg="0";$data=array();
// +-----------------------------------+
// + STEP 2: get data				   +
// +-----------------------------------+


// +-----------------------------------+
// + STEP 3: perform operations		   +
// +-----------------------------------+


	$data= GeneralFunctions::getQuestions()?GeneralFunctions::getQuestions():[];
	if($data){
	$success="1";
	$msg="Records Found";
	}
	else{
	$success='0';
	$msg="No Record Found";
	}
	

// +-----------------------------------+
// + STEP 4: send json data			   +
// +-----------------------------------+
if($success==1){
echo json_encode(array("success"=>$success,"msg"=>$msg,"questions"=>$data));
}
else
echo json_encode(array("success"=>$success,"msg"=>$msg));
?>