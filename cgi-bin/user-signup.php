<!doctype html>
<html>
<head>
<title>用户注册</title>
</head>
<body>
<?php 
// 用户注册
$name = $_POST["name"];
$username = $_POST["username"];
$user_id = $_POST["man-number"];
$phone = $_POST["usertel"];
$card = $_POST["card"];
// 检查输入是否合法
$uid_len = strlen("$user_id");
$phone_len = strlen("$phone");
$card_len = strlen("$card");
$url = "../sign/sign-up.php";

if ($uid_len != 18){
	echo "<center>";
	echo "身份证号必须为 18 位，请<a href = $url>返回</a>重新输入";
	echo "</center>";
}

elseif($phone_len != 11){
	echo "<center>";
	echo "手机号必须为 11 位，请<a href = $url>返回</a>重新输入";
	echo "</center>";
}

elseif($card_len != 16){
	echo "<center>";
	echo "信用卡号必须为 16 位，请<a href = $url>返回</a>重新输入";
	echo "</center>";
}
//输入均合法，检查是否有已存在的信息
else{
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}
//echo $username;
//echo "<br>";
$sql =<<<EOF
		SELECT u_uname 
		FROM userinfo 
		WHERE u_uname='$username';
EOF;
$ret = pg_query($conn, $sql);
//$result = pg_fetch_all($ret);
$sum = pg_num_rows($ret);

$sql_uid = <<<EOF
			SELECT user_id 
			FROM userinfo
			WHERE user_id = '$user_id';
EOF;
$ret = pg_query($conn, $sql_uid);
$sum_uid = pg_num_rows($ret);

$sql_phone = <<<EOF
			SELECT u_phone
			FROM userinfo
			WHERE u_phone = '$phone';
EOF;
$ret = pg_query($conn, $sql_phone);
$sum_phone = pg_num_rows($ret);
//echo $sum;
//用户名已经存在
if ($sum || $username == "Admi"){
	$href = "../index.php";
	echo "<center>";
	echo "该用户名已存在，请" . "<a href=$href>返回</a>登录/注册。"; 
	echo "</center>";
}
//身份证号已被注册
elseif ($sum_uid){
	$href = "../index.php";
	echo "<center>";
	echo "该身份证号已注册，请" . "<a href=$href>返回</a>登录/注册。"; 
	echo "</center>";

}
//手机号已经被注册
elseif($sum_phone){
	$href = "../index.php";
	echo "<center>";
	echo "该手机号已被注册，请" . "<a href=$href>返回</a>登录/注册。"; 
	echo "</center>";

}
//输入正确
else{
//$name = $_POST["name"];
//$username = $_POST["username"];
//$user_id = $_POST["man-number"];
//$phone = $_POST["usertel"];
//$card = $_POST["card"];
	$ins = <<<EOF
		INSERT INTO 
		userinfo(user_id,u_name,u_phone, u_uname, u_creditcardid) 
		VALUES ('$user_id', '$name', '$phone', '$username', '$card');
EOF;
	$insert = pg_query($conn, $ins);
	if (!$insert){
		echo "<center>";
		echo "注册用户失败。";
		echo "</center>";
	}
	else {
		$login = "../sign/sign-in.php";
		echo "<center>";
		echo "用户注册成功。" . "请<a href = $login>登录</a>。";
		echo "</center>";
	}
}
//pg_close($conn);
}
?>
</body>
