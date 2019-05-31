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
//登录
Route::rule('login','admin/Login/in')->method('GET,POST');
//登出
Route::get('logout','admin/Login/out');
//注册
Route::rule('registered','admin/Login/registered')->method('GET,POST');
//添加文章
Route::rule('article-add','admin/Article/add')->method('GET,POST');
//登陆后主业
Route::get('index$','admin/Index/index');