<?php
namespace app\admin\controller;
use think\Controller;
//验证码命名空间
use think\captcha\Captcha;
//Db类命名空间
use think\Db;
class Login extends Controller{
	//加载登陆模板
	public function getlogin(){
		return $this->fetch('login');
	}
	// 生成验证码的方法
	public function getverify(){
		$config =[
	    // 验证码字体大小
	    'fontSize'=>30,    
	    // 验证码位数
	    'length'=>4,   
	    // 关闭验证码杂点
	    'useNoise'=>false, 
	];
	$captcha = new Captcha($config);
	return $captcha->entry(1);
	}
	public function postsubmit(){

		//判断验证码是否正确
		$captcha=new Captcha();
		//接受表单传输的验证码
		$code=input('code');
		$res=$captcha->check($code,1);
		if ($res) {
			//验证码正确后  对用户名和密码进行判断
			$name=input('name');
			$pwd=input('pwd');
			$ures=Db::name('admin')->where('name',$name)->where('pwd',md5($pwd))->find();
			if ($ures) {
				session('user',$ures['user']);
				session('name',$ures['name']);
				session('uid',$ures['id']);
				$this->success('登陆成功','index');
			}else{
				$this->error('用户名或者密码错误');
			}
		}else{
			$this->error('验证码不正确');
		}
	}
	//退出登录
	public function getout(){
		session('name',null);
		$this->success('退出成功','login/login');
	}
}



?>