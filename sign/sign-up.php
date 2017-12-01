<!doctype html>
<html>
	<head>
		<meta charset="utf-8"/>
		<title> 用户注册</title>
	</head>
	<body>
			<center>
			<H1>用户注册</H1>
			<span>
			<form method="post" action="../cgi-bin/user-signup.php">
				姓名<br>
				<input type="text" name="name" maxlength = 20 required="required"> <br>
				身份证号<br>
				<input type="text" name="man-number" maxlength=18 required="required" ><br>
				手机号<br>
				<input type="text" name="usertel" maxlength=11 required="required" ><br>
				信用卡<br>
				<input type="text" name="card" maxlength=16 required="required"><br>
				用户名<br>
				<input type="text" name="username" maxlength=20 required="required"><br>
			</span>
				<span>
					<br><input type="submit" name="sign-up" value="注册">
				</span>
			</form>
			</center>
	</body>
</html>
