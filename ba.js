(function(a,undefined){
	/* 黎明竞价点击统计功能代码
	 * 杨海涛 2014年9月19日
	 * code utf-8
	 * filename ba.js
	 */

	//定义每次要发送到服务器上的数据
	//Interface:  key值为GET上去的key值
	var ba={
		url:"",			//客户进入页面
		ads_site:"",	//联盟网站
		referer:"",     //点击来源网站
		timezone:"",	//时区
		lang:"",		//语言
		device:"",		//设备名称
		browser:"",		//浏览器名称
		resolution:"",	//屏幕分辨率
		cookie:0		//是否支持cookie
	};
	//Interface
	//action:用户进入或者离开的动作，为in 或 out

	//Interface
	//只统计竞价来源
	//两种方式判断客户来自于竞价
	//1,当前url中含有竞价指定字段
	//2,含有COOKIE值
	//短期COOKIE用来统计用户访问轨迹，可设置关闭浏览器就失效
	//长期COOKIE用来统计用户的二次访问，需要设置较长时间
	//如果用户含有短期COOKIE，或者URL中含有点击字段，则此用户是竞价用户，予以记录，否则是正常优化的用户，不予记录
	//
	//短期COOKIE名 visitid
	//长期COOKIE名 userid

	//发送远程数据的地址
	var dataurl="http://www.baidu.com/ba.php";

	//1, 获取网站url
	ba.url=a.top.location.href;

	//获取搜索字段
	var basearch=a.top.location.search;

	//2.获取联盟网站
	ba.ads_site=(function(){
		for (var i in ads_site_key){
			var exp=new  RegExp(ads_site_key[i],"i");
			if(basearch.match(exp)){
				var ads_site=basearch.match(exp);
				return (ads_site[2]);
			}
		}
		return "";
	})();

	//3，获取网站来源
	//url ?之后含有的关键词,此种情况为点击广告
	var click_key=["gclid"];
	//联盟网站字段
	var ads_site_key=["(placement=)(.*?)[-&][\\w]*?=","(place\=)(.*?)[&-][\\w]*?\=","(plc\=)(.*?)[&-][\\w]*?\="];
	ba.referer=a.document.referrer||"";

	//4, 获取客户浏览器所在时区
	ba.timezone="GMT "+(function(){
		var offset=new Date().getTimezoneOffset()/60;
		if(offset>0) return String(offset*-1);
		if(offset<0) return "+"+String(offset*-1); 
		if(offset=0) return "";
	})();

	//5, 获取客户浏览器语言
	ba.lang=a.navigator.language||a.navigator.userLanguage;

	//6, 获取分辨率
	ba.resolution=a.screen.width+"*"+a.screen.height;

	//7, 获取浏览器名称
	ba.browser=getbrowser();

	//8,获取设备名称
	ba.device=getdevice();


	//获取浏览器名称
	function getbrowser(){
		var browser = {appname: 'unknown', version: 0},
			userAgent = a.navigator.userAgent.toLowerCase();
		//IE 11
		if((/trident\/7\.0/).test(userAgent)){
			browser.appname="IE";
			browser.version="11";
		}
		//IE,firefox,opera,chrome,netscape
		if (/(msie|firefox|opera|chrome|netscape)\D+(\d[\d.]*)/.test(userAgent)){
			browser.appname = RegExp.$1;
			browser.version = RegExp.$2;
			browser.appname=(browser.appname=="msie")?"IE":browser.appname;
		} else if ( /version\D+(\d[\d.]*).*safari/.test(userAgent)){ // safari
			browser.appname = 'safari';
			browser.version = RegExp.$2;
		}
		return browser.appname;
	}

	//获取设备名称
	function getdevice(){
		//可能的设备:
		//移动设备
		//ipad iphone ipod andriod  symbianos BlackBerry   windows mobile windows ce MIDP
		//固定设备
		//win2000 - Win8
		//Mac Unix Linux
		var ua = a.navigator.userAgent.toLowerCase();
		if(ua.match(/ipad/i))
			return 'ipad';
		if(ua.match(/iphone/i))
			return 'iphone';
		if(ua.match(/ipod/i))
			return 'ipod';
		if(ua.match(/android/i))
			return 'android';
		if(ua.match(/symbianos/i))
			return 'symbian';
		if(ua.match(/blackberry/i))
			return 'BlackBerry';
		if(ua.match(/windows[\s]*mobile/i))
			return 'windows mobile';
		if(ua.match(/windows[\s]*ce/i))
			return 'windows ce';
		if(ua.match(/midp/i))
			return 'o mobile';

		if((a.navigator.platform == "Mac68K") || (a.navigator.platform == "MacPPC") || (a.navigator.platform == "Macintosh") || (a.navigator.platform == "MacIntel"))
			return 'Mac';
		if((a.navigator.platform == "Win32") || (a.navigator.platform == "Windows")){
			if(ua.indexOf("windows nt 5.0") > -1 || ua.indexOf("windows 2000") > -1) return 'Win200';
			if(ua.indexOf("windows nt 5.1") > -1 || ua.indexOf("windows xp") > -1) return 'WinXP';
			if(ua.indexOf("windows nt 5.2") > -1 || ua.indexOf("windows 2003") > -1) return 'Win2003';
			if(ua.indexOf("windows nt 6.0") > -1 || ua.indexOf("windows vista") > -1) return 'WinVista';
			if(ua.indexOf("windows nt 6.1") > -1 || ua.indexOf("windows 7") > -1) return 'Win7';
			if(ua.indexOf("windows nt 6.2") > -1 || ua.indexOf("windows nt 6.3") > -1  || ua.indexOf("windows 8") > -1) return 'Win8';
		}else if(a.navigator.platform == "X11"){
			return 'Unix';
		}else if(String(a.navigator.platform).indexOf("Linux") > -1){
			return 'Linux';
		}
		return 'Unkown';
	}


	//获取对应key值的cookie
	var getcookie=function(name){
		var c=document.cookie;
		var carr=c.split(";");
		for(var i=0;i<carr.length;i++){
			if(carr[i].indexOf(name+"=")>-1){
				return 	carr[i].substring(carr[i].indexOf("=")+1);
			}
		}
		return "";
	}

	//设置COOKIE
	var setcookie=function(name,value,days,path){
		if(name==null) throw "COOKIE NAME MUST NOT BE EMPTY"; 
		if(value==null) throw "COOKIE VALUE MUST NOT BE EMPTY";
		if(days==null) days=0;
		var exp=new Date();
		exp.setTime(exp.getTime()+days*24*60*60*1000);
		if(path==null) path="/";
		document.cookie=name+"="+escape(value)+";expires="+exp.toGMTString()+";path="+path;
	}

	//判断用户浏览器是否支持COOKIE
	var is_enable_cookie=a.navigator.cookieEnabled||false;

	//判断url是否含有指定关键词
	//用来判断用户是不是点击广告进入
	function is_ad_in(){
		for(var i in click_key){
			if(basearch.indexOf(click_key[i])>-1) return true;
		}
		return false;
	}

	//序列化对象
	function serialize(obj){
		var str="";
		for(var i in obj){
			str+=encodeURIComponent(i)+"="+encodeURIComponent(obj[i])+"&";
		}
		return str.substring(0,str.length-1);
	}

	//添加动作回调函数与参数
	function gelegate(target,action,callback,arg){
		if(document.addEventListener){
			target.addEventListener(action,function(){
				callback.apply(null,arg);
			},false);
		}else if(document.attachEvent){
			action="on"+action;
			target.attachEvent(action,function(){
				callback.apply(null,arg);
			});
		}
	}

	//发送远程数据
	//url是远程图片的地址
	//action为用户动作，进入 in 离开 out
	//msg为对象，默认为统计到的数据，
	function sendmsg(url,action,msg){
		if(!url) url= dataurl;
		if(!action) action="in";
		if(!msg) msg=ba;
		new Image().src=url+"?action="+action+"&"+serialize(msg);
	}

	//当用户点击广告进入时，给当前网站设置一个识别的COOKIE
	//只有含有此COOKIE时, 才向服务器发送统计数据
	//因为只需要统计点击广告的用户
	//COOKIE: click_time 进入时间

	//如果用户点击广告进入，并且支持COOKIE 则
	//1,发送统计数据
	//2,设置或更新COOKIE
	if(is_ad_in() && is_enable_cookie){
		ba.cookie=1;
		setcookie("click_time",new Date().getTime(),365,'/');
		sendmsg(dataurl,'in',ba);
		//挂载窗口关闭动作
		gelegate(a,"beforeunload",sendmsg,[dataurl,'out',ba]);
	}
	//如果用户不是点击广告进入，但是是之前的客户回访 也支持COOKIE
	//如果客户端支持COOKIE
	//含有COOKIE click_time的为竞价客户
	else if(!is_ad_in() && getcookie('click_time')){
		ba.cookie=1;
		//发送数据
		sendmsg(dataurl,'in',ba);
		//挂载窗口关闭动作
		gelegate(a,"beforeunload",sendmsg,[dataurl,'out',ba]);
	}
	//如果用户没有启用COOKIE,则不能判定访问用户是否是竞价用户的第二次访问
	//此时直接发送数据给服务器
	else if (!is_enable_cookie){
		//发送用户进入数据
		sendmsg(dataurl,'in',ba);
		//挂载窗口关闭动作
		gelegate(a,"beforeunload",sendmsg,[dataurl,'out',ba]);
	}
})(window)
;
