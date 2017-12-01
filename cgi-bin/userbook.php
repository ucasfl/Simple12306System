<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>用户订单</title>
</head>
<body>
<?php
//　显示管理员查询的用户订单结果
$username = $_GET["username"];
$id = $_GET["id"];
//echo "id = $id";
echo "<H3>用户 $username 的订单</H3>";
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}
$bookselect = <<<EOF
	SELECT *
	FROM book 
	WHERE b_userid = '$id';
EOF;
$ret = pg_query( $conn, $bookselect );
$row_num = pg_num_rows($ret);
//echo $row_num;
if ( $row_num == 0 ){
	echo "<p>该用户无任何订单记录。</p>";
}
else {
	//$all_record = pg_fetch_all($ret);
	//echo $all_record[0][0];
	//echo count($all_record);
	$status = array ("normal"=>"未乘坐", "cancelled"=>"已取消", "past"=>"已乘坐");
	//$notuse = "未乘坐";
	//$used   = "已乘坐";
	//$cancel = "已取消";
	//echo $row_num;
	$seat   = array("YZ"=>"硬座", "RZ"=>"软座", "YW1"=>"硬卧上", "YW2"=>"硬卧中", "YW3"=>"硬卧下", "RW1"=>"软卧上", "RW2"=>"软卧下");
	echo "<table border = \"4\">";
	echo "<tr>";
	echo "<th>订单ID</th>";
	echo "<th>用户ID</th>";
	echo "<th>列车号</th>";
	echo "<th>日期</th>";
	echo "<th>出发</th>";
	echo "<th>到达</th>";
	echo "<th>类型</th>";
	echo "<th>票价</th>";
	echo "<th>订单状态</th>";
	echo "</tr>";
	//$i = 0;
	while ($all_record = pg_fetch_row($ret)){
		$i = $i + 1;
		$sd1 = $all_record[4];
		$sd2 = $all_record[5];
		$trainid = $all_record[2];
		$sdname = <<<EOF
			 SELECT p_stationname
			 FROM passby 
			 WHERE p_stationnum = $sd1
			 AND p_trainid = '$trainid';
EOF;
		$ret1 = pg_query( $conn, $sdname );
		$station1 = pg_fetch_row($ret1);
		$station1name = $station1[0];
		$sdname = <<<EOF
			 SELECT p_stationname 
			 FROM passby 
			 WHERE p_stationnum = $sd2
			 AND p_trainid = '$trainid';
EOF;
		$ret1 = pg_query( $conn, $sdname );
		$station2 = pg_fetch_row($ret1);
		$station2name = $station2[0];

		echo "<tr>";
		for ( $j = 0; $j < 4; $j = $j + 1 )
			echo "<td>$all_record[$j]</td>";
		$se_index = $all_record[6];
		$st_index = $all_record[8];
		echo "<td>$station1name</td>";
		echo "<td>$station2name</td>"; // 该处使用的关联数组可能存在问题
		echo "<td>$seat[$se_index]</td>";
		echo "<td>$all_record[7]</td>";
		echo "<td>$status[$st_index]</td>";
		echo "</tr>";
		//$ret = pg_query( $conn, $bookselect );
		//for ($j = 0; $j <= $i; $j++)
		//    $all_record = pg_fetch_row($ret);
	}
	echo "</table>";
}
pg_close($conn);
?>
</body>
</html>
