<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Pay extends Controller{
	//加载支付模板
	public function getpay(){
		$money=input('money');
		$ordno=input('ordno');
		
		return $this->fetch('payend',['money'=>$money,'ordno'=>$ordno]);
	}
	//修改订单状态
	public function getupdate(){
		$info=input('ordno');
		$res=Db::name('order')->where('ordno',$info)->update(array('status'=>2));
		if ($res) {
			$result=Db::name('order')->alias('o')->join('address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,o.ordno,o.money")->where('ordno',$info)->find();
			return $this->fetch('success',['data'=>$result]);
		}else{
			$result=Db::name('order')->alias('o')->join('address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,o.ordno,o.money")->where('ordno',$info)->find();
			return $this->fetch('success',['data'=>$result]);

		}
	}
	
}


?>