<?php
namespace app\admin\controller;
use think\Controller;
use think\captcha\Captcha;
use think\Session;
class Index extends Controller
{
    public function index()
    {   //判断是否登录
        if (!empty(Session::get('name','think'))) {
          return $this->fetch('index');
        }else{
          return $this->success('请先登录','login/login');
        }
        
   }
   public function menu(){
   	return $this->fetch('menu');
   }
   public function main(){
   	return $this->fetch('main');
   }
   public function bar(){
   	return $this->fetch('bar');
   }
   public function top(){
   	return $this->fetch('top');
   }
}
