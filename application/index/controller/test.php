<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/30
 * Time: 下午8:21
 */
namespace app\index\controller;

use think\route\dispatch\Controller;

class test extends \think\Controller
{
    public function xx()
    {

       $a =  \app\index\model\article::select();
       $this->assign('a',$a);
//       print_r($a);
       return $this->fetch();
    }
}