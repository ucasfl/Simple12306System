<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset = "utf-8">
<title>订单查询</title>
<body>
<center>
<H1>用户订单查询</H1>
<p>请在下面输入日期范围：
<form action = "../cgi-bin/userbook-search.php" method = "post">
出发日期从<input type = "date" name = "datemin" required = "required">
到 <input type = "date" name = "datemax" required = "required">
  <input type = "submit" name = "submit" value = "查询">
</form>
<?php
//$username = $_SESSION["name"];
////echo $username;
//$_SESSION["username"] = $username;
////echo $_SESSION["username"];
?>
</center>
</body>
</html>
