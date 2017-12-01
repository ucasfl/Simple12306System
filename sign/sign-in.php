<!doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title> 用户登录</title>
	</head>
	<body>
		<center>
		<H1>用户登录</H1>
		<div>
			<form action="../cgi-bin/user-signin.php" method="post">
				普通用户登录<br>
			用户名：<input type="text" name="name" required="required">
			<input type="submit" name="登录" value="登录" ><br>
			</form>
			<form action="../cgi-bin/admi-signin.php" method="post">
			管理员登录<br>
			用户名：<input type="text" name="adminame" required="required">
			<input type="submit" name="登录" value="登录" ><br>
			</form>
		</div>
	</body>
		</center>
</html>
