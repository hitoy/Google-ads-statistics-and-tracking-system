/***
数据库结构
用户点击表：
系统ID
访问ID
用户ID
IP
广告网站
来源
时区
语言
设备
浏览器
操作系统
用户代理

访问轨迹表
id --对应点击表中的id
访问url
动作 进入或者离开 in /out 
时间
***/


create table click_user(
id int UNSIGNED auto_increment primary key,
userid char(20) not null,
ip_addr char(18) not null,
ads_site varchar(200),
referer varchar(500),
timezone char(8),
lang char(10),
device char(10),
browser char(15),
useragent varchar(200),
resolution char(18),
suspicious boolean	default false,
hash char(32),
index userid  (UserId),
index ip_addr (IP_addr)
)engine=myisam default charset=utf8;

create table user_track(
id bigint UNSIGNED auto_increment primary key,
userid char(20) not null,
url varchar(500),
action char(10),
time Datetime not null,
hash char(32),
index userid  (userid),
index hash (hash)
)engine=myisam default charset=utf8;
