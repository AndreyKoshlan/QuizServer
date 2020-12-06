<?php
	function isUserExists($conn, $login) {
		$sql = "SELECT id FROM users WHERE login='".$login."'";
		return ($conn->query($sql)->num_rows === 0);
	}

	function registerUser($login, $pass, $name) {
		$conn = connectDefault();
		if (!isset($conn)) {
			return 'DB FAIL';
		}
		if (isUserExists($conn, $login)) {
			$sql = "INSERT INTO users (login, password, name) VALUES ('".$login."', '".$pass."', '".$name."')";
			if ($conn->query($sql) === TRUE) {
				$ret->status = 1;
				$conn->close;
				return json_encode($ret);
			}
		}
		$ret->status = 0;
		$conn->close;
		return json_encode($ret);
	}

	require 'connect.php';
	$msgtype = filter_var(trim($_GET['type']), FILTER_SANITIZE_STRING);
	if ($msgtype === "register") {
		$ulogin = filter_var(trim($_GET['login']), FILTER_SANITIZE_STRING);
		$upass = filter_var(trim($_GET['pass']), FILTER_SANITIZE_STRING);
		$uname = filter_var(trim($_GET['name']), FILTER_SANITIZE_STRING);
		echo registerUser($ulogin, $upass, $uname);
	}
?>