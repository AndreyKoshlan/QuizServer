<?php
$servername = "localhost";
$username = "root";
$password = "";

function connectSQL($servername, $username, $password) {
	// Create connection
	$conn = new mysqli($servername, $username, $password);
	// Check connection
	if ($conn->connect_error) {
		echo "Connection failed: " . $conn->connect_error . "\r\n";
		return NULL;
	}
	return $conn;
}

function queryDB($conn, $sql) {
	if ($conn->query($sql) === TRUE) {
		return true;
	} else {
		echo "Command error: " . $conn->error . "\r\n";
		return false;
	}
}

function queryFileDB($conn, $filename) {
	$sql = file_get_contents($filename); 
	if ($conn->multi_query($sql) === TRUE) {
		return true;
	} else {
		echo "Command error: " . $conn->error . "\r\n";
		return false;
	}
}

function connectDB($conn, $dbname) {
	$sql = "USE " . $dbname;
	if (queryDB($conn, $sql)) {
		echo "Connected\r\n";
	}
}

function createDB($conn, $dbname) {
	// Create database
	$sql = "CREATE DATABASE " . $dbname;
	if (queryDB($conn, $sql)) {
		echo "Database created successfully\r\n";
	}
}

function createUsersTable($conn) {
	//$sql = "CREATE TABLE users (
	//id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
	//login VARCHAR(30) NOT NULL,
	//password VARCHAR(30) NOT NULL,
	//name VARCHAR(50) NOT NULL
	//)";
	if (queryFileDB($conn, "create.sql")) {
		echo "Tables created successfully\r\n";
	}
}

echo "Connecting to MySQL... \r\n";
$conn = connectSQL($servername, $username, $password);
if (isset($conn)) {
	echo "Creating database... \r\n";
	createDB($conn, "quizDB");
	echo "Connecting to database... \r\n";
	connectDB($conn, "quizDB");
	echo "Creating 'Users' table... \r\n";
	createUsersTable($conn);
}

$conn->close();
?>