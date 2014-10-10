<?php
//初始化数据库
include "config.php";
require_once ("./mysql.php");
$DB = new MySql(DB_host,DB_usr,DB_psw,DB_name);//初始化数据库类
//用户列表
function userlist($flag,$pages){
	$list_num=0;
	if($flag==0){
	$sql="SELECT * FROM `click_user`  ORDER BY `click_user`.`id` desc LIMIT 20 OFFSET $pages" ;}
	else{
	$sql="SELECT * FROM `click_user` WHERE `click_user`.`suspicious`=1 ORDER BY `click_user`.`userid` desc LIMIT 20 OFFSET $pages";
		}
	global $DB;
	$query=$DB->query($sql); 
	while ($user=$DB->fetch_array($query))
	{
		$list_num++;
		$id=$user['id'];
		$userid=$user['userid'];
		$userid='<a href="?id='.$userid.'">'.$userid.'</a>';
		$userip=$user['ip_addr'];
		$adssite=$user['ads_site'];
		$userlan=$user['lang'].'&nbsp;'.$user['timezone'];
		$userdevice=$user['device'].'&nbsp;'.$user['browser'];
		$suspicious=$user['suspicious'];
		$usersuspicious='';
		if($suspicious){
			$usersuspicious='<a href="?flag=1"><font color="red" >可疑</font></a>';
			}
		@$list.='<tr><td>'.$id.'</td><td>'.$userid.'</td><td>'.$userip.'</td><td>'.$adssite.'</td><td>'.$userlan.'</td><td>'.$userdevice.'</td><td>'.$usersuspicious.'</td></tr>';
	}
	return array (@$list,$list_num);
}
//用户详情
function details($userid,$pages){
	$list_num=0;
	$sql="SELECT * FROM `click_user` WHERE `click_user`.`userid`='$userid' ORDER BY `click_user`.`id` desc LIMIT 20 OFFSET $pages";
	global $DB;
	$query=$DB->query($sql); 
	while ($user=$DB->fetch_array($query))
	{
		$list_num++;
		$id=$user['id'];
		$userip=$user['ip_addr'];
		$adssite=$user['ads_site'];
		$userlan=$user['lang'].'&nbsp;'.$user['timezone'];
		$userdevice=$user['device'].'&nbsp;'.$user['browser'];
		$userreferer=$user['referer'];
		$userresolution=$user['resolution'];
		$suspicious=$user['suspicious'];
		$usersuspicious='';
		if($suspicious){
			$usersuspicious='<a href="?flag=1"><font color="red" >可疑</font></a>';
		}
		@$list.= '<tr><td>'.$id.'</td><td>'.$userip.'</td><td>'.$adssite.'</td><td>'.$userlan.'</td><td>'.$userdevice.'</td><td>'.$userreferer.'</td><td>'.$userresolution.'</td><td>'.$usersuspicious.'</td></tr>';
	}
	return array (@$list,$list_num);
}
	
//用户轨迹表
	function tracklist($userid,$pages){
		$list_num=0;
		$sql="SELECT * FROM `user_track` WHERE `user_track`.`userid`='$userid' ORDER BY `user_track`.`id` DESC LIMIT 20 OFFSET $pages";
		global $DB;
		$query=$DB->query($sql); 
		while ($user=$DB->fetch_array($query))
		{
			$list_num++;
			$id=$user['id'];
			$url=$user['url'];
			$action=$user['action'];
			$time=$user['time'];
			global $distinguish;
			if(strchr(strstr($url,'?'),$distinguish)&&$action=="in") 
			@$list.= '<tr><td>'.$id.'</td><td><font color="red" >'.$url.'</font></td><td>'.$action.'</td><td>'.$time.'</td></tr>';
			else	@$list.= '<tr><td>'.$id.'</td><td>'.$url.'</td><td>'.$action.'</td><td>'.$time.'</td></tr>';
		}
		return array (@$list,$list_num);
		}
	
	
	
