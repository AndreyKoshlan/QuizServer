<?php

function connectSQL($servername, $username, $password, $dbname) {
	// Create connection
	$conn = new mysqli($servername, $username, $password, $dbname);
	// Check connection
	if ($conn->connect_error) {
		//echo "Connection failed: " . $conn->connect_error . "\r\n";
		return NULL;
	}
	return $conn;
}

function connectDefault() {
	$sql_servername = "localhost";
	$sql_username = "root";
	$sql_password = "";
	$sql_dbname = "quizDB";

	return connectSQL($sql_servername, $sql_username, $sql_password, $sql_dbname);
}

?>