<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
class Order extends Controller{
	//加载订单管理模板
	public function getorder(){
		$allOrder=Db::name('order')->alias('o')->join('shop_order_info i','o.ordno=i.ordno','RIGHT')->join('shop_goods g','i.goodsid=g.id')->field('i.*,o.status,o.money,o.ctime,g.goods_name,g.slt')->where('uid',session('user.uid'))->select();
		// dump($allOrder);
		if ($allOrder) {
				foreach ($allOrder as $key => $value) {
				$i=0;
				foreach ($allOrder as $k => $v) {
						
					if($value['ordno']==$v['ordno']){
						$resr[$value['ordno']]['ordno']=$v['ordno'];
						$resr[$value['ordno']]['money']=$v['money'];
						$resr[$value['ordno']]['ctime']=$v['ctime'];
						$resr[$value['ordno']]['s']=$v['status'];
						switch($v['status']){					
							case 1:$resr[$value['ordno']]['status']='未支付';break;
							case 2:$resr[$value['ordno']]['status']='未发货';break;
							case 3:$resr[$value['ordno']]['status']='待收货';break;
							case 4:$resr[$value['ordno']]['status']='订单完成';break;
							case 5:$resr[$value['ordno']]['status']='订单完成';break;
						}
						switch($v['status']){					
							case 1:$resr[$value['ordno']]['sta']='一键支付';break;
							case 2:$resr[$value['ordno']]['sta']='提醒发货';break;
							case 3:$resr[$value['ordno']]['sta']='确认收货';break;
							case 4:$resr[$value['ordno']]['sta']='订单评价';break;
							case 5:$resr[$value['ordno']]['sta']='删除订单';break;
						}
						$resr[$value['ordno']][$i]=$v;					
						$i++;
					}
				}
				
			}
			
			$data1=$this->bianli(1);
			$data2=$this->bianli(2);
			$data3=$this->bianli(3);
			$data4=$this->bianli(4);
			// dump($data1);
			return $this->fetch('order',['data'=>$resr,'data1'=>$data1,'data2'=>$data2,'data3'=>$data3,'data4'=>$data4]);
		}else{
			return $this->fetch('orders');
		}
		
	}
	//确认收货
	public function getupdate(){
		$ordno=input('ordno');
		$res=Db::name('order')->where('ordno',$ordno)->update(array('status'=>4));
		if ($res) {
			$this->redirect('/order/order');
		}else{
			$this->error('收货失败');
		}
	}
	//删除订单
	public function getdel(){
		$ordno=input('ordno');
		$res=Db::name('order')->where('ordno',$ordno)->delete();
		if ($res) {
			$result=Db::name('order_info')->where('ordno',$ordno)->delete();
			if ($result) {
				$this->redirect('/order/order');
			}else{
				$this->error('删除失败');
			}
			
		}else{
			$this->error('删除失败');
		}
	}

