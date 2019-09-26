<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Shoplist extends Controller{
	//加载订单列表页面
	public function getlist(){
		$id=substr(input('id'),1);
		$data['uid']=session("user.uid");
		$res=Db::name('cart')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->field('c.*,g.goods_name,g.slt,c.num*c.price as total')->where("c.id in ($id)")->select();
		$t=Db::name('cart')->field("sum(num*price) as totals")->where("id in ($id)")->find();
		//获取用户地址
		$add=Db::name('address')->alias('a')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*")->where('uid',$data['uid'])->select();
//
				return $this->fetch('pay',['res'=>$res,'t'=>$t,'add'=>$add]);
	}
	//接受订单数据
	public function getdolist(){
		
		$data['addressid']=input('addid');
		$data['money']=input('money');
		$data['uid']=session('user.uid');
		// $data['orderno']=
		$data['ordno']=date('Ymd').str_pad(mt_rand(1,99999),5,'0',STR_PAD_LEFT);
		$res=Db::name('order')->insert($data);
		$id='';
		if ($res) {
			//查询购物车表
			$info=Db::name('cart')->where('status',1)->select();
			foreach ($info as $key => $value) {
				// $data1[$key]['ordno']=input('ordno');
  		// 	$data1[$key]['goodsid']=$val['goodsid'];
  		// 	$data1[$key]['num']=$val['num'];
  		// 	$data1[$key]['price']=$val['price'];
  		// 	$data1[$key]['attr']=$val['attr'];
  			// 组装一个id字符串
  			 	$result[$key]['ordno']=$data['ordno'];
  			 	$result[$key]['goodsid']=$value['goodsid'];
  			 	$result[$key]['num']=$value['num'];
  			 	$result[$key]['price']=$value['price'];
  			 	$result[$key]['attr']=$value['attr'];
  			 	$id=$value['goodsid'].",".$id;
			}
			//向shop_order_info表中插入数据
			$infoAll=Db::name('order_info')->insertAll($result);
			$goodsid=rtrim($id,',');
			// dump($goodsid);
			// dump($goodsid);
			//删除购车车表中的数据
			if ($infoAll>0) {
				$del=Db::name('cart')->where("goodsid in ($goodsid)")->delete();	
				if ($del) {
					$infos=$data['money'];
					$infoss=$data['ordno'];
					$this->success('进入支付页面',url('/pay/pay',['money'=>$infos,'ordno'=>$infoss]));
				}else{
				$this->error('订单提交失败');
				}
			}else{
				$this->error('订单提交失败');
			}
		}
	
	}
}
?>