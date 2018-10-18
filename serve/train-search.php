<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset="utf-8"/>
<title>具体车体查询</title>
<body>
<center>
<H1>请输入您需要查询的车次</H1>
<div>
	<form action="../cgi-bin/train.php" method="post">
　	 车次号：<input type="text" name = "trainid" required = "required">
	 日期：  <input type="date" name = "date" >
			 <input type="submit" name = "查询" value="查询" ><br>
</form>
</div>

</center>
</body>
</html>

