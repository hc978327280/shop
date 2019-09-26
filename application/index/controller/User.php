<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use app\index\model\Member;
class User extends Controller{
	//加载登录模板
	public function getlogin(){
		return $this->fetch('user_login');
	}
	//验证登录
	public function postdologin(){
		$data=request()->post();
		$user=$data['user'];
		$password=md5($data['pwd']);
	
		$res=Db::name('memberlogin')->where('username',$user)->whereor('email',$user)->whereor('phone',$user)->find();
			
		if ($res) {

			if ($password==$res['pwd']) {
				//将登录信息写入SESSION
				$arr=array('username'=>$res['username'],'phone'=>$res['phone'],'email'=>$res['phone'],'uid'=>$res['id']);
				session('user',$arr);
				if (isset($data['chk'])) {
					cookie('user',$arr,strtotime("+1 month"));
					$this->success('登录成功','/');
					
				} else {
					$this->success('登录成功','/');
				}
				
			} else {
				$this->error('用户名或密码错误');
			}
			
		} else {
			// 用户名不存在
				$this->error('用户名或密码错误');
		}
		// dump(session('user.phone'));
	
	}



	//加载注册模板
	public function getreg(){
		return $this->fetch('user_register');
	}
	//邮箱注册
	public function postemail(){
		// 判断是否已存在用户名
		// $user = Member::get(['email' => input('email')]);
		$data=request()->post();
		$data['pwd']=md5($data['pwd1']);
		if (isset($data['cik'])) {
			if ($data['pwd']==md5($data['pwd2'])) {
				$res['pwd']=$data['pwd'];
				$res['email']=$data['email'];
					$res['username']=mt_rand();
				
				$yx=Db::name('memberlogin')->insert($res);
				if ($yx) {
		    	$this->success('用户注册成功',url('/logins/login'));
					
				} else {
		    	$this->error('注册失败');
					
				}
				
			} else {
		    	$this->error('两次密码不一致');
				
			}
			
		}else{
		    	$this->error('请同意《服务协议》');
			
		}
	}
	//手机注册
	public function postphone(){
		$data=request()->post();
		$data['pwd']=md5($data['pwd2']);
		
		$result=Db::name('memberlogin')->select();
		foreach ($result as $key => $value) {
			$phone=$value['phone'];
		}
		
		if ($phone!=$data['phone']) {
				if (isset($data['cik1'])) {
				if ($data['pwd']==md5($data['pwd2'])) {
					$res['pwd']=$data['pwd'];
					$res['phone']=$data['phone'];
					$res['username']=mt_rand();
					$sj=Db::name('memberlogin')->insert($res);
					if ($sj) {
						$this->success('用户注册成功',url('/'));
					} else {
						$this->error('注册失败');
					}
					
				} else {
					$this->error('两次密码不一致');
				}
				
			} else {
				$this->error('请同意《服务协议》');
			}
		} else {
				$this->error('该手机号已注册');
			
		}
		
		
		
	}
	// ajax请求短信接口
	public function postsendMess(){
		$phone=input('phone');
		$templateid='487520';
		echo mess($phone,$templateid);
		}
	//验证短信
	public function postdophone(){
		$yzm=session('yzm');
		$code=input('code');
		if ($yzm==$code) {
			$arr=array('code'=>0,'msg'=>'验证码正确');
		} else {
			$arr=array('code'=>1,'msg'=>'验证码不正确');
		}
		echo json_encode($arr);
		
	}

}



?>