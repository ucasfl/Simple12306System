<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset="utf-8"/>
<title>车次查询</title>
<body>
<?php
// 具体车次查询
//echo $_SESSION["name"];
$tomorrow = date("Y-m-d", strtotime("+1 day"));
//echo $tomorrow;
$trainid = $_POST["trainid"];
$inputdate = $_POST["date"];
//echo $date;
//获取查询日期，若用户不输入，则默认为明天
if ($inputdate){
	$thedate = $inputdate;
}
else{
	$thedate = $tomorrow;
}
//echo $thedate;

$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}
$sql = <<<EOF
	SELECT * FROM passby WHERE p_trainid = '$trainid';
EOF;
$ret = pg_query($conn, $sql);
echo "车次情况如下：<br>";
echo "<table border=\"4\">";
echo "<tr>";
echo "<td>车次</td>" ;
echo "<td>站名</td>" ;
echo "<td>站次</td>" ;
echo "<td>到达时间</td>";
echo "<td>出发时间</td>" ;
echo "<td>硬座</td>" ;
echo "<td>软座</td>" ; 
echo "<td>硬卧上铺</td>";
echo "<td>硬卧中铺</td>" ;
echo "<td>硬卧下铺</td>" ;
echo "<td>软卧上铺</td>" ;
echo "<td>软卧下铺</td>";
echo "</tr>";
while ($row = pg_fetch_row($ret)){
	////echo count($row);
	$num = count($row);
	echo "<tr>";
	for ( $i = 0; $i < $num; $i = $i + 1 ){
		echo "<td>" . "$row[$i]" . "</td>";
	}
	echo "</tr>";
}
echo "</table>";


//获取总站数
$get_station_num = <<<EOF
				SELECT COUNT(p_trainid)
				FROM passby
				WHERE p_trainid = '$trainid';
EOF;
$ret = pg_query($conn, $get_station_num);
$station_num = pg_fetch_row($ret);
$sta_num = $station_num[0];

$get_first = <<<EOF
				SELECT p_stationname
				FROM passby
				WHERE p_trainid = '$trainid'
				AND p_stationnum = 1;
EOF;
$ret1 = pg_query($conn, $get_first);
$row = pg_fetch_row($ret1);
$first_name = $row[0];
$get_last = <<<EOF
				SELECT p_stationname
				FROM passby
				WHERE p_trainid = '$trainid'
				AND p_stationnum = $sta_num;
EOF;
$ret1 = pg_query($conn, $get_last);
$row = pg_fetch_row($ret1);
$last_name = $row[0];

echo "<br>";
echo "<table border=\"4\">";
echo "<tr>";
echo "<td>始发站</td>";
echo "<td>$first_name</td>";
echo "<td>终点站</td>";
echo "<td>$last_name</td>";
echo "</tr>";
echo "</table>";

$get_price = <<<EOF
			SELECT *
			FROM passby
			WHERE p_trainid = '$trainid' 
			AND   p_stationnum = $sta_num;
EOF;

$ret = pg_query($conn, $get_price);
$hastype = array();
$price = pg_fetch_row($ret);
//$hastype = array(0, 0, 0, 0, 0, 0, 0);
$hastype = array($price[5], $price[6], $price[7],
 $price[8], $price[9], $price[10], $price[11]);
//echo count($hastype);
//echo "rz = $price[6]";
//if (!$price[6])
//    echo "无软座";
//else echo "有软座";
////echo "station num = $station_num[0]";
//获取余票信息
$get_booked_ticket = <<<EOF
				with T1(T1_Type, T1_SeatNum) as
				(SELECT T_Type, T_SeatNum
				 FROM TicketInfo 
				 WHERE T_TrainId = '$trainid'
					AND T_PStationNum >= 1
					AND T_PStationNum < $sta_num
					AND T_Date = '$thedate')
				
				SELECT T1_Type, MAX(T1_SeatNum)
				FROM T1
				GROUP BY T1_Type;
EOF;
$ret = pg_query($conn, $get_booked_ticket);
if (!$ret){
	echo "执行失败";
}
//$test_array=array(array(1,2), array(3, 4), array(5, 6));
//echo "test_array = " . count($test_array);
//else{
//    echo "执行成功";
//}
//$first = pg_fetch_row($ret);
//echo "$column_name[0], $column_name[1]<br>";
//$all_result = pg_fetch_all($ret);
//$colum = pg_num_rows($ret);
//echo "colum = $colum";
$all_type = array("YZ", "RZ", "YW1", "YW2", "YW3", "RW1", "RW2");

$left_num = array(0, 0, 0, 0, 0, 0, 0);

for ($i = 0; $i < 7; $i = $i + 1){
	//for ($j = 0; $j < count($all_result); $j = $j + 1){
	//    //if ($j == $i && )
	//        $booked = $all_result[$i][$j];
	//}
	if (!$hastype[$i])
		$left_num[$i] = -1;//不存在
	else
		$left_num[$i] = 5;
}
//计算余票
while ($row = pg_fetch_row($ret)){
	for ($i = 0; $i <7; $i = $i + 1){
		if ($row[0] == $all_type[$i])
			$left_num[$i] = 5 - $row[1];
	}
}

echo "<p>$thedate ，列车 $trainid 的余票信息如下</p>";
echo "<table border = \"4\">";
echo "<tr>";
echo "<td>硬座</td>";
echo "<td>软座</td>";
echo "<td>硬卧上</td>";
echo "<td>硬卧中</td>";
echo "<td>硬卧下</td>";
echo "<td>软卧上</td>";
echo "<td>软卧下</td>";
echo "</tr>";
echo "<tr>";
for ($i = 0; $i <7; $i = $i + 1){
	if ($left_num[$i] == -1)
		echo "<td> - </td>";
	elseif( $left_num[$i] == 0 )
		echo "<td>0</td>";
	else{
		$k = $i + 5;
		echo "<td><a href=\"booking.php?trainid=$trainid&date=$thedate&type=$all_type[$i]&price=$price[$k]&fromstation=1&tostation=$sta_num\">$left_num[$i]</a></td>";

	}
}
echo "</tr>";
echo "</table>";
pg_close($conn);
?>
</body>
</html>
