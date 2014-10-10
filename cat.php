<?php
require_once('./auth.php');
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link href="style.css" rel="stylesheet" type="text/css">
<?php
/*
* 数据查询
* 王荣杰
* 2014年9月25日
* code utf-8
* filename index.php
*/
include "function.php";
if (!empty($_GET)){
	$aget=array_map("urldecode",$_GET);
	$aget=array_map("addslashes",$aget);
	@$flag=$aget["flag"];
	@$pages=$aget["pages"];
	@$uid=$aget["id"];
	@$hash=$aget["hash"];
}
if (empty($flag)) $flag=0; //默认查询所有用户列表 为1则只查询可疑用户
if (empty($pages)||$pages<0) $pages=0;//默认显示最新的十个用户
if (empty($uid)){
?> 
<title>点击用户列表</title></head>
<body>
<div class="l">
<h1>点击用户列表</h1>
<a href="/cat.php">返回首页</a>
<table width="900" border="1">
    <tr>
   		<th scope="col">ID</th>
    	<th scope="col">用户ID</th>
    	<th scope="col">用户ip</th>
   		<th scope="col">网盟网站</th>
        <th scope="col">语言时区</th>
        <th scope="col">设备信息</th>
        <th scope="col"><a href="?flag=1">是否可疑用户</a></th>
 	 </tr>
    <?php
	$list=userlist($flag,$pages);
	echo $list[0];
	$listnum=$list[1];
	
}
else{	?>
<title>用户<?php echo $uid ;?>详情</title></head>
<body>
<div class="l">
<h1>用户<?php echo $uid ;?>详情</h1>
<a href="/cat.php" >返回首页</a>
<table width="1000" border="1">
	<tr>
   		<th scope="col">ID</th>
    	<th scope="col">用户ip</th>
   		<th scope="col">网盟网站</th>
        <th scope="col">语言时区</th>
        <th scope="col">设备信息</th>
        <th scope="col">用户来源</th>
        <th scope="col">分辨率</th>
        <th scope="col"><a href="?flag=1">是否可疑</a></th>
 	 </tr>
    <?php
	$list=details($uid,$pages);
	echo $list[0];
	$userlistnum=$list[1];
	
	?>
 </table>
 <h1>用户<?php echo $uid ;?>访问记录</h1>
<table width="1000" border="1">
	<tr>
   		<th scope="col">ID</th>
    	<th scope="col">URL||||(谷歌广告进入会标为<font color="red" >红色</font>)</th>
   		<th scope="col">Action</th>
        <th scope="col">时间</th>
 	 </tr>
    <?php
	$list=tracklist($uid,$pages);
	echo $list[0];
	$tracklistnum=$list[1];
	$listnum=max($userlistnum,$tracklistnum);
	?>

 
 <?php  } ?>
</table>

<?php 
$pre=$pages-20;
$next=$pages+20;
if($pages!=0) echo '<a href="?pages=0&flag='.$flag.'&id='.@$uid.'&hash='.@$hash.'">第一页</a>';
if($pre>=0) echo '<a href="?pages='.$pre.'&flag='.$flag.'&id='.@$uid.'&hash='.@$hash.'">上一页</a>' ;
if($listnum==20) echo '<a href="?pages='.$next.'&flag='.$flag.'&id='.@$uid.'&hash='.@$hash.'">下一页</a><br>';
 ?>
</div>

	</body>
</html>
