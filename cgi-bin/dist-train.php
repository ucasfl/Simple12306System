<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset = "utf-8">
<title>两地间查询</title>
</head>
	<body>
<?php
$username = $_SESSION["name"];
$from = $_POST["from"];
$to = $_POST["to"];
$date = $_POST["date"];
$inputtime = $_POST["time"];
//echo $to;
//echo $from;
$tomorrow = date("Y-m-d", strtotime("+1 day"));
if ($date){
	$thedate = $date;
}
else {
	$thedate = $tomorrow;
}

if ($inputime){
	$time = $inputtime;
}
else{
	$time = "00:00:00";
}
$conn = pg_connect("host=localhost port=5432 dbname=train user=fenglv password=nopasswd");
if (!$conn){
	echo "连接失败";
}

$query_train = <<<EOF
with S1(S1_TrainId, S1_StationNum) as 
(select Passby.P_TrainId, Passby.P_StationNum
    from Passby, Station
    where Passby.P_StationName = Station.S_Name
        and Station.S_City = '$from'),

S2(S2_TrainId,S2_StationNum) as
(select Passby.P_TrainId, Passby.P_StationNum
    from Passby, Station
    where Passby.P_StationName = Station.S_Name
        and Station.S_City = '$to'),

T1(T1_TrainId) as
(   select S1.S1_TrainId
    from S1, S2
    where S1.S1_TrainId = S2.S2_TrainId
        and S1.S1_StationNum < S2.S2_StationNum
    ),

T3(Tp_trainid, Tp_stationname, Tp_stationnum, Tp_arrivetime, Tp_gotime, Tp_moneyyz, Tp_moneyrz, Tp_moneyyw1, Tp_moneyyw2, Tp_moneyyw3, Tp_moneyrw1, Tp_moneyrw2, Tt1_trainid, Ts_name, s_city)  as
    (select *
    from Passby, T1, Station
    where Passby.P_TrainId = T1.T1_TrainId
        and Passby.P_StationName = Station.S_Name
        and (Station.S_City = '$from'
            or Station.S_City = '$to')),

T4(T4_id,  yz, rz, yw1, yw2, yw3, rw1, rw2) as
(
select Tp_trainid, Max(Tp_moneyyz)-Min(Tp_moneyyz), Max(Tp_moneyrz)-Min(Tp_moneyrz), Max(Tp_moneyyw1)-Min(Tp_moneyyw1), Max(Tp_moneyyw2)-Min(Tp_moneyyw2), Max(Tp_moneyyw3)-Min(Tp_moneyyw3), Max(Tp_moneyrw1)-Min(Tp_moneyrw1), Max(Tp_moneyrw2)-Min(Tp_moneyrw2)
from T3
group by Tp_trainid
),

T5(T5_id,T5_StationName, T5_GoTime, T5_StationNum) as
(
select T1.T1_TrainId, Passby.P_StationName, Passby.P_GoTime, Passby.P_StationNum
from T1, Passby, Station
where P_TrainId = T1_TrainId
    and P_StationName = Station.S_Name
    and Station.S_City = '$from'
),

T6(T6_id, T6_StationName, T6_ArriveTime, T6_StationNum) as
(
select T1.T1_TrainId, Passby.P_StationName, Passby.P_ArriveTime, Passby.P_StationNum
from T1, Passby, Station
where P_TrainId = T1_TrainId
    and P_StationName = Station.S_Name
    and Station.S_City = '$to'
)

-- order by yz;
select DISTINCT T4.T4_id,T5.T5_StationName, T5.T5_GoTime, T6.T6_StationName,
T6.T6_ArriveTime,  T4.yz, T4.rz, T4.yw1, T4.yw2, T4.yw3, T4.rw1, T4.rw2, T5.T5_StationNum, T6.T6_StationNum
from T4, T5, T6
where T4.T4_id = T5.T5_id
    and T6.T6_id = T5.T5_id
    and T5_GoTime > '$time'
order by T4.yz
;
EOF;

$ret_train = pg_query($conn, $query_train);
if (!$ret_train){
	echo "执行失败";
}
$row_num = pg_num_rows($ret_train);
//echo "row_num = $row_num  ";

echo "车次情况如下：<br>";
echo "<table border=\"4\">";
echo "<tr>";
echo "<td>车次</td>" ;
echo "<td>出发站</td>" ;
echo "<td>出发时间</td>" ;
echo "<td>到达站</td>" ;
echo "<td>到达时间</td>";
echo "<td>硬座</td>" ;
echo "<td>软座</td>" ; 
echo "<td>硬卧上铺</td>";
echo "<td>硬卧中铺</td>" ;
echo "<td>硬卧下铺</td>" ;
echo "<td>软卧上铺</td>" ;
echo "<td>软卧下铺</td>";
echo "</tr>";


for ($x = 0; $x < min($row_num, 10); $x++){
    $a_row = pg_fetch_row($ret_train);
    echo "<tr>";
    for ($y = 0; $y < 12; $y++){
        echo "<td>$a_row[$y]</td>";
    }
    echo "</tr>";
	//获取站序

//$get_station_num = <<<EOF
//                SELECT COUNT(p_trainid)
//                FROM passby
//                WHERE p_trainid = '$a_row[0]';
//EOF;
//$ret = pg_query($conn, $get_station_num);
//$station_num = pg_fetch_row($ret);
//$sta_num = $station_num[0];

//$get_price = <<<EOF
//            SELECT *
//            FROM passby
//            WHERE p_trainid = '$a_row[0]'
//            AND   p_stationnum = $sta_num;
//EOF;

//$ret = pg_query($conn, $get_price);
//$price = pg_fetch_row($ret);
////$hastype = array(0, 0, 0, 0, 0, 0, 0);
$hastype = array($a_row[5], $a_row[6], $a_row[7],
 $a_row[8], $a_row[9], $a_row[10], $a_row[11]);
$get_booked_ticket = <<<EOF
				with T1(T1_Type, T1_SeatNum) as
				(SELECT T_Type, T_SeatNum
				 FROM TicketInfo 
				 WHERE T_TrainId = '$a_row[0]'
					AND T_PStationNum >= $a_row[12]
					AND T_PStationNum < $a_row[13]
					AND T_Date = '$thedate')
				
				SELECT T1_Type, MAX(T1_SeatNum)
				FROM T1
				GROUP BY T1_Type;
EOF;
$ret = pg_query($conn, $get_booked_ticket);
if (!$ret){
	echo "执行失败";
}
$all_type = array("YZ", "RZ", "YW1", "YW2", "YW3", "RW1", "RW2");

$left_num = array(0, 0, 0, 0, 0, 0, 0);

for ($i = 0; $i < 7; $i = $i + 1){
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

echo "<tr>";
echo "<td></td><td></td><td></td><td></td><td></td>";
for ($i = 0; $i <7; $i = $i + 1){
	if ($left_num[$i] == -1)
		echo "<td> - </td>";
	elseif( $left_num[$i] == 0 )
		echo "<td>0</td>";
	else{
		//$k = $i + 5;
		//echo $price[$k] . "   ";
		echo "<td><a href=\"booking.php?trainid=$a_row[0]&date=$thedate&type=$all_type[$i]&price=$hastype[$i]&fromstation=$a_row[12]&tostation=$a_row[13]\">$left_num[$i]</a></td>";

	}
}
echo "</tr>";
    // left tickets
    // $a_row[0] is $train_id
    // $from
    // $to
    // $date
}
echo "</table>";
?>
</body>
</html>
