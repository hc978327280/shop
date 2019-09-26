<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
class Allow extends Controller{
	public function _initialize(){
		//判断是否登
		if (!session('?uid')) {
			$this->redirect('login/login');
			
		}else{
			//当前登录的用户id
			$uid=session('uid');
			// 获取登录用户的角色
			$res=Db::name('admin_role')->where('uid',$uid)->where('rid',1)->find();
			// 判断是否为超级管理员角色
			if(!$res){
				// 获取当前访问的控制器
				$cur_controller=request()->controller();
				// 获取当前访问的方法
				$cur_action=request()->action();
				// 获取到当前用户所属角色有哪些权限
				$auth=Db::name('admin_role')
					->alias('ar')
					->join('shop_role_node rn','ar.rid=rn.rid','LEFT')
					->join('shop_node n','n.id=rn.nid','LEFT')
					->field("n.ename,n.pid")
					->where('ar.uid',$uid)
					->select();
					// dump($auth);
					// 将所有的控制器放在一个数组中
				foreach ($auth as $key => $value) {
					if($value['pid']==0){
						$controller[]=$value['ename'];
					}
				}
				// dump($auth);
				// 判断是否有访问控制器的权限
			if(in_array($cur_controller,$controller)){
				// 判断是否有访问方法的权限
				foreach ($auth as $key => $value){
					$a=Db::name('node')->where('ename',$cur_controller)->where('pid',0)->find();
					if($value['pid']==$a['id']){
						$action[]=$value['ename'];
					}
				}
				if(!in_array($cur_action,$action)){
					$this->error('您没有权限访问该方法','auth/auth');
				}
			}else{
				$this->error('您没有权限访问改模块','auth/auth');
			}
				
			}
		}
	}
}


?>