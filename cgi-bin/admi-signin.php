<!doctype html>
<html>
<head>
<meta charset = "utf-8">
<title>管理员登录</title>
</head>
	<body>
<?php
// 管理员登录
$adminame = $_POST["adminame"];
if ( $adminame == "Admi"){
	echo "<H3> 尊敬的管理员，欢迎登录12306系统！</H3>";
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}
$booknum = <<<EOF
			SELECT COUNT(*), SUM(b_money) FROM book;
EOF;
$ret = pg_query( $conn, $booknum );
if (!$ret){
	echo "执行失败";
}
$result = pg_fetch_row($ret);
$ticket_num = $result[0];
$money_num = $result[1];
if ($ticket_num == 0) {
	$money_num = 0;
}
//show total tickets number and total money
echo "<p>当前总订单数：$ticket_num .</p>";
echo "<p>当前总票价：$money_num .</p>";

//show hot train
$select_hot_train = <<<EOF
				SELECT b_trainid, COUNT(b_trainid) 
				FROM book 
				GROUP BY b_trainid ORDER BY  COUNT(b_trainid) DESC;
EOF;
$ret = pg_query( $conn, $select_hot_train );
$i = 0;
echo "<p>热门车次</p>";
echo "<table border=\"4\"><tr><th>列车号</th><th>订单数</th></tr>";
while ( $i < 10 && $row = pg_fetch_row($ret) ){
	echo "<tr><td>$row[0]</td><td>$row[1]</td></tr>";
	$i = $i + 1;
}
echo "</table>";

//show user info 
$select_user = <<<EOF
	SELECT * FROM userinfo;
EOF;
$ret = pg_query($conn, $select_user);
echo "<p>当前注册用户列表<p>";
echo "<table border = \"4\"><tr><th>用户ID</th><th>姓名</th><th>身份证号</th><th>电话号码</th><th>用户名</th><th>信用卡号</th><th>查看订单</th</tr>";
//}
while ( $row = pg_fetch_row($ret) ){
	$id = $row[0];
	$username = $row[3];
	echo "<tr>";
	for ($i = 0; $i < 2; $i = $i + 1){
		echo "<td>$row[$i]</td>";	
	}
	echo "<td>$id</td>";
	for ( $i = 2; $i < 5; $i = $i + 1 ){
		echo "<td>$row[$i]</td>";
	}
	echo "<td><a href = \"userbook.php?username=$username&id=$id\">订单</a></td>";
	echo "</tr>";
	}
echo "</table>";
pg_close($conn);
}

//not admi sign in
else {
	$href = "../index.php";
	echo "<center>";
	echo "您不是管理员，请<a href=$href>返回</a>重新登录/注册。";
	echo "</center>";
}
?>

	</body>
</html>
