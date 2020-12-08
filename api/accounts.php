<?php

	function getUserID($conn, $user_sid) {
		$sql = "SELECT uid FROM sessions WHERE sid = '".$user_sid."'";
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			$row = $rez->fetch_assoc();
			$ret->status = 1;
			$ret->uid = $row["uid"];
		} else {
			$ret->status = 0;
			$ret->error = "Token is incorrect";
		}
		return json_encode($ret);
	}

	function getInfo($conn, $user_id) {
		$sql = "SELECT name, passed, tests FROM users WHERE id = ".$user_id;
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			$row = $rez->fetch_assoc();
			$ret->status = 1;
			$ret->name = $row["name"];
			$ret->passed = $row["passed"];
			$ret->tests = $row["tests"];
		} else {
			$ret->status = 0;
			$ret->error = "User not found";
		}
		return json_encode($ret);
	}

	require 'connect.php';
	$msgtype = filter_var(trim($_POST['type']), FILTER_SANITIZE_STRING);
	$conn = connectDefault();
	if (!isset($conn)) {
		$ret->status = 0;
		$ret->reason = "Internal Server Error";
		echo json_encode($ret);
		die();
	}
	if ($msgtype === "info") {
		$uid = filter_var(trim($_POST['uid']), FILTER_SANITIZE_STRING);
		echo getInfo($conn, $uid);
	}
	if ($msgtype === "getid") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		echo getUserID($conn, $sid);
	}
	$conn->close();

?>