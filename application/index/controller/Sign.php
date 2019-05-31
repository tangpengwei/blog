<?php

namespace app\index\controller;

use app\admin\model\user;
use think\Controller;
use think\Request;

class Sign extends Controller
{

    /**
     * 登录处理
     */
    public function in()
    {
        $request = $this->request;
        if ($request->isGet()){
            return $this->fetch();
        }
        if ($request->isPost()){
             $account = $request->param('account');
            $password = $request->param('password');
            $m = new \app\index\model\user();

            if (preg_match('/^1[3-9]\d{9}$/',$account,$match)){
                $res = $m->where('mobile',$account)->find();

            }else{
                $res = $m->where('email',$account)->find();
            }

            if ($res){
                if (password_verify($password,$res->password)){
                        session('userloginInfo',$res);
                        $this->success('登录成功');
                }else{
                    $this->error('您输入的用户明或者密码有误1');
                }
            }else{
                $this->error('您输入的用户名或者密码有误2');
            }


        }






    }

    /**
     * 注册处理
     */
    public function up()
    {
      $request= $this->request;
      //判断是否为Get请求
      if ($request->isGet()){
            return $this->fetch();
      }

      if ($request->isPost()){

          $rule = [
              'agree' => 'require',
              'mobile' => 'require|mobile|unique:user',
              'password' => 'require|confirm:rp|length:6,12'

          ];



          $msg = [
              'agree.require' => '你需要同意注册协议',
              'mobile.require' => '手机号为必填项',
              'mobile.mobile' => '请输入正确的手机号',
              'mobile.unique' => '该手机号已被使用',
              'password.require' => '密码为必填项',
              'password.confirm' => '两次密码不一致',
              'password.length' => '密码长度应在六到十二'
          ];



          $info = $this->validate($request->param(), $rule, $msg);

          if ($info !== true){

              $this->error($info);
          }
            $m = new \app\index\model\User();
            $m->mobile = $request->param('mobile');
            $m->password = password_hash($request->param('password'),PASSWORD_DEFAULT);
            $m->nickname = '新用户'.rand(10000,999999);
            if ($m->save()){
                $this->success('注册成功', url('index/Sign/in'));
            }else{
                $this->error('注册失败');
            }



      }



    }


    public function logout(){
        session('userloginInfo',null);
        $this->redirect('index/Index/index');
    }



}
