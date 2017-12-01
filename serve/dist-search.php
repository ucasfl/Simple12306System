<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset = "utf-8">
<title>两地间车次查询</title>
</head>
<body>
<center>
<H1>请输入查询信息</H1>
<form action="../cgi-bin/dist-train.php" method="post"> 
出发城市：<input type="text" name = "from" required = "required">
到达城市：<input type="text" name = "to" required = "required">
出发日期：<input type="date" name = "date" >
出发时间：<input type="time" name = "time" >
		  <input type="submit" name = "submit" value = "查询" ><br>
</form>
</center>
</body>
</html>
