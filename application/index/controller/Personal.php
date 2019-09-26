<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Personal extends Controller{
	public function getindex(){
		$uid=session('user.uid');

		$res=Db::name('member_info')->where('uid',$uid)->find();
		if ($res) {
			return $this->fetch('informationeait',['data'=>$res]);
		} else {
			return $this->fetch('information');
		}
		
	}
	//添加个人信息
	public function postadd(){
		$data=request()->post();
		$data['uid']=session('user.uid');
		$res['username']=$data['username'];
		//将昵称存入登陆表
		Db::name('memberlogin')->where('id',$data['uid'])->update($res);
		// 获取表单上传文件 例如上传了001.jpg
		if (isset($data['photo'])) {
				 $file = request()->file('photo');
		    // 移动到框架应用根目录/public/uploads/ 目录下
		    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		    if($info){
		        // 成功上传后 获取上传信息
		        $data['photo']=$info->getSaveName();
		     
		    }
		}
		$res=Db::name('member_info')->insert($data);
		if ($res) {
			$this->success('添加个人信息成功');
		} else {
			$this->error('添加个人信息失败');
		}
		
	}

	//个人信息修改
	public function postupdate(){
		$data=request()->post();
		dump($data);
		$data['uid']=session('user.uid');
		$res['username']=$data['username'];
		Db::name('memberlogin')->where('id',$data['uid'])->update($res);
		// 获取表单上传文件 例如上传了001.jpg
		if (isset($data['photo'])) {
				 $file = request()->file('photo');
		    // 移动到框架应用根目录/public/uploads/ 目录下
		    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
		    if($info){
		        // 成功上传后 获取上传信息
		        $data['photo']=$info->getSaveName();
		     
		    }
		}
		$result=Db::name('member_info')->where('uid',$data['uid'])->update($data);
		if ($result) {
			$this->success('修改个人信息成功');
		} else {
			$this->error('修改个人信息失败');
			
		}
		
	}
}


?>