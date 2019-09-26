<?php
namespace app\index\controller;
use think\Controller;
use think\captcha\Captcha;
use think\Db;
class Index extends Controller
{
    public function index()
    {	
    	
        $p=Db::name('type')->where('pid',0)->select();
        // dump($p);
        foreach ($p as $key => $value) {
            // var_dump($value);
            
            $s=Db::name('type')->where('pid',$value['id'])->select();
            if ($s) {
                    foreach ($s as $k => $v) {
                    $ss=Db::name('type')->where('pid',$v['id'])->select();
                    // dump($ss);
                    if ($ss) {
                        foreach ($ss as $k1 => $v1) {
                        // $data[$value['name']][$v['name']]=$value['id'];
                        $data[$value['name']][$v['name']][$k1]['name']=$v1['name'];
                        $data[$value['name']][$v['name']][$k1]['id']=$v1['id'];

                    }
                   
                    }
                    
                }
            }
           
        } 
        $uid=session('user.uid');
        $res=Db::name('cart')->where('uid',$uid)->count();
        return $this->fetch('index',['data'=>$data,'res'=>$res]);
   }
   
   
}
