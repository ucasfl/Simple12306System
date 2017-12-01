<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset="utf-8"/>
<title>用户登录</title>
</head>
<body>
<?php 
// 用户登录
$username = $_POST["name"];
//echo $username;
//echo "<br>";
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}

$sql = <<<EOF
	SELECT u_uname FROM userinfo WHERE u_uname = '$username';
EOF;
$ret = pg_query($conn, $sql);
//$result = pg_fetch_all($ret);
$sum = pg_num_rows($ret);
//echo $sum;
//echo "<br>";
//echo $result[0];
//echo "<br>";
if (!$sum){
	echo "<center>";
	$local_href = "../index.php";
	echo "用户不存在！" . "请<a href = $local_href>返回</a>重新登录/注册。";
	echo "</center>";
	session_destroy();
}
else{
	$_SESSION["name"] = $_POST["name"];
	//echo $_SESSION["name"];
	$trainS_href = "../serve/train-search.php";
	$distS_href  = "../serve/dist-search.php";
	$bookS_href  = "../serve/book.php";
	//echo "<center>";
	echo "尊敬的乘客$username" . " ,欢迎访问12306在线购票系统，我们愿竭诚为您服务！";
	echo "<br>";
	echo "<br>";
	echo "请在下面点击选择您需要的服务：";
    echo "<br>";
	echo "<ul>";
	echo "<li><a href = $trainS_href>查询具体车次</a></li>";
	echo "<li><a href = $distS_href>查询两地间车次</a></li>";
	echo "<li><a href = $bookS_href>订单查询</a></li>";
	echo "</ul>";
	//echo "</center>";
}
pg_close($conn);
?>
</body>
</html>
