<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Goodslist extends Controller{
	//加载商品列表
	public function getindex(){
		$id=input('id');
		$type=Db::name('goods')->where('type_id',$id)->select();

		// dump($type);
		return $this->fetch('goodslist',['data'=>$type]);
	}
	//加载单品商品
	public function getgoods(){
		$goodsid=input('id');
		$res=Db::name('goods')->find($goodsid);
		// dump($res);
		$data=explode(',',$res['goods_img']);
		//查询属性跟库存
		$ress=Db::name('comment')->alias('c')->join('shop_memberlogin m','c.uid=m.id')->field('c.comment,c.img,c.admin,m.username,c.time')->where('c.goodsid',$goodsid)->select();
		// dump($ress);
		$result=Db::name('goods_attr')->alias('att')->join('shop_product p','p.attr_id=att.id','LEFT')->field('att.attr_value,p.*')->where('att.goods_id',$goodsid)->select();
		// dump($result);

		return $this->fetch('introduction',['data'=>$res,'res'=>$data,'result'=>$result,'com'=>$ress]);
	}
	//接受库存查询
	public function getkucun(){
		 if (request()->isajax()) {
			$attrid=input('attr_id');
			$info=Db::name('product')->field('goods_number')->where('attr_id',$attrid)->find();
			 echo json_encode($info);
		 }
	}
	
}



?>