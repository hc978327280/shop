<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
class Comment extends Controller{
	//加载个人评论模块i
	public function getcoms(){
		$uid=session('user.uid');
		$res=Db::name('comment')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->field('c.time,c.comment,c.img,g.goods_name,g.slt')->where('c.uid',$uid)->select();
		return $this->fetch('comment',['data'=>$res]);
	}
}



?>