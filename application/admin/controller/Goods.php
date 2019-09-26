<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use lib\Cattree;
class Goods extends Controller{
	public function getgoods(){
			//接受搜索的数据
			$sale=input('is_sale');
			$name=input('goods_name');
			if ($name==NULL && $sale==NULL) {
				$res=Db::name('goods')->join('shop_type','shop_goods.type_id=shop_type.id','LEFT')->join('shop_product','shop_goods.id=shop_product.goods_id','LEFT')->field('shop_goods.*,shop_type.name,sum(shop_product.goods_number) as nums')->group('shop_goods.id')->where('is_delete','N')->paginate(20,false,['query' => request()->param()]);
			}elseif($name==NULL && $sale!=2){
				$res=Db::name('goods')->join('shop_type','shop_goods.type_id=shop_type.id','LEFT')->join('shop_product','shop_goods.id=shop_product.goods_id','LEFT')->field('shop_goods.*,shop_type.name,sum(shop_product.goods_number) as nums')->group('shop_goods.id')->where('is_sale',$sale)->where('is_delete','N')->paginate(20,false,['query' => request()->param()]);
			}elseif($name!=NULL && $sale==2){
				$res=Db::name('goods')->join('shop_type','shop_goods.type_id=shop_type.id','LEFT')->join('shop_product','shop_goods.id=shop_product.goods_id','LEFT')->field('shop_goods.*,shop_type.name,sum(shop_product.goods_number) as nums')->where('goods_name','like','%'.$name.'%')->group('shop_goods.id')->where('is_delete','N')->paginate(20,false,['query' => request()->param()]);
			}elseif($name!=NULL && $sale!=2){
				$res=Db::name('goods')->join('shop_type','shop_goods.type_id=shop_type.id','LEFT')->join('shop_product','shop_goods.id=shop_product.goods_id','LEFT')->field('shop_goods.*,shop_type.name,sum(shop_product.goods_number) as nums')->where('goods_name','like','%'.$name.'%')->where('is_sale',$sale)->group('shop_goods.id')->where('is_delete','N')->paginate(20,false,['query' => request()->param()]);
			}elseif ($name==NULL && $sale==2) {
				$res=Db::name('goods')->join('shop_type','shop_goods.type_id=shop_type.id','LEFT')->join('shop_product','shop_goods.id=shop_product.goods_id','LEFT')->field('shop_goods.*,shop_type.name,sum(shop_product.goods_number) as nums')->group('shop_goods.id')->where('is_delete','N')->paginate(20,false,['query' => request()->param()]);
			}
				//当前页
			$data = Request::instance() -> param();
	        if(!isset($data['page'])){
	            $data['page'] = 1; 
	        }
			$this -> assign('page',$data['page']);
			return $this->fetch('goods_list',['data'=>$res]);
	}
	//加载添加模块
	public function getadd(){
		$res=Db::name('type')->select();
		$wx=new Cattree($res,'ord');
		$res=$wx->getTree();
		return $this->fetch('goods_add',['data'=>$res]);
	}
	//商品添加
	public function postadd(){
		$data=request()->post();
		 // 获取表单上传文件
	    $files = request()->file('goods_img');
	    foreach($files as $file){
	        // 移动到框架应用根目录/public/uploads/ 目录下
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 成功上传后 获取上传信息
	           $path[]=$info->getSaveName(); 
	        }
	    }
	    $data['goods_img']=implode(',',$path);
	    $data['slt']=$path[mt_rand(0,count($path)-1)];
	    
	    $res=Db::name('goods')->insert($data);
		    if ($res) {
		    	$this->success('商品添加成功',url('Goods/goods'));
		    }else{
		    	$this->success('商品添加失败');
		    }
	}
	//删除商品
	public function getdel(){
		$id=input('id');
		$res=Db::name('goods')->delete($id);
		if ($res) {
			$this->success('删除成功',url('Goods/goods'));
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
			$res=Db::name('goods')->where("id in ($id)")->delete();
			if ($res) {
			$this->success('批量删除成功',url('Goods/goods'));
			}else{
			$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
	}
	//加载修改模板
	public function getupdate(){
		$id=input('id');
		$bb=Db::name('goods')->where('id',$id)->find();
		$bs=$bb['type_id'];
		$b=Db::name('type')->where('id',$bs)->find();
		$res=Db::name('type')->select();
		$wx=new Cattree($res,'ord');
		$res=$wx->getTree();
		return $this->fetch('goods_update',['data'=>$res,'b'=>$b,'bb'=>$bb]);
	}
	//执行修改
	public function postdoupdate(){
		$id=input('id');
		$data=request()->post();
		$data['updatetime']=date('Y-m-d H:i:s');
		$files = request()->file('goods_img');
	    foreach($files as $file){
	        // 移动到框架应用根目录/public/uploads/ 目录下
	        $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
	        if($info){
	            // 成功上传后 获取上传信息
	            $path[]=$info->getSaveName();
	        }
	    }
	    if (!empty($path)) {
	    	$data['goods_img']=implode(',',$path);
	    	$data['slt']=$path[mt_rand(0,count($path)-1)];
	    }
		$res=Db::name('goods')->where('id',$id)->update($data);
		if ($res) {
			$this->success('修改成功',url('Goods/goods'));
		}else{
			$this->success('修改失败',url('Goods/goods'));
		}
	}
	public function gettype(){
		$id=input('id');
		$res=Db::name('goods')->find($id);
		return $this->fetch('goods_type',['data'=>$res]);
	}
	//接受添加的属性
	public function postgettype(){
		$data=request()->post();
		$res['goods_id']=$data['id'];
		$res['attr_value']=$data['attr_value'];
		$attr=Db::name('goods_attr')->insert($res);
		if ($attr>0) {
			$att2=Db::name('goods_attr')->order('id desc')->find();
			$result['goods_id']=$data['id'];
			$result['goods_number']=$data['goods_number'];
			$result['attr_id']=$att2['id'];
			$pro=Db::name('product')->insert($result);
			if ($pro>0) {
				$this->success('添加成功',url('Goods/goods'));
			} else {
				$this->error('添加失败');
			}
			
		}else{
			$this->error('添加失败');
		}
		
	}
	//修改商品属性库存模板
	public function gettypeUp(){
		$gid=input('id');
		$res=Db::name('goods_attr')->where('goods_id',$gid)->select();
		$result=Db::name('goods')->find($gid);
		return $this->fetch('goods_typeup',['data'=>$res,'good'=>$result]);
	}
	//执行修改
	public function posttypeup(){
		$data=request()->post();
		$res['goods_id']=$data['goods_id'];
		$res['goods_number']=$data['goods_number'];
		$res['attr_id']=$data['attr_id'];
		$res2['goods_id']=$data['goods_id'];
		$res2['attr_value']=$data['attr_value'];
		$kc=Db::name('product')->where('attr_id',$data['attr_id'])->update($res);
		if ($kc) {
			$sx=Db::name('goods_attr')->where('id',$res['attr_id'])->update($res2);
			if ($sx) {
				$this->success('添加成功',url('Goods/goods'));
				
			}else{
			$this->error('添加失败');

			}
		}else{
			$this->error('修改失败');

		}
	}
	
}









?>