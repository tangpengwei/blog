<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/20
 * Time: 下午7:09
 */
namespace app\admin\controller;

use app\admin\model\admin;
use think\Controller;

class Login extends Controller
{
    /**
     * 退出登录
     */
    public function out()
    {
        //把session置为null
        session('adminLoginInfo',null);
        //直接跳转
        $this->redirect('admin/Login/in');
    }



    /**
     * 登录操作
     */
    public function in()
    {
        $re = $this->request;
        if ($re->isPost()){
            //接受数据
            $data = $re->only(['mobile','password']);
            //验证数据
            $rule = [
                'mobile' => 'require|mobile',
                'password' =>'require|length:6,12'
            ];
            $msg= [
                'mobile.require' => '手机号为必填项',
                'mobile.mobile' => '手机号格式错误',
                'password.require' => '密码为必填项',
                'password.length' => '密码长度不对'
            ];
            $info = $this->validate($data,$rule,$msg);
            //验证数据不为真
            if ($info!== true){
                //报错
                return $this->error($info);
            }
            //找到库里与前台传过来的手机号对比 看是否能找到一条数据
            $admin = admin::where('mobile',$data['mobile'])->find();
            if (!$admin){
                //如果找不到 报错
                $this->error('您输入的手机号或者密码有误');
            }
            //使用hash验证密码是否和库里的密码是否一样
            if (password_verify($data['password'],$admin->password)){
                //登录成功 记录session值
                session('adminLoginInfo',$admin);
                //提示成功 并跳转页面
                $this->success('成功',url('admin/Index/index'));
            }else{
                //如果密码不一致  报错
                $this->error('您输入的手机号或则密码有误');
            }





        }
        if ($re->isGet()){
            return $this->fetch();
        }
    }
}


















