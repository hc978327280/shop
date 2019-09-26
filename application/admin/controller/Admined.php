<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\paginator\driver\Bootstrap;
use think\Request;
class Admined extends Allow
{
	public function getadmined(){
		//接受搜索数据
		$role=Db::name('role')->where('status',1)->select();
		// dump($role);
		$grade=input('grade');//等级
		$name=input('name');//昵称
		if (empty($grade)&&$grade==0) {
			$res = Db::name('admin')->alias('d')->join('shop_admin_role a','a.uid=d.id','LEFT')->join('shop_role r','r.id=a.rid','LEFT')->field('d.*,r.name as cname')->where('d.name','like',"%".$name."%")->paginate(20,false,['query' => request()->param()]);

		}
		if ($grade!=0&&!empty($name)) {
			$res = Db::name('admin')->alias('d')->join('shop_admin_role a','a.uid=d.id','LEFT')->join('shop_role r','r.id=a.rid','LEFT')->field('d.*,r.name as cname,r.*')->where('d.name','like',"%".$name."%")->where('r.id',$grade)->paginate(20,false,['query' => request()->param()]);
		}
		if (empty($name)&&$grade!=0) {
			$res = Db::name('admin')->alias('d')->join('shop_admin_role a','a.uid=d.id','LEFT')->join('shop_role r','r.id=a.rid','LEFT')->field('d.*,r.name as cname,r.*')->where('r.id',$grade)->paginate(20,false,['query' => request()->param()]);
		}
		if (empty($name)&&$grade==0) {
			$res = Db::name('admin')->paginate(20,false,['query' => request()->param()]);
			
		}
		// $ress = 
		// dump($ress);
		$data = Request::instance() -> param();
        if(!isset($data['page'])){
            $data['page'] = 1; 
        }
		$this -> assign('page',$data['page']);
		return $this->fetch('admined_list',['data'=>$res,'role'=>$role]);
	}
	//添加管理员页面
	public function getadminedadduser(){
		$role=Db::name('role')->where('status',1)->select();
		return $this->fetch('admined_adduser',['role'=>$role]);
	}
	//添加管理员
	public function postadduser(){
		$data['name']=input('name');
		$data['user']=input('user');
		$data['pwd']=md5(input('pwd'));
		$data['status']=input('status');
		// dump($data);
		$user=Db::name('admin')->where('name',$data['name'])->find();
		// dump($user);/
		if (!$user) {
			// 启动事务
			Db::startTrans();
			try{
			    Db::name('admin')->insert($data);
				$role['rid']=input('rid');
			    // 获取最后一次插入的主键id
				$role['uid']=Db::name('admin')->getLastInsID();
				Db::name('admin_role')->insert($role);
			    // 提交事务
			    Db::commit();
			    $code=0;    
			} catch (\Exception $e) {
			    // 回滚事务
			    Db::rollback();
			    $code=1;
			}
			if($code==0){
				$this->success('添加成功','admined/admined','',1);
			}else{
				$this->error('添加失败');
			}

		}else{
			$this->error('用户名已存在');
		}
		
	}
	//删除管理员
	public function getdel(){
		$id=input('id');
		$res=Db::name('admin')->where('id',$id)->delete();
		if ($res) {
		$res1=Db::name('admin_role')->where('uid',$id)->delete();
			$this->success('删除成功','admined/admined','',1);
		}else{
			$this->error('删除失败');
		}
	}
	//批量删除
	public function postdodel(){
		//获取批量删除的id
		if(isset($_POST['id'])){
			$id=implode(',',$_POST['id']);
			$res=Db::name('admin')->where("id in ($id)")->delete();
			if ($res) {
				$res=Db::name('admin_role')->where("uid in ($id)")->delete();

				$this->success('批量删除成功','admined/admined','',1);
			}else{
				$this->error('删除失败');
			}
		}else{
			$this->error('删除失败');
		}
		
		
	}
	//编辑管理员回填
	public function getupdate(){
		$id=input('id');
		// dump($id);
		// $role=Db::name('role')->where('status',1)->select();
		// $res=Db::name('admin')->alias('a')->join('shop_admin_role r','a.id=r.uid','LEFT')->field('a.*,r.rid')->select();
		$res=Db::name('admin')->find($id);
		$role=Db::name('role')->where('status',1)->select();
		$rols=Db::name('admin_role')->where('uid',$id)->select();
		// dump($rols);
		foreach($rols as $key=>$value){
			$arr[]=$value['rid'];
		}
		$res['rid']=$arr;
		// dump($res);
		// $res['rid']=explode(',',$res['rid']);
		return $this->fetch('admined_upuser',['data'=>$res,'role'=>$role]);
	}
	//编辑管理员回填
	public function postUpdate(){
		
		//修改数据
			// $this->success('数据修改成功','admined','',1);
		// 启动事务
			Db::startTrans();
			try{
			  	$uid=input('id');
				$data['name']=input('name');
				$data['user']=input('user');
				$data['pwd']=input('pwd');
				$data['status']=input('status');
				$result=Db::name('admin')->find($uid);
				//判断密码
				if ($result['pwd']==$data['pwd']) {
					$data['pwd']=input('pwd');
				}else{
					$data['pwd']=md5(input('pwd'));
				}
				Db::name('admin')->where('id',$uid)->update($data);
				$params=request()->post();
				// $rid=implode(',',$params['rid']);
				$info=Db::name('admin_role')->where('uid',$uid)->find();
				// 判断是执行添加还是执行修改
				if($info){
					// 执行修改
					Db::name('admin_role')->where('uid',$uid)->delete();
					foreach ($params['rid'] as $key => $value) {
						$res['uid']=$info['uid'];
						$res['rid']=$value;
						Db::name('admin_role')->insert($res);
					}
				}else{
					// 执行添加
					foreach ($params['rid'] as $key => $value) {
						$res[$key]['uid']=$info['uid'];
						$res[$key]['rid']=$value;

					}
					Db::name('admin_role')->insertAll($res);
				}
			    // 提交事务
			    Db::commit(); 
			    $code=0;   
			} catch (\Exception $e) {
			    // 回滚事务
			    Db::rollback();
			    $code=1;
			}
			if($code==0){
				$this->success('修改成功','admined/admined','',1);
			}else{
				$this->error('修改失败');
			}

	}
}
?>