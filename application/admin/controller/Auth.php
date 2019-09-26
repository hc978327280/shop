<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Auth extends Controller{
	//加载角色列表
	public function getauth(){
		$res=Db::name('role')->paginate(20,false,['query' => request()->param()]);
				//当前页
			$data = Request::instance() -> param();
	        if(!isset($data['page'])){
	            $data['page'] = 1; 
	        }
			$this -> assign('page',$data['page']);
		return $this->fetch('auth_list',['res'=>$res]);
	}
	//加载添加角色模板
	public function getaddauth(){
		return $this->fetch('auth_add');
	}
	//执行添加角色
	public function postadd(){
		$data=request()->post();
		$res=Db::name('role')->insert($data);
		if ($res) {
			$this->success('添加角色成功','auth/auth');
		}else{
			$this->error('添加角色失败');
		}
	}
	//编辑角色的模板
	public function geteditauth(){
		// 查询编辑的角色信息
		$info=Db::name('role')->find(input('rid'));
		$data=$this->getnodes();
		// 获取当前角色拥有的权限节点
		$node=Db::name('role_node')->where('rid',input('rid'))->field('nid')->select();
		if ($node) {
				foreach($node as $key=>$value){
				$node1[]=$value['nid'];
				// dump($node1);
				$this->assign('node',$node1);
			}
			// dump($node1);
		}
		

		return $this->fetch('auth_edit',['info'=>$info,'data'=>$data]);
	}
	//加载节点
	public function getjiedian(){
		$res=Db::name('node')->paginate(20,false,['query' => request()->param()]);
			//当前页
			$data = Request::instance() -> param();
	        if(!isset($data['page'])){
	            $data['page'] = 1; 
	        }
			$this -> assign('page',$data['page']);
		return $this->fetch('jiedian_list',['res'=>$res]);
	}
	//加载添加节点模板
	public function getaddjiedian(){
		$control=Db::name('node')->where('pid',0)->select();
		return $this->fetch('edit_jiedian',['control'=>$control]);
	}
	//执行添加节点
	public function postaddjie(){
		$data=request()->post();
		// dump($data);
		$res=Db::name('node')->insert($data);
		if ($res) {
			$this->success('添加角色成功','auth/jiedian');
		}else{
			$this->error('添加节点失败');
		}
	}
	//加载权限分配模板
	public function getquanxian(){
		$roles=Db::name('role')->where('status',1)->select();
		$data=$this->getnodes();
		
		return $this->fetch('quanxian',['roles'=>$roles,'data'=>$data]);
	}
	//将分配的权限写入数据表
	public function postaddauth(){
		$params=request()->post();
		// dump($params);
		// dump($params);
		/*
		array(
			array('rid'=>3,'nid'=>2),
			array('rid'=>3,'nid'=>3),
			array('rid'=>3,'nid'=>5),
			array('rid'=>3,'nid'=>6)
		)
		 */
		//遍历数据
		foreach ($params['nid'] as $key => $value) {
			$info=Db::name('role_node')->where('rid',$params['rid'])->where('nid',$value)->find();
			// dump($info);
			//判断该节点是否已经添加
			if (!$info) {
				$data[$key]['rid']=$params['rid'];
				$data[$key]['nid']=$value;
		// dump($data);

			}

		}
		$res=Db::name('role_node')->insertAll($data);
		if($res==count($data)){
			$this->success('权限分配成功');
		}else{
			$this->error('权限分配异常');
		}
	}
	//修改权限
	public function postxiugai(){
		$params=request()->post();
		// dump($params);
		$res=Db::name('role_node')->where('rid',$params['rid'])->delete();
		if ($res) {
			foreach ($params['nid'] as $key => $value) {
				$data[$key]['rid']=$params['rid'];
				$data[$key]['nid']=$value;

			}
			// dump($data);
		}
		$res=Db::name('role_node')->insertAll($data);
		if($res==count($data)){
			$this->success('权限分配成功');
		}else{
			$this->error('权限分配异常');
		}
		
	}
	private function getnodes(){
		// 获取控制器节点信息
		$node=Db::name('node')->where('pid',0)->select();
		foreach ($node as $key => $value) {
			$child=Db::name('node')->where('pid',$value['id'])->select();
			$data[$key]=$value;
			$data[$value['ename']]=$child;
		}
		return $data;
	}
	//删除节点
	public function getdel(){
		$id=input('id');
		$res=Db::name('node')->delete($id);
		if ($res) {
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
	//删除角色
	public function gettodel(){
		$id=input('id');
		$res=Db::name('role')->delete($id);
		if ($res) {
			$this->success('删除成功');
		}else{
			$this->error('删除失败');
		}
	}
}



?>