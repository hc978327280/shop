<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Orderlist extends Controller{
	//加载订单列表模板
	public function getorderlist(){
		$ordno=input('ordno');
		$status=input('status');
		if (empty($ordno)&&empty($status)) {
			$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,O.*")->paginate(20,false,['query' => request()->param()]);
		}
		if (!empty($ordno)&&$status==5) {
			$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,O.*")->where('o.ordno','like',"%".$ordno."%")->paginate(20,false,['query' => request()->param()]);
		}
		if (empty($ordno)&&$status==1) {
			$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,O.*")->where('o.status',$status)->paginate(20,false,['query' => request()->param()]);
		}
		if (empty($ordno)&&$status>=2&&$status!=5) {
			$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,O.*")->where('o.status','>=',2)->paginate(20,false,['query' => request()->param()]);
		}
		if (empty($ordno)&&$status==5) {
			$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,O.*")->paginate(20,false,['query' => request()->param()]);
		}
		
		
		//当前页
			$data = Request::instance() -> param();
	        if(!isset($data['page'])){
	            $data['page'] = 1; 
	        }
			$this -> assign('page',$data['page']);
		return $this->fetch('order_list',['data'=>$res]);
	}
	//订单查看模板
	public function getorder(){
		$id=input('id');
		$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id','LEFT')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field("p.name as pname,c.name as cname,r.name as rname,a.*,O.*")->where('o.id',$id)->find();
		$info=Db::name('order')->alias('o')->join('shop_order_info i','o.ordno=i.ordno','LEFT')->join('shop_goods g','i.goodsid=g.id','LEFT')->field('i.num*i.price as total,o.money,i.num,i.price,.i.attr,g.goods_name,g.slt')->where('o.id',$id)->select();
		$money=Db::name('order')->find($id);
		// dump($money);
		return $this->fetch('order_detail',['data'=>$res,'info'=>$info,'money'=>$money]);
	}
	//订单单条删除
	public function getdel(){
		// dump(input('id'));
		$id=input('id');
		$res=Db::name('order')->delete($id);
		if ($res) {
			$this->redirect('orderlist/orderlist');
		}else{
			$this->error('删除失败');
		}
	}
	//订单批量删除
	public function postdodel(){
		//获取批量删除的id		
		if(isset($_POST['id'])){
			$id=implode(',',$_POST['id']);
			$res=Db::name('order')->where("id in ($id)")->delete();
			if ($res) {
				$this->redirect('orderlist/orderlist');
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
		// dump($data);
	}
	//发货
	public function postfahuo(){
		$id=input('id');
		$res=Db::name('order')->where('id',$id)->update(array('status'=>3));
		if ($res) {
				$this->redirect('orderlist/orderlist');
		}else{
			$this->error('发货失败');
		}
	}
}



?>