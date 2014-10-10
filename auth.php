<?php
require_once('./config.php');
//用session存储认证信息
session_start();

if(empty($_POST) && !isset($_SESSION['auth'])){
	showlogin('请输入登陆口令!');
	exit();
}

if(@$_POST['pwd'] == $pwd || $_SESSION['auth'] == 'login'){
	$_SESSION['auth']='login';
}else{
	showlogin('口令错误');
	session_destroy();
	exit();
}

function showlogin($note){
	$r = <<<EOT
<!DOCTYPE HTML>
<html lang="zh-cn">
<head>
<meta charset="UTF-8">
<title>登陆</title>
</head>
<body>
<form action="" method="post">	
<label>$note</label>
<input type="password" name="pwd"/>
<input type="submit" name="提交"/>
</form>
</body>
</html>
EOT;
	echo $r;
}
