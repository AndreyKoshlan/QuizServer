<?php

function CheckSid($conn, $sid) {
		$sql = "SELECT sid FROM sessions WHERE sid = '".$sid."'";
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

function StartTest($conn, $testid, $sid) {
		$sql = "SELECT content FROM tests WHERE testid = '".$testid."'";
		$rez = $conn->query($sql);
		if (CheckSid($conn, $sid)) {
			$row = $rez->fetch_assoc();
			$ret->status = 1;
			$ret->content = $row["content"];
		} else {
			$ret->status = 0;
			$ret->error = "Test not found";
		}
		return json_encode($ret);
	}
	
function CreateTest($conn, $sid, $content, $answers, $name, $groupid, $uid){
		if (CheckSid($conn, $sid)){
			$sql = "INSERT INTO tests (content, answers, name, groupid, uid) VALUES ('".$content."', '".$answers."', '".$name."', '".$groupid."', '".$uid."')";
			if ($conn->query($sql) === TRUE) {
			res->status = 1;
			} else {
				$ret->status = 0;
				$ret->error = "Test not created";
			}
		} else {
			$ret->status = 0;
			$ret->error = "Token is incorrect";
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
	if ($msgtype === "start") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$testid = filter_var(trim($_POST['testid']), FILTER_SANITIZE_STRING);
		echo StartTest($conn, $sid, $testid);
	}
	if ($msgtype === "finish") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$testid = filter_var(trim($_POST['testid']), FILTER_SANITIZE_STRING);
		$answers = filter_var(trim($_POST['answers']), FILTER_SANITIZE_STRING);
		echo getUserID($conn, $sid);
	}
	if ($msgtype === "create") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$content = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);
		$answers = filter_var(trim($_POST['answers']), FILTER_SANITIZE_STRING);
		$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
		$groupid = filter_var(trim($_POST['groupid']), FILTER_SANITIZE_STRING);
		$uid = filter_var(trim($_POST['uid']), FILTER_SANITIZE_STRING);
		echo CreateTest($conn, $sid, $content, $answers, $name, $groupid, $uid);
	}
	$conn->close();

?>