	//加载订单详情
	public function getinfo(){
		$ordno=input('ordno');
		if ($ordno) {
			$res=Db::name('order')->alias('o')->join('shop_address a','o.addressid=a.id')->join('shop_province p',"a.province=p.code","LEFT")->join('shop_city c',"a.city=c.code","LEFT")->join('shop_area r',"a.country=r.code","LEFT")->field('p.name as pname,c.name as cname,r.name as rname,a.*')->where('ordno',$ordno)->find();
		}
			$allOrder=Db::name('order')->alias('o')->join('shop_order_info i','o.ordno=i.ordno','RIGHT')->join('shop_goods g','i.goodsid=g.id')->field('i.*,o.status,o.money,o.ctime,g.goods_name,g.slt')->where('o.ordno',$ordno)->select();
			// dump($allOrder);
			if ($allOrder) {
					foreach ($allOrder as $key => $value) {
					$i=0;
					foreach ($allOrder as $k => $v) {
							
						if($value['ordno']==$v['ordno']){
							$resr[$value['ordno']]['ordno']=$v['ordno'];
							$resr[$value['ordno']]['money']=$v['money'];
							$resr[$value['ordno']]['ctime']=$v['ctime'];
							$resr[$value['ordno']]['s']=$v['status'];
							switch($v['status']){					
								case 1:$resr[$value['ordno']]['status']='未支付';break;
								case 2:$resr[$value['ordno']]['status']='未发货';break;
								case 3:$resr[$value['ordno']]['status']='待收货';break;
								case 4:$resr[$value['ordno']]['status']='订单完成';break;
								case 5:$resr[$value['ordno']]['status']='订单完成';break;
							}
							switch($v['status']){					
								case 1:$resr[$value['ordno']]['sta']='一键支付';break;
								case 2:$resr[$value['ordno']]['sta']='提醒发货';break;
								case 3:$resr[$value['ordno']]['sta']='确认收货';break;
								case 4:$resr[$value['ordno']]['sta']='订单评价';break;
								case 5:$resr[$value['ordno']]['sta']='删除订单';break;
							}
							$resr[$value['ordno']][$i]=$v;					
							$i++;
						}
					}
					
				}
			}
			
		return $this->fetch('orderinfo',['res'=>$res,'data'=>$resr]);
	}
	//加载物流模板
	public function getwuliu(){
		return $this->fetch('logistics');
	}
	//加载评论模板
	public function getcom(){
		$ordno=input('ordno');
		$res=Db::name('order_info')->alias('i')->join('shop_goods g','g.id=i.goodsid','LEFT')->field('g.goods_name,g.slt,i.*')->where('i.ordno',$ordno)->select();
		// dump($res);
		return $this->fetch('commentlist',['data'=>$res]);
	}
	//提交用户评论
	public function postdocom(){
		 $request = Request::instance();

		// $data['uid']=session("user.uid");
	    $data = $request->post();//获取post上传的内容
	    $i=$data['ordno'][0];
	    // dump($i);
	    $files=$request->file('img');//获取文件
		    foreach($files as $key=>$file){
	        // 移动到框架应用根目录/public/uploads/ 目录下
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 成功上传后 获取上传信息
	            // 输出 jpg
	            $data['img'][$key]=$info->getSaveName();
	            
	        }  
	    }
	    
	    foreach ($data as $key => $value) {
	    	foreach ($value as $k => $v) {
	    		$res[$k][$key]=$v;
	    	}
	    }
	   $infoAll=Db::name('comment')->insertAll($res);
	   if ($infoAll>0) {
	   		$up=Db::name('order')->where('ordno',$i)->update(array('status'=>5));
	   		if ($up) {
	   			$this->success('评论成功',url('/order/order'));
	   		}else{
	   			$this->error('评论失败');
	   		}
	   }else{
	   	 $this->error('评论失败');
	   }

	}
	










	//遍历函数
	public function bianli($status=''){
		 
			$allOrder=Db::name('order')->alias('o')->join('shop_order_info i','o.ordno=i.ordno','RIGHT')->join('shop_goods g','i.goodsid=g.id')->field('i.*,o.status,o.money,o.ctime,g.goods_name,g.slt')->where('o.status',$status)->where('uid',session('user.uid'))->select();
		// dump($allOrder);
		if ($allOrder) {
			foreach ($allOrder as $key => $value) {
			$i=0;
			foreach ($allOrder as $k => $v) {
					
				if($value['ordno']==$v['ordno']){
					$resr[$value['ordno']]['ordno']=$v['ordno'];
					$resr[$value['ordno']]['money']=$v['money'];
					$resr[$value['ordno']]['ctime']=$v['ctime'];
					$resr[$value['ordno']]['s']=$v['status'];
					switch($v['status']){					
						case 1:$resr[$value['ordno']]['status']='未支付';break;
						case 2:$resr[$value['ordno']]['status']='未发货';break;
						case 3:$resr[$value['ordno']]['status']='待收货';break;
						case 4:$resr[$value['ordno']]['status']='订单完成';break;
						case 5:$resr[$value['ordno']]['status']='订单完成';break;

					}
					switch($v['status']){					
						case 1:$resr[$value['ordno']]['sta']='一键支付';break;
						case 2:$resr[$value['ordno']]['sta']='提醒发货';break;
						case 3:$resr[$value['ordno']]['sta']='确认收货';break;
						case 4:$resr[$value['ordno']]['sta']='订单评价';break;
						case 5:$resr[$value['ordno']]['sta']='删除订单';break;
					}
					$resr[$value['ordno']][$i]=$v;					
					$i++;
				}
			}
			
		}
		return $resr;
		}
		
	}
}



?>