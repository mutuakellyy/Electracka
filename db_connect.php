<?php

	//connect to the database
	$dbconnect = mysqli_connect('localhost', 'Joshua', 'josh24/7', 'security');//(server, username, password,database)
	//check if it is Successul
	if(!$dbconnect){
		echo "Database failed to connect" .mysqli_connect_error();  //to be commented
	}else{
		//echo "<p style='color:white'>Database connected";
	}
?>