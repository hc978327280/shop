<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Address extends Controller{
	//加载地址页面
	public function getaddress(){
		$dizhi=Db::name('address')->alias('a')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*")->where('uid',session("user.uid"))->select();
		$pro=Db::name('province')->select();
		return $this->fetch('address',['pro'=>$pro,'dizhi'=>$dizhi]);
	}


	//ajax三级联动
	public function postcity(){
		if (!input('citycode')) {
				
			$province=input('code');
			$city=Db::name('city')->where('provincecode',$province)->select();
			echo json_encode($city);
		} else {
			$citycode=input('citycode');
			$area=Db::name('area')->where('citycode',$citycode)->select();
			echo json_encode($area);
		}
		
	}
	//地址添加
	public function postadd(){
		$data=request()->post();
		$data['uid']=session('user.uid');
		$res=Db::name('address')->insert($data);
		if ($res) {
			$this->success('地址添加成功',url('/add/address'));
		} else {
			$this->error('地址添加失败',url('/add/address'));
		}
		
	}
	//地址删除
	public function getdel(){
		$id=input('id');
		$res=Db::name('address')->delete($id);
		if ($res) {
			$this->success('地址删除成功',url('/add/address'));
		} else {
			$this->error('地址删除失败',url('/add/address'));
		}
		

	}
	//地址的修改模板加载
	public function getupdate(){
		$id=input('id');
		$res=Db::name('address')->find($id);
		$pro=Db::name('province')->select();
		return $this->fetch('addressxiugai',['pro'=>$pro,'data'=>$res]);
	}
	//执行修改地址
	public function postdoupdate(){
		$id=input('id');
		$data=request()->post();
		$res=Db::name('address')->where('id',$id)->update($data);
		if ($res) {
			$this->success('地址修改成功',url('/add/address'));
			
		} else {
			$this->error('地址修改失败',url('/add/address'));
			
		}
		
	}
}



?>