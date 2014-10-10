<?php
/* 点击用户类 杨海涛
 * 2014年9月23日
 * code utf-8
 * filename class.user.php
 */

//点击进入用户的类 click user
class Cuser{
	//public $userid;			//点击用户的用户ID，存放在COOKIE和数据库中
	public $ipaddr;		//用户IP
	public $ads_site;		//进入网站的联盟网站
	public $referer;		//用户点击来源, 通过JS获取
	public $timezone;		//用户所在时区
	public $lang;			//用户浏览器语言
	public $device;		//用户使用设备
	public $browser;		//用户浏览器
	public $url;
	//public $os;			//操作系统
	public $useragent;		//用户代理
	public $resolution;	//分辨率
	public $action;	//in/out

	//构造函数,用户相关值
	public function __construct(){
		$d=array_map("rawurldecode",$_GET);
		$d=array_map("addslashes",$d);
		$this->ipaddr=$this->getIP();
		$this->useragent=$_SERVER["HTTP_USER_AGENT"];
		//传递数据
		$this->url=$d["url"];
		$this->ads_site=$d["ads_site"];
		$this->referer=$d["referer"];
		$this->timezone=$d["timezone"];
		$this->lang=$d["lang"];
		$this->device=$d["device"];
		$this->browser=$d["browser"];
		$this->resolution=$d["resolution"];
		$this->action=$d["action"];
	}

	//获取用户真实IP
	public function getIP(){
		$ip=$_SERVER["REMOTE_ADDR"];
		if(isset($_SERVER["HTTP_CLIENT_IP"])){
			$ip=$_SERVER["HTTP_CLIENT_IP"];	
		}else if(isset($_SERVER["HTTP_X_FORWARDED_FOR"])){
			$ip=$_SERVER["HTTP_X_FORWARDED_FOR"];
		}else if(isset($_SERVER["HTTP_X_REAL_IP"])){
			$ip=$_SERVER["HTTP_X_REAL_IP"];
		}
		//$_SERVER["REMOTE_ADDR"]=$ip;
		$ipexp="/^((25[0-5]|2[0-4]\d|[01]?\d\d?)\.){3}(25[0-5]|2[0-4]\d|[01]?\d\d?)$/i";
		if(preg_match($ipexp,$ip)){
			$_SERVER["REMOTE_ADDR"]=$ip;
		}
		return $ip;
	}

	//获取当前访问用户环境的hash值
	//包括: IP, 时区，语言，浏览器， 分辨率
	public function getUhash(){
		return md5($this->ipaddr.$this->timezone.$this->lang.$this->browser.$this->resolution);
	}

	//根据COOKIE获取此用户以前访问的情况
	//获取访问用户的相关信息：ID Hash值，和访问次数
	//COOKIE的组成:用户ID_用户情况hash值_用户访问次数
	//用户ID的组成: 第一次访问时间戳随机5个区分大小写字母
	public function getUmsg($key="id"){
		$d=$_COOKIE["userid"];
		if(empty($d)){
			return "";
		}
		$u=explode("_",$d);
		if($key=="id"){
			return $u[0];
		}
		if($key=="hash"){
			return $u[1];
		}
		if($key=="count"){
			return $u[2];
		}
	}

	//生成当前访问用户ID，当前时间戳键随机5个区分大小写字母
	public function generateID(){
		$id=time();
		for ($i=0;$i<5;$i++){
			$code=chr(rand(65,122));
			if(preg_match("/[a-zA-Z]/",$code)){
				$id.=$code;
			}else {
				$i--;
			}
		}
		return $id;
	}

	//判断用户是否是第一次访问
	//如果不含有长期期COOKIE，则此用户一定是第一次访问
	public function is_new(){
		if(empty($_COOKIE["userid"])){
			return true;
		}
		return false;
	}
	//创建新用户
	public function create_user($userid,$ip,$suspicious,$hash){
		$sqlusr="INSERT INTO `click_user` (`UserId`,`IP_addr`,`ads_site`,`referer`,`timezone`,`lang`,`device`,`browser`,`useragent`,`resolution`,`suspicious`,`hash` ) VALUES ('$userid','$ip','$this->ads_site','$this->referer','$this->timezone','$this->lang','$this->device','$this->browser','$this->useragent','$this->resolution','$suspicious','$hash')";
		global $DB;
		$query=$DB->query($sqlusr);
	}
	//记录用户轨迹
	public function record_user_track($userid,$hash){
		$nowtime=date('Y-m-d H:i:s',time());
		$sqltrack="INSERT INTO `user_track` (`userid`,`url`,`action`,`time`,`hash`) VALUES ('$userid','$this->url','$this->action','$nowtime','$hash')";
		global $DB;
		$query=$DB->query($sqltrack);
	}
}
?>
