<?php
	require 'db_connect.php';
	//session start
	session_start();
	
	//check if it is Successul
	if(!$dbconnect){
		echo "Database failed to connect" .mysqli_connect_error();  //to be commented
	}else{
		//create variables to pick up names from the session
		$firstname = $_SESSION['firstname'];
		$othername = $_SESSION['othername'];
	}
	//check whether the user is logged in
	if(!isset($_SESSION['firstname'])){
		//redirect the user to the login page
		header('Location: login.php');
	}
