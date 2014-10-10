<?php
/* 竞价点击统计系统
 * 王荣杰 2014年9月24日
 * code utf-8
 * filename ba.php
 */

//非正常访问，退出
if(empty($_GET)) exit();

//加载相关配置
require_once("./config.php");
require_once ("./mysql.php");
require_once("./class.user.php");

date_default_timezone_set('PRC');
ignore_user_abort(true);

//设置头部信息防止被缓存
header("Cache-Control:max-age=0");
header("Pragma:no-cache");    
header("Expires:Thu Sep 25 2014 00:00:00 GMT");
header("Content-Type:image/jpeg");
header("Content-Length:1124");

//初始化
$DB = new MySql(DB_host,DB_usr,DB_psw,DB_name);
$click_user=new Cuser();
$url=$click_user->url;
$ip=$click_user->getIP();

//三种身份的用户
//非竞价正常浏览
//1, 第一次点击广告进入的用户 ->  进入网址会含有某个特殊参数
//2, 第N次点击广告进入的用户 ->	进入网址含有某个特殊参数且含有永久COOKIE
//3, 广告用户的第二次浏览 -> 请求会包含有识别cookie visitid
//只有满足点击广告或者广告用户第二次浏览，才给于记录

if(strchr(strstr($url,'?'),$distinguish) || !empty($_COOKIE['visitid'])){
	//竞价进入分为两种情况，第一种是第一次点击广告进入的，第二种是二次访问
	//
	//如果用户是第一次访问(不含永久的COOKIE或者不支持COOKIE),则:
	//1,设置永久COOKIE
	//2,设置短期COOKIE
	//3,记录用户表和轨迹表
	//
	//如果用户是第二次访问(含有短期COOKIE),则:
	//1,更新短期COOKIE
	//2,通过原有hash和现有hash对比，检查用户是否改变环境
	//	如果改变，1,把新的hash值加入到永久COOKIE中存储
	//			  2,新建一个同userid用户，并标记此用户可疑
	//3,更新访问次数,写入永久COOKIE
	if($click_user->is_new()){
		//1:
		$userid=$click_user->generateID();
		$uhash=$click_user->getUhash();
		$long_term_cookie=$userid.'_'.$uhash.'_1';
		setcookie("userid",$long_term_cookie,time()+315360000,"/","");
		//2:
		setcookie("visitid",$userid,time()+$cookie_time,"/","");
		//3:
		$click_user->create_user($userid,$ip,0,$uhash);
		$click_user->record_user_track($userid,$uhash);
	}else{
		//1:
		setcookie("visitid",time(),time()+$cookie_time,"/","");
		//2:
		$userid=$click_user->getUmsg("id");		//COOKIE中保存的用户ID
		$rhash=$click_user->getUmsg("hash");    //COOKIE中保存的用户信息hash
		$chash=$click_user->getUhash();			//当前请求的hash
		//记录轨迹
		$click_user->record_user_track($userid,$chash);

		if($rhash!=$chash){
			//创建可疑用户
			$click_user->create_user($userid,$ip,1,$chash);
			//更新可疑用户cookie
			setcookie("userid",$userid.'_'.$chash.'_'.$click_user->getUmsg('count'),time()+315360000,"/","");
		}
		//3:
		$vcout=$click_user->getUmsg("count");
		if(empty($_COOKIE['visitid'])){
			$vcout++;
			$long_term_cookie=$userid.'_'.$chash.'_'.$vcout;
			setcookie("userid",$long_term_cookie,time()+315360000,"/","");
		}
	}
}
echo base64_decode('/9j/4QAYRXhpZgAASUkqAAgAAAAAAAAAAAAAAP/sABFEdWNreQABAAQAAAAKAAD/4QMqaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wLwA8P3hwYWNrZXQgYmVnaW49Iu+7vyIgaWQ9Ilc1TTBNcENlaGlIenJlU3pOVGN6a2M5ZCI/PiA8eDp4bXBtZXRhIHhtbG5zOng9ImFkb2JlOm5zOm1ldGEvIiB4OnhtcHRrPSJBZG9iZSBYTVAgQ29yZSA1LjUtYzAxNCA3OS4xNTE0ODEsIDIwMTMvMDMvMTMtMTI6MDk6MTUgICAgICAgICI+IDxyZGY6UkRGIHhtbG5zOnJkZj0iaHR0cDovL3d3dy53My5vcmcvMTk5OS8wMi8yMi1yZGYtc3ludGF4LW5zIyI+IDxyZGY6RGVzY3JpcHRpb24gcmRmOmFib3V0PSIiIHhtbG5zOnhtcE1NPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vIiB4bWxuczpzdFJlZj0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL3NUeXBlL1Jlc291cmNlUmVmIyIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bXBNTTpEb2N1bWVudElEPSJ4bXAuZGlkOjYzOTkyOTE5NDNDMTExRTQ4RTk0QjJDOEREQzkzNDRBIiB4bXBNTTpJbnN0YW5jZUlEPSJ4bXAuaWlkOjYzOTkyOTE4NDNDMTExRTQ4RTk0QjJDOEREQzkzNDRBIiB4bXA6Q3JlYXRvclRvb2w9IkFkb2JlIFBob3Rvc2hvcCBDQyAoV2luZG93cykiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDpDREQ0RTBEMjQzOTMxMUU0QjZFQUVCM0E2Q0ExNzk1QSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDpDREQ0RTBEMzQzOTMxMUU0QjZFQUVCM0E2Q0ExNzk1QSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/Pv/uAA5BZG9iZQBkwAAAAAH/2wCEABQQEBkSGScXFycyJh8mMi4mJiYmLj41NTU1NT5EQUFBQUFBREREREREREREREREREREREREREREREREREREREQBFRkZIBwgJhgYJjYmICY2RDYrKzZERERCNUJERERERERERERERERERERERERERERERERERERERERERERERERERP/AABEIAAEAAQMBIgACEQEDEQH/xABLAAEBAAAAAAAAAAAAAAAAAAAABgEBAAAAAAAAAAAAAAAAAAAAABABAAAAAAAAAAAAAAAAAAAAABEBAAAAAAAAAAAAAAAAAAAAAP/aAAwDAQACEQMRAD8AswAf/9k=');
