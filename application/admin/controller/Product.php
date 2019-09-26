<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use lib\Cattree;
use think\Request;
use think\paginator\driver\Bootstrap;

class Product extends Controller{
	public function getproduct(){
		$res=$this->getttree();
		// dump($result);
		return $this->fetch('product_category',['data'=>$res]);
	}
	public function getadd(){
		$res=$this->getttree();
		return $this->fetch('product_add',['data'=>$res]);
	}
	//插入分类
	public function postdoadd(){
		$data['name']=input('name');
		$data['pid']=input('pid');
		$result=Db::name('type')->where('id',$data['pid'])->find();
		// $a=Db::name('type')->order('id desc')->find();
		// $data['ord']=$a['ord']+1;
		// dump($a);
		//拼接id
		$data['path']=($result['path'].$data['pid'].',');
		// dump($data['path']);
		$res=Db::name('type')->insert($data);
		if ($res) {
			$this->success('添加成功','product','',1);
		}else{
			$this->error('添加失败');
		}

	}
	//加载修改分类模块
	public function getupdate(){
		$id=input('id');
		$res=$this->getttree();
		$b=Db::name('type')->where('id',$id)->find();
		return $this->fetch('product_update',['data'=>$res,'b'=>$b]);	
	}
	//实现修改
	public function postupdate(){
		$pid=input('pid');
		$name=input('name');
		$id=input('id');
		$res=$this->getttree();
		//判断是否修改父级
		if ($res[$id]['pid']==$pid) {
			//没有修改父级
			$data['name']=$name;
			$data['pid']=$pid;
			$result=Db::name('type')->where('id',$id)->update($data);
			if ($result) {
				$this->success('修改成功','product','',1);
			}else{
				$this->error('修改失败');
			}
		}else{
			//判断有没有子类
			if (empty($res[$id]['child'])) {
				//父级分类的修改
				$info=Db::name('type')->where('id',$pid)->find();
				$data['name']=$name;
				$data['pid']=$pid;
				$data['path']=$info['path'].$pid.',';
				$result=Db::name('type')->where('id',$id)->update($data);
				if ($result) {
					$this->success('修改成功','product','',1);
				}else{
					$this->error('修改失败');
				}
			}else{
				$this->error('有子类，修改失败');
			}
		}
		
	}
	//删除分类
	public function getdel(){
		$id=input('id');
		$res=$this->getttree();
		
		//如果删除的分类下面有子类，连带子类一起删除
		$idd=$res[$id]['child'].','.$id;
		$tj=ltrim($idd,',');
		$b=Db::name('type')->where("id in ($tj)")->delete();
		if ($b) {
			$this->success('删除成功','product','',1);
		}else{
			$this->error('添加失败');
		}
	}
	//商品排序
	public function postlistOrd(){
		foreach ($_POST['ord'] as $key=>$value){
			$b=Db::name('type')->where('id',$_POST['id'][$key])->update(array('ord'=>$_POST['ord'][$key]));
			if ($b>0) {
			$this->success('修改成功','product','',1);
		}else{
			$this->error('修改失败','product','',1);
		}

		}
		
		
		
	}




	//无限分类
	private function getttree($delimiter='|--'){
		$res=Db::name('type')->select();
		$wx=new Cattree($res,'ord');
		$res=$wx->getTree();
		return $res;
	}
}

?>