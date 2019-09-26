<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Shopcart extends Controller{
	//加载购物车模块
	public function getcart(){
		$type_id=input('type_id');
		$data['uid']=session("user.uid");
		//判断是否从商品详情也跳转
		if (input('goodsid')) {
			$data['goodsid']=input('goodsid');
			$data['attr']=input('kouwei');
			$data['num']=input('nums');
			$data['market_price']=input('market');
			$data['price']=input('shop');
	
			//判断商品最多不能超过多少件
			$kucun=Db::name('product')->where('attr_id',$type_id)->find();
			// dump($kucun['goods_number']);
			if ($data['num']<$kucun['goods_number']) {
				//判断商品是否存在，存在就修改数量
				$res=Db::name('cart')->where('goodsid',$data['goodsid'])->find();
				
				if (!$res) {
					$result=Db::name('cart')->insert($data);
					if ($result) {
						$this->success('加入购物车成功','/cart/cart');
					}else{
		       			$this->error('加入数量超过上限');
					}
				}else{
					//执行修改商品数量跟属性
					$xiugai['num']=input('nums')+$res['num'];
					$xiugai['attr']=input('kouwei').':'.$res['attr'];
					$info=Db::name('cart')->where('goodsid',$data['goodsid'])->update($xiugai);
					if ($info) {
						$this->success('加入购物车成功','/cart/cart');
					}else{
		       			$this->error('加入购物车失败');
					}
				}
			}else{
				$this->error('加入数量超过上限');
			}
		}
		//查询购物车表遍历
		$res=Db::name('cart')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->field('c.*,g.goods_name,g.slt,c.num*c.market_price as total')->order('c.ctime desc')->where('uid',$data['uid'])->select();
			$t=Db::name('cart')->field("sum(num*market_price) as total,sum(num) as nums")->where('status',1)->find();
			
		return $this->fetch('shopcart',['res'=>$res,'t'=>$t]);
	}

	//接受购物车的数量加减
	public function postaddnum(){
		$data['num']=input('num');
		$res=Db::name('cart')->where('id',input('id'))->update($data);
		if ($res) {
			$result=Db::name('cart')->field("num*market_price as shop_price")->find(input('id'));
			$t=Db::name('cart')->field("sum(num*market_price) as total,sum(num) as nums")->where('status',1)->find();
			$arr=['shop_price'=>$result['shop_price'],'total'=>$t['total'],'num'=>$t['nums']];
			echo json_encode($arr);
		}
	}
	//接受复选框
	public function postcheck(){
		$id=input('id');
		$info=Db::name('cart')->find($id);
		if ($info['status']==0) {
			Db::name('cart')->where('id',$id)->update(array('status'=>1));
			$t=Db::name('cart')->field("sum(num*market_price) as total,sum(num) as nums")->where('status',1)->find();
			$arr=['total'=>$t['total'],'num'=>$t['nums']];
		}else{
			Db::name('cart')->where('id',$id)->update(array('status'=>0));
			$t=Db::name('cart')->field("sum(num*market_price) as total,sum(num) as nums")->where('status',1)->find();
			$arr=['total'=>$t['total'],'num'=>$t['nums']];
		}
		echo json_encode($arr);
	}
	//多选
	public function postchecks(){
		$id=substr(input('id'),1);
		
		Db::name('cart')->where("id in ($id)")->update(array('status'=>1));
		$t=Db::name('cart')->field("sum(num*market_price) as total,sum(num) as nums")->where('status',1)->find();
		$arr=['total'=>$t['total'],'num'=>$t['nums']];
		echo json_encode($arr);
	}
	//取消多选
	public function postdocheck(){
		$id=substr(input('id'),1);
		
		Db::name('cart')->where("id in ($id)")->update(array('status'=>0));
		$t=Db::name('cart')->field("sum(num*market_price) as total,sum(num) as nums")->where('status',1)->find();
		$arr=['total'=>$t['total'],'num'=>$t['nums']];
		echo json_encode($arr);
	}
	//单条删除
	public function getdel(){
		$id=input('id');
		$res=Db::name('cart')->delete($id);
		if ($res) {
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}

	}
	//批量删除
	public function postdodel(){
		$data=request()->post();
		//判断是否传入
		if (!empty($data)) {
			$id=implode(',',$data['id']);
			$res=Db::name('cart')->where("id in ($id)")->delete();
			if ($res) {
			$this->success('批量删除成功');
			}else{
			$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
	}
}


?>