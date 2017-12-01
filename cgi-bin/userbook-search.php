<!doctype html>
<?php session_start(); ?>
<html>
<head>
<meta charset="utf-8">
<title>用户订单查询结果</title>
</head>
<body>
<?php
//显示用户订单的查询结果
//echo "hello, world";
$username = $_SESSION["name"];
$date_min = $_POST["datemin"];
$date_max = $_POST["datemax"];
//echo $date_max;
//echo $date_min;
echo "<H3>尊敬的用户 $username ，您的订单列表如下</H3>";
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}

$get_uid = <<<EOF
			SELECT user_id
			FROM userinfo
			WHERE u_uname = '$username';
EOF;
$ret = pg_query($conn, $get_uid);
$row = pg_fetch_row($ret);
$uid = $row[0];
$select_book = <<<EOF
			SELECT b_id 
			FROM book 
			WHERE b_date BETWEEN '$date_min' AND '$date_max' AND b_userid = '$uid' ORDER BY b_date DESC;
EOF;
$ret = pg_query($conn, $select_book);
if (!$ret){
	echo "查询失败！";
}
$row_num = pg_num_rows($ret);
if ($row_num == 0){
	echo "该用户无任何订单记录";
}
else{
	echo "<table border = \"4\"><tr><td>订单号</td><td>查看详细信息</td></tr>";
	while ($row = pg_fetch_row($ret)){
		echo "<tr><td>$row[0]</td><td><a href = \"bookinfo.php?bookid=$row[0]\">详细信息</td></tr>";
	}
	echo "</table>";
}
pg_close($conn);
//echo $username;
?>
</body>
</html>
