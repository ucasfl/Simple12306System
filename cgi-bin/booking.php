<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset = "utf-8">
<title>预定</title>
</head>
	<body>
<?php
$username = $_SESSION["name"];
//echo $username;
$trainid = $_GET["trainid"];
//echo $trainid . " ";
$date = $_GET["date"];
//echo $date ." ";
//$num = $_GET["num"];
//echo $num ." ";
$type = $_GET["type"];
//echo $type . " ";
$ticketprice = $_GET["price"];
//echo $ticketprice;
$first = $_GET["fromstation"];
$last = $_GET["tostation"];
//echo $last;
//echo $price . " "
switch ($type){
case "YZ":
	$seat = "硬座";
	break;
case "RZ":
	$seat = "软座";
	break;
case "YW1":
	$seat = "硬卧上";
	break;
case "YW2":
	$seat = "硬卧中";
	break;
case "YW3":
	$seat = "硬卧下";
	break;
case "RW1":
	$seat = "软卧上";
	break;
case "RW2":
	$seat = "软卧下";
	break;
}
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}
$getfrom = <<<EOF
			SELECT p_stationname
			FROM passby
			WHERE p_trainid = '$trainid'
			 AND  p_stationnum = $first;
EOF;
$ret = pg_query($conn, $getfrom);
$row = pg_fetch_row($ret);
$fromname = $row[0];
$getto = <<<EOF
			SELECT p_stationname
			FROM passby
			WHERE p_trainid = '$trainid'
			 AND  p_stationnum = $last;
EOF;
//echo $trainid;
$ret = pg_query($conn, $getto);
$row = pg_fetch_row($ret);
$toname = $row[0];
//echo $last;
//echo $toname;
$gettime = <<<EOF
			SELECT p_gotime
			FROM passby
			WHERE p_trainid = '$trainid'
			 AND  p_stationnum = 1;
EOF;
$ret = pg_query($conn, $gettime);
$row = pg_fetch_row($ret);
$gotime = $row[0];
$price = $ticketprice + 5;
//echo $price;
echo "<p>您即将预定 $date   $gotime ，从 $fromname 到 $toname 的 $trainid 次列车的 $seat 票 一张，票价为 $price (包含5元手续费) 。点击下方确认生成订单，取消返回登录首页。</p>";

echo "<center>";
echo "<input type=button value=\"确认\" onclick=\"window.location.href='confirm.php?&gotime=$gotime&fromname=$fromname&toname=$toname&seat=$seat&trainid=$trainid&date=$date&from=$first&to=$last&type=$type&money=$price'\">";
echo "   ";
echo "<input type=button value = \"取消\" onclick = \"window.location.href='../index.php'\">";
echo "</center>";
pg_close();
?>

</body>
</html>
