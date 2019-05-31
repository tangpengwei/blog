<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/30
 * Time: 下午8:54
 */
namespace app\index\controller;
use app\index\model\category;
use app\index\model\user;
use think\Controller;

class People extends Controller
{
    public function updateDate()
    {
        $Info = category::where('pid',2)->select();
        $this->assign('Info',$Info);

        $id = $this->request->param('id');
        $list = user::where('id',$id)->find();
        $this->assign('list',$list);
      return $this->fetch();
    }

    public function uploadPicture()
    {
        $Info = category::where('pid',2)->select();
        $this->assign('Info',$Info);

        $id = $this->request->param('id');
        $list = user::where('id', $id)->find();
        $this->assign('list', $list);
        return $this->fetch();


    }


    public function touxiang()
    {
        $id = $this->request->param('id');
        $image = $this->request->file('file');
        $res = $image->validate(['size'=>1048576,'ext'=> 'jpg,png,gif'])->move('static/image');
        $path = $res->getPathname();
        if (user::where('id',$id)->update(['avatar'=>$path])){
            return json(['code'=>1,'url'=>$path,'msg'=>"成功"]);
        }else{
            return json(['code'=>0,'msg'=>'失败']);
        }

    }


    public function updatePassword()
    {
        $id = $this->request->param('id');
        if ($this->request->isGet()) {
            $Info = category::where('pid',2)->select();
            $this->assign('Info',$Info);

            $list = user::where('id', $id)->find();
            $this->assign('list', $list);
            return $this->fetch();
        }

        if ($this->request->isPost()){
           $password = $this->request->only(['password_old','password_new','password_new_repeat']);
           $info = user::where('id',$id)->find();
            if (!password_verify($password['password_old'],$info->password)){
                $this->error('密码错误');
            }
            if ($password['password_new']!= $password['password_new_repeat']){
                $this->error('两次密码输入不一致');
            }
            $newpassword = password_hash($password['password_new'],1);
           if (user::where('id',$id)->update(['password'=>$newpassword])){
                $this->success('成功',url('index/Sign/logout'));
           }else{
                $this->error('失败');
           }


        }
    }

}
