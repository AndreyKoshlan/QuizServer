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
		if (CheckSid($conn, $sid)) {
			$sql = "SELECT content FROM tests WHERE testid = '".$testid."'";
			$rez = $conn->query($sql);
			if ($rez->num_rows > 0) { 
				$row = $rez->fetch_assoc();
				$ret->status = 1;
				$ret->content = $row["content"];
			} else {
				$ret->status = 0;
				$ret->error = "Test not found";
			}
		} else {
			$ret->status = 0;
			$ret->error = "Token is incorrect";
		}
		return json_encode($ret);
	}

function GetTest($conn, $testid, $sid) {
	$sql = "SELECT uid FROM sessions WHERE sid = '".$sid."'";
	$rez = $conn->query($sql);
	if ($rez->num_rows > 0) {
		$row = $rez->fetch_assoc();
		$uid = $row["uid"];
		$sql = "SELECT name, groupid, content, answers FROM tests WHERE testid = '".$testid."' AND uid = '".$uid."'";
		$taskrez = $conn->query($sql);
			if ($taskrez->num_rows > 0) { 
				$row = $taskrez->fetch_assoc();
				$ret->status = 1;
				$ret->name = $row["name"];
				$ret->groupid = $row["groupid"];
				$ret->content = $row["content"];
				$ret->answers = $row["answers"];
			} else {
				$ret->status = 0;
				$ret->error = "Test not found";
			}
	} else {
		$ret->status = 0;
		$ret->error = "Token is incorrect";
	}
	return json_encode($ret);
}
	
function CreateTest($conn, $sid, $content, $answers, $name, $groupid) {
		$sql = "SELECT uid FROM sessions WHERE sid = '".$sid."'";
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			$row = $rez->fetch_assoc();
			$uid = $row["uid"];
			$sql = "INSERT INTO tests (content, answers, name, groupid, uid) VALUES ('".$content."', '".$answers."', '".$name."', '".$groupid."', '".$uid."')";
			if ($conn->query($sql) === TRUE) {
				$ret->status = 1;
			} else {
				$ret->status = 0;
				$ret->error = "Can''t create test";
			}
		} else {
			$ret->status = 0;
			$ret->error = "Token is incorrect";
		}
		return json_encode($ret);
}

function ChangeTest($conn, $testid, $sid, $content, $answers, $name, $groupid) {
	$sql = "SELECT uid FROM sessions WHERE sid = '".$sid."'";
	$rez = $conn->query($sql);
	if ($rez->num_rows > 0) {
		$row = $rez->fetch_assoc();
		$uid = $row["uid"];
		$sql = "SELECT testid FROM tests WHERE testid = '".$testid."' AND uid = '".$uid."'";
		$taskrez = $conn->query($sql);
		if ($taskrez->num_rows > 0) { 
			$sql = "UPDATE tests SET content='".$content."', answers='".$answers."', name='".$name."', groupid='".$groupid."', uid='".$uid."' WHERE testid = '".$testid."'";
			if ($conn->query($sql) === TRUE) {
				$ret->status = 1;
			} else {
				$ret->status = 0;
				$ret->error = "Can''t update test";
			}
		} else {
			$ret->status = 0;
			$ret->error = "Test not found";
		}
	} else {
		$ret->status = 0;
		$ret->error = "Token is incorrect";
	}
	return json_encode($ret);
}

function CompareAnswers($client, $server) {
	$jc = json_decode($client, true);
	$js = json_decode($server, true);
	$points = 0;
	$questions = 0;
	for ($i = 0; $i < count($js["pages"]); $i++) {
		$questions++;
		if ($jc["pages"] === $js["pages"]) {
			$points++;
		}
	}
	$ret->status = 1;
	$ret->result->correct = $points;
	$ret->result->count = $questions;
	if ($questions === 0) {
		$ret->result->ratio = 1;
	} else {
		$ret->result->ratio = $points/$questions;
	}
	$ret->result->percent = strval($ret->result->ratio*100)."%";
	return json_encode($ret);
}

function FinishTest($conn, $sid, $testid, $answers) {
	if (CheckSid($conn, $sid)) {
		$sql = "SELECT answers FROM tests WHERE testid = '".$testid."'";
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			$row = $rez->fetch_assoc();
			return CompareAnswers($answers, $row["answers"]);
		} else {
			$ret->status = 0;
			$ret->error = "Test not found";
		}
	} else {
		$ret->status = 0;
		$ret->error = "Token is incorrect";
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
	if ($msgtype === "start") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$testid = filter_var(trim($_POST['testid']), FILTER_SANITIZE_STRING);
		echo StartTest($conn, $testid, $sid);
	}
	if ($msgtype === "finish") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$testid = filter_var(trim($_POST['testid']), FILTER_SANITIZE_STRING);
		$answers = filter_var(trim($_POST['answers']), FILTER_SANITIZE_STRING);
		echo FinishTest($conn, $sid, $testid, $answers);
	}
	if ($msgtype === "create") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$content = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);
		$answers = filter_var(trim($_POST['answers']), FILTER_SANITIZE_STRING);
		$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
		$groupid = filter_var(trim($_POST['groupid']), FILTER_SANITIZE_STRING);
		echo CreateTest($conn, $sid, $content, $answers, $name, $groupid);
	}
	if ($msgtype === "change") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$content = filter_var(trim($_POST['content']), FILTER_SANITIZE_STRING);
		$answers = filter_var(trim($_POST['answers']), FILTER_SANITIZE_STRING);
		$name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
		$groupid = filter_var(trim($_POST['groupid']), FILTER_SANITIZE_STRING);
		$testid = filter_var(trim($_POST['testid']), FILTER_SANITIZE_STRING);
		echo ChangeTest($conn, $testid, $sid, $content, $answers, $name, $groupid);
	}
	if ($msgtype === "get") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$testid = filter_var(trim($_POST['testid']), FILTER_SANITIZE_STRING);
		echo GetTest($conn, $testid, $sid);
	}
	$conn->close();

?>
