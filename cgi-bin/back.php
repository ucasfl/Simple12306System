<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset ="utf-8">
<title>查询返程信息</title>
</head>
<body>
<center>
<H1>返程信息查询</H1>
<?php
$username = $_GET["username"];
$fromname = $_GET["fromname"];
$toname = $_GET["toname"];
$date = $_GET["date"];
$onemoreday = date("Y-m-d", strtotime("+1 day", strtotime("$date")));
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}
$get_city = <<<EOF
			SELECT s_city
			FROM station
			WHERE s_name = '$fromname';
EOF;
$ret = pg_query($conn, $get_city);
$row = pg_fetch_row($ret);
$fname = $row[0];
$get_city = <<<EOF
			SELECT s_city
			FROM station
			WHERE s_name = '$toname';
EOF;
$ret = pg_query($conn, $get_city);
$row = pg_fetch_row($ret);
$tname = $row[0];
echo "<form action=\"dist-train.php\" method=\"post\">";
echo "出发城市：<input type=\"text\" name = \"from\" value =\"$fname\">";
echo "到达城市：<input type=\"text\" name = \"to\" value=\"$tname\">";
echo "出发日期：<input type=\"date\" name = \"date\" value=\"$onemoreday\">";
echo "出发时间：<input type=\"time\" name=\"time\" value = \"00:00\">";
echo "<input type = \"submit\" value = \"提交\">";
echo "<br>";
?>
</center>
</body>
</html>
