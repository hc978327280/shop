<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// return [
//     '__pattern__' => [
//         'name' => '\w+',
//     ],
//     '[hello]'     => [
//         ':id'   => ['index/hello', ['method' => 'get'], ['id' => '\d+']],
//         ':name' => ['index/hello', ['method' => 'post']],
//     ],

// ];
use think\Route;
//前台路由
Route::rule('/','index/Index');
//前台登录路由
Route::controller('logins','User');
//前台个人中心路由
Route::controller('centers','Center');
//前台地址管理路由
Route::controller('add','Address');
//前台用户个人资料路由
Route::controller('per','Personal');
//前台商品列表
Route::controller('goodslis','Goodslist');
//前台购物车路由
Route::controller('cart','Shopcart');
//前台订单列表
Route::controller('list','Shoplist');
//前台订单提交
Route::controller('pay','Pay');
//前台订单管理
Route::controller('order','Order');
//前台个人评论
Route::controller('coms','Comment');

//后台登录页的路由
Route::controller('login','Login');
//后台管理员管理路由
Route::controller('admined','admin/Admined');
//后台商品分类路由
Route::controller('product','Product');
//后台商品列表路由
Route::controller('goods','Goods');
//后台订单管理
Route::controller('orderlist','Orderlist');
//后台权限管理
Route::controller('auth','Auth');
//后台评价管理
Route::controller('com','Comment');
//后台路由
Route::rule('admin','Index');
Route::rule('/menu','Index/menu');
Route::rule('/bar','Index/bar');
Route::rule('/main','Index/main');
Route::rule('/top','Index/top');

