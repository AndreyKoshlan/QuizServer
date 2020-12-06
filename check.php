<?php
	$login = filter_var(trim($_POST['login']), FILTER_SANITIZE_STRING);
	$pass = filter_var(trim($_POST['pass']), FILTER_SANITIZE_STRING);
	
	if(mb_strlen($login) < 5 || mb_strlen($login) > 90){
		echo "Недопустимая длина логина";
		exit();
	} else if(mb_strlen($pass) < 4 || mb_strlen($pass) > 50){
		echo "Недопустимая длина пароля (от 4 символов)";
		exit();
	}
	
	$mysql = new mysqli('localhost', 'root', '', 'registers');
	$mysql->query("INSERT INTO `users` (`login`, `pass`) VALUES('$login', '$pass')");
	
	$mysql->close();
?>