<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件
function mess($mobile,$templateid){
	//载入ucpass类
	import('Ucpaas',EXTEND_PATH);
	//填写在开发者控制台首页上的Account Sid
	$options['accountsid']='3c25acfec8ac5fdd23fcd41f74030c14';
	//填写在开发者控制台首页上的Auth Token
	$options['token']='14a1d5aad44dc7769bbb076dd785a317';
	//初始化 $options必填
	$ucpass = new \Ucpaas($options);
	$appid = "114cf1a45d974b69a3b00b6da4b57fa3";	//应用的ID，可在开发者控制台内的短信产品下查看
	// $templateid = "480951";    //可在后台短信产品→选择接入的应用→短信模板-模板ID，查看该模板ID
	$param =mt_rand(100000,999999); //多个参数使用英文逗号隔开（如：param=“a,b,c”），如为参数则留空
	// 存储验证码信息
	session('yzm',$param);
	// session_start();
	// $_SESSION['yzm']=$param;
	// $mobile = $_POST['phone'];
	$uid = "";

	//70字内（含70字）计一条，超过70字，按67字/条计费，超过长度短信平台将会自动分割为多条发送。分割后的多条短信将按照具体占用条数计费。

	return  $ucpass->SendSms($appid,$templateid,$param,$mobile,$uid);
	}