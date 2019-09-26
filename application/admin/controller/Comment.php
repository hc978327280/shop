<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Comment extends Controller{
	//加载评论模板
	public function getcom(){
		
		$ordno=input('ordno');
		$status=input('status');
		if ($ordno==NULL&&$status==NULL) {
			$res=Db::name('comment')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->join('shop_memberlogin m','c.uid=m.id')->field('c.*,g.goods_name,m.username')->paginate(20,false,['query' => request()->param()]);
		}
		if ($ordno==NULL&&$status!=2&&$status!=NULL) {
			$res=Db::name('comment')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->join('shop_memberlogin m','c.uid=m.id')->field('c.*,g.goods_name,m.username')->where('c.status',$status)->paginate(20,false,['query' => request()->param()]);
		}
		if ($status==2&&$ordno!=NULL) {
			$res=Db::name('comment')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->join('shop_memberlogin m','c.uid=m.id')->field('c.*,g.goods_name,m.username')->where('c.ordno','like',"%".$ordno."%")->paginate(20,false,['query' => request()->param()]);
		}
		if ($status!=2&&$ordno!=NULL) {
			$res=Db::name('comment')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->join('shop_memberlogin m','c.uid=m.id')->field('c.*,g.goods_name,m.username')->where('c.ordno','like',"%".$ordno."%")->where('c.status',$status)->paginate(20,false,['query' => request()->param()]);
		}
		if ($status==2&&$ordno==NULL) {
			$res=Db::name('comment')->alias('c')->join('shop_goods g','c.goodsid=g.id','LEFT')->join('shop_memberlogin m','c.uid=m.id')->field('c.*,g.goods_name,m.username')->paginate(20,false,['query' => request()->param()]);
		}
		$data = Request::instance() -> param();
        if(!isset($data['page'])){
            $data['page'] = 1; 
        }
		$this -> assign('page',$data['page']);
		return $this->fetch('user_message',['data'=>$res]);
	}
	//加载评论回复模板
	public function gettocom(){
		$cid=input('id');
		$res=Db::name('comment')->find($cid);
		return $this->fetch('reply_message',['data'=>$res]);
	}
	//回复评论
	public function postdocom(){
		$data=request()->post();
		$id=input('id');
		$res=Db::name('comment')->where('id',$id)->update($data);
		if ($res) {
			$info=Db::name('comment')->where('id',$id)->update(array('status'=>1));
			if ($info) {
				$this->success('回复成功','com/com');
			}else{
				$this->error('回复失败','com/com');
			}
		}else{
			$this->error('回复失败','com/com');
		}
	}
}



?>