<?php

$user_sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
$user_id = filter_var(trim($_POST['uid']), FILTER_SANITIZE_STRING);

require 'connect.php';

function getUserID($user_sid){
	$sql_1 = "SELECT uid FROM sessions WHERE sid = '".$user_sid."'";
	$result_1 = $conn->query($sql_1);
	if ($result_1->num_rows > 0){
		$row_1 = $result_1->fetch_assoc();
		$ret->status = 1;
		$ret->uid = $row_1[uid];
	}
	else{
		$ret->status = 0;
	}
	return json_encode($ret);
}

function getInfo($user_id){
	$sql_2 = "SELECT name, passed, tests FROM users WHERE id = '".$user_id."'";
	$row_2 = $result_2->fetch_assoc();
	if ($result_2->num_rows > 0){
		$result_2 = $conn->query($sql_2);
		$ret->status = 1;
		$ret->name = $row_2[name];
		$ret->passed = $row_2[passed];
		$ret->tests = $row_2[tests];
	}
	else{
		$ret->status = 0;
	}
	return json_encode($ret);
}

$conn->close();

?>