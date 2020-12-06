<?php

	function createNewSession($conn, $uid, $ip, &$sid) {
		$sid = hash('sha256', rand(1,10000000000000)).hash('sha256', rand(1,100000000000000));
		$sql = "INSERT INTO sessions (sid, uid, ip) VALUES ('".$sid."', '".$uid."', '".$ip."')";
		return $conn->query($sql);
	}

	function loginUser($conn, $login, $pass) {
		$sql = "SELECT id, password FROM users WHERE login='".$login."' AND password='".$pass."'";
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			$data = $rez->fetch_assoc();
			$sid = 0;
			if (createNewSession($conn, $data["id"], $_SERVER['REMOTE_ADDR'], $sid) === TRUE) {
				$ret->status = 1;
				$ret->sid = $sid;
				return json_encode($ret);
			} else {
				$ret->status = 0;
				$ret->error = "Session creation error";
				return json_encode($ret);
			}
		}
		$ret->status = 0;
		$ret->error = "Incorrect username or password";
		return json_encode($ret);
	}

	function isUserExists($conn, $login) {
		$sql = "SELECT id FROM users WHERE login='".$login."'";
		return !($conn->query($sql)->num_rows === 0);
	}

	function registerUser($conn, $login, $pass, $name) {
		if (!isUserExists($conn, $login)) {
			$sql = "INSERT INTO users (login, password, name) VALUES ('".$login."', '".$pass."', '".$name."')";
			if ($conn->query($sql) === TRUE) {
				return loginUser($conn, $login, $pass);
			}
		}
		$ret->status = 0;
		$ret->error = "User already exists";
		return json_encode($ret);
	}

	function getStatus($conn, $sid) {
		$sql = "SELECT sid FROM sessions WHERE sid='".$sid."'";
		$rez = $conn->query($sql);
		if ($rez->num_rows > 0) {
			$ret->status = 1;
			return json_encode($ret);
		} else {
			$ret->status = 0;
			return json_encode($ret);
		}
	}

	function logoffUser($conn, $sid) {
		$sql = "DELETE FROM sessions WHERE sid='".$sid."'";
		if ($conn->query($sql) === TRUE) {
			$ret->status = 1;
			return json_encode($ret);
		} else {
			$ret->status = 0;
			$ret->error = "Token is incorrect";
			return json_encode($ret);
		}
	}

	require 'connect.php';
	$msgtype = filter_var(trim($_GET['type']), FILTER_SANITIZE_STRING);
	$conn = connectDefault();
	if (!isset($conn)) {
		$ret->status = 0;
		$ret->reason = "Internal Server Error";
		echo json_encode($ret);
		die();
	}
	if ($msgtype === "login") {
		$ulogin = filter_var(trim($_GET['login']), FILTER_SANITIZE_STRING);
		$upass = filter_var(trim($_GET['pass']), FILTER_SANITIZE_STRING);
		echo loginUser($conn, $ulogin, $upass);
	}
	if ($msgtype === "register") {
		$ulogin = filter_var(trim($_GET['login']), FILTER_SANITIZE_STRING);
		$upass = filter_var(trim($_GET['pass']), FILTER_SANITIZE_STRING);
		$uname = filter_var(trim($_GET['name']), FILTER_SANITIZE_STRING);
		echo registerUser($conn, $ulogin, $upass, $uname);
	}
	if ($msgtype === "status") {
		$usid = filter_var(trim($_GET['sid']), FILTER_SANITIZE_STRING);
		echo getStatus($conn, $usid);
	}
	if ($msgtype === "logoff") {
		$usid = filter_var(trim($_GET['sid']), FILTER_SANITIZE_STRING);
		echo logoffUser($conn, $usid);
	}
	$conn->close;
?>