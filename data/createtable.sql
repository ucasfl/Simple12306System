
create table Station
(
    S_Name          varchar(20) primary key,
    S_City          varchar(20) not null
);

-- Change owner to be import into database
-- $ sudo chown -R postgres train-2016-10

copy Station
from './all-stations-out.txt'
with (FORMAT csv);

-- Test if import success
-- select S_Name, S_City
-- from Station
-- where S_City = '上海';


create table Passby
(
    P_TrainId       varchar(6) not null,
    P_StationName   varchar(20) not null,
    P_StationNum    integer not null,
    P_ArriveTime    time,
    P_GoTime        time,
    P_MoneyYZ       float,
    P_MoneyRZ       float,
    P_MoneyYW1      float,
    P_MoneyYW2      float,
    P_MoneyYW3      float,
    P_MoneyRW1      float,
    P_MoneyRW2      float,
    primary key (P_TrainId, P_StationNum),
    foreign key (P_StationName) references Station(S_Name)
);

copy Passby
from './all-all.csv'
with (FORMAT csv);

-- dbmslab2=# copy Passby
-- dbmslab2-# from '/home/dingshizhe/Documents/db_/dbms-lab2/train-2016-10/tmp/pass_by_all_data/all-all-tmp.csv'
-- dbmslab2-# with (FORMAT csv);
-- COPY 54742



-- Test if import success

-- select P_TrainId, P_StationName, P_StationNum
-- from Passby
-- where P_StationName = '北京';

-- select Station.S_City, Passby.P_TrainId
-- from Passby, Station
-- where Passby.P_StationName = Station.S_Name
--     and Station.S_City = '北京';

-- select Passby.P_TrainId
-- from Passby, Station
-- where Passby.P_StationName = Station.S_Name
--     and Station.S_City = '北京'
-- intersect
-- select Passby.P_TrainId
-- from Passby, Station
-- where Passby.P_StationName = Station.S_Name
--     and Station.S_City = '苏州';

-- select *
-- from Passby
-- where P_TrainId = 'G107';


create table UserInfo
(
    User_Id         char(18) primary key,
    U_Name          varchar(20) not null,
    U_Phone         char(11) not null,
    U_UName         varchar(20) not null,
    U_CreditCardId  char(16) not null
);

create type status_type as enum (
    'normal', 'cancelled', 'past'
);

create type seat_type as enum (
    'YZ', 'RZ', 'YW1', 'YW2', 'YW3', 'RW1', 'RW2'
);

create table Book
(
    B_Id            SERIAL primary key,
    B_UserId        char(18) not null,
    B_TrainId       varchar(6) not null,
    B_Date          date not null,
    B_StationNum1   integer not null,
    B_StationNum2   integer not null,
    B_SType         seat_type not null,
    B_Money         integer not null,
    B_Status        status_type not null,
    foreign key (B_TrainId, B_StationNum1) references Passby(P_TrainId, P_StationNum),
    foreign key (B_UserId) references UserInfo(User_Id)
);

create table TicketInfo
(
    T_TrainId      varchar(6) not null,
    T_PStationNum  integer not null,
    T_Type         seat_type not null,
    T_Date         date not null,
    T_SeatNum      integer not null,
    primary key (T_TrainId, T_PStationNum, T_Type, T_Date),
    foreign key (T_TrainId, T_PStationNum) references Passby(P_TrainId, P_StationNum)
);


