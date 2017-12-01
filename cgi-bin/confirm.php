<!doctype html>
<?php session_start();?>
<html>
<head>
<meta charset = "utf-8">
<title>订单确定</title>
</head>
	<body>
<?php 
$username = $_SESSION["name"];
//echo $username;
$trainid = $_GET["trainid"];
$date = $_GET["date"];
$stnum1 = $_GET["from"];
$stnum2 = $_GET["to"];
$seattype = $_GET["type"];
$money = $_GET["money"];
$gotime =$_GET["gotime"];
$fromname = $_GET["fromname"];
$toname = $_GET["toname"];
$seat = $_GET["seat"];

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
$result = pg_fetch_row($ret);
$uid = $result[0];

$book = <<<EOF
		INSERT INTO 
    	book(B_UserId,B_TrainId,B_Date,B_StationNum1,B_StationNum2,B_SType,B_Money,B_Status)
	    VALUES('$uid', '$trainid', '$date', $stnum1, $stnum2, '$seattype', $money, 'normal');
EOF;
$ins = pg_query($conn, $book);
if($ins){
	echo "您已成功预定 $date $gotime ，从 $fromname 到 $toname 的 $trainid 次列车的 $seat 票一张，票价为 $money (包含5元手续费)。";
}
else{
	echo "预定失败!!!";
}
//echo $uid;
//echo $date;
//echo $seattype;
//echo $money;
for ($x=$stnum1; $x<$stnum2; $x++) {
	//echo $x . "  ";
    $query_seat_num = <<<EOF
        select T_SeatNum
        from TicketInfo
        where T_TrainId = '$trainid'
            and T_PStationNum = $x
            and T_Type = '$seattype'
            and T_Date = '$date';
EOF;
    $ret = pg_query($conn, $query_seat_num);
	if (!$ret){
		echo "执行失败";
	}
    $row_num = pg_num_rows($ret);
    if ($row_num == 0){
        $fuction = <<<EOF
            insert into
                TicketInfo(T_TrainId,T_PStationNum,T_Type,T_Date,T_SeatNum)
            values ('$trainid', $x, '$seattype', '$date', 1);
EOF;
    $ret = pg_query($conn, $fuction);
		if (!$ret){
			echo "执行失败";
		}
    }
    else{
        $row = pg_fetch_row($ret);
		$seat_num = $row[0];
        $new_seat_num = $seat_num + 1;
        $fuction = <<<EOF
            update TicketInfo
            set T_SeatNum = $new_seat_num
            where T_TrainId = '$trainid'
                and T_PStationNum = $x
                and T_Type = '$seattype'
                and T_Date = '$date';
EOF;
        $ret = pg_query($conn, $fuction);
		if (!$ret){
			echo "执行失败";
		}
    }
}
echo "<p><a href = \"back.php?fromname=$toname&toname=$fromname&date=$date&username=$username\">点击</a>查询返程信息。</p>";
?>
</body>
</html>
