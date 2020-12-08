<?php
  
  /*
  	main - общедоступные тесты
    my - свои тесты
    крч тут не так будет. отдельно проверяй если groupid === "my", тогда выбирай все testid, где uid = id_пользователя я пошёл кушац красава
  */
  
	function getUserID($conn, $user_sid) {
  	$sql = "SELECT uid FROM sessions WHERE sid = '".$user_sid."'";
    $rez = $conn->query($sql);
    if ($rez->num_rows > 0) {
      $row = $rez->fetch_assoc();
      return $row["uid"];
    }
	}
  
	function getGroups($conn, $sid) {
  	$sql = "SELECT groupid, name FROM groups";
    $rez = $conn->query($sql);
    if ($rez->num_rows > 0) {
    	$ret->status = 1;
      while ($row = $rez->fetch_assoc()) {
      	$group->groupid = $row["id"];
        $group->name = $row["name"];
        $ret->groups[] = $group;
      }
      if (isset($sid)) {
        $mygroup->groupid = "my";
        $mygroup->name = "Мои тесты";
        $ret->groups[] = $mygroup;
    	}
    } else {
      $ret->status = 0;
      $ret->error = "Group search error";
    }
    return json_encode($ret);
	}

	function getListGroupCustom($conn, $rows) {
		if ($rows->num_rows > 0) {
      $ret->status = 1;
      while ($row = $rows->fetch_assoc()) {
      	$test->testid = $row["testid"];
        $test->name = $row["name"];
        $test->uid = $row["uid"];
        $ret->tests[] = $test;
      }
		} else {
			$ret->status = 0;
      $ret->error = "Tests search error";
		}
		return json_encode($ret);
  }

	function getListGroupMy($conn, $uid) {
    if (!isset($uid)) {
      $ret->error = "Token is incorrect";
    	$ret->status = 0;
      return json_encode($ret);
    }
  	$sql = "SELECT testid, name, uid FROM tests WHERE uid = '".$uid."'";
		$result = $conn->query($sql);
    getListGroupCustom($conn, $result);
  }

	function getListGroupByName($conn, $groupid) {
  	$sql = "SELECT testid, name, uid FROM tests WHERE groupid = '".$groupid."'";
		$result = $conn->query($sql);
		getListGroupCustom($conn, $result);
  }

	function getList($conn, $sid, $groupid) {
    $uid = getUserID($conn, $sid);
    if ($groupid === "my") {
  		return getListGroupMy($conn, $uid);
    } else {
    	return getListGroupByName($conn, $groupid);
    }
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
	if ($msgtype === "groups") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		echo getGroups($conn, $sid);
	}
	if ($msgtype === "list") {
		$sid = filter_var(trim($_POST['sid']), FILTER_SANITIZE_STRING);
		$groupid = filter_var(trim($_POST['groupid']), FILTER_SANITIZE_STRING);
		echo getTaskList($conn, $sid, $groupid);
	}
	$conn->close();
?>