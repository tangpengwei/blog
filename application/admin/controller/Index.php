<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/20
 * Time: 下午7:57
 */
namespace app\admin\controller;
use app\admin\model\category;
use think\Controller;

class Index extends Controller
{
    /**
     * @return mixed
     * 后台登录页
     */
    public function index()
    {
       return   $this->fetch();
    }

    /**
     * @return mixed
     * 框架首页
     */
    public function console()
    {
        return $this->fetch();
    }

    /**
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *
     *     添加新闻
     */

    public function addCategory()
    {
        //把请求赋值给$re
        $re = $this->request;
        if ($re->isGet()){
            //接收ajax传过来的值
            $pid = $re->param('id',0);
            //如果值不为null
            if (empty($pid)){
                //把"顶级分类"定义为标签 供前端使用
                $this->assign('parentName','顶级分类');
            }else{
                //在数据库中的category表中找到所对应的name的属性值
                $parentName = category::where('id',$pid)->value('name');
                //如果数据库存在的话
                if (!$parentName){
                    //报错
                    $this->error('非法操作');
                }
                //把找到的这个name名自定义为标签 供前端使用
                $this->assign('parentName',$parentName);
            }
            //把传过来的id值在自定义标签
            $this->assign('pid',$pid);
            return $this->fetch();
        }
        //如果请求为POST请求
        if ($re->isPost()){
            //接收ajax传过来的值
            $name = $re->param('name');
            $pid = $re->param('pid',0);
            //如果传过来的name的长度不符合2-10
            if (mb_strlen($name,'utf-8')>10 || mb_strlen($name,'utf-8')<2){
                //报错
                $this->error('分类名称长度应在2-10位之间');
            }
            //同一个父级下不能重名
            $where = ['pid'=> $pid,'name'=>$name];
            //判断category表中是否有符合数据的值
            if (category::where($where)->find()){
                //报错
                $this->error('该分类已经存在');
            }


            if ($pid == 0){
                //顶级分类的处理
                $level = 0;
                $path = '0-';
            }else{
                //查找是否有符合数据的值
                $parent = category::where('id',$pid)->find();
                if (empty($parent)){
                    $this->error('非法操作');
                }
                $level = $parent->level +1;
                $path = $parent->path .$pid . '-';
            }

            //入库
            $data =[
                    'name' => $name,
                    'pid'=> $pid,
                    'level'=> $level,
                    'path' => $path,
                    'type'=> $this->request->param('type')
                ];
                if (category::create($data)){
                    $this->success('写入成功');
                }else{
                    $this->error('写入失败');
                }



//            //入库
//            //如果查找不到  level就为空 就不在库里存储level
//            if (empty($parent)){
//                $data =[
//                    'name' => $name,
//                    'pid'=> $pid,
//                ];
//                if (category::create($data)){
//                    $this->success('写入成功');
//                }else{
//                    $this->error('写入失败');
//                }
//            }else{
//                //反之则直接把他存进数据库  说明（level是用来使各个等级有分层）
//                $data =[
//                    'name' => $name,
//                    'pid'=> $pid,
//                    'level'=> $parent->level +1
//                ];
//                if (category::create($data)){
//                    $this->success('写入成功');
//                }else{
//                    $this->error('写入失败');
//                }
//            }

        }
    }


    /**
     * @return mixed|string
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     *      分类列表
     */
    public function categoryList()
    {
        //如果使用的是ajax请求
        if ($this->request->isAjax()){
            //接收数据
            $pid =$this->request->param('id',0);
            //查出库里所有符合条件的值
            $list = category::where('pid',$pid)->select();
            //定义一个变量 用来存储 所有遍历的值
            $str = '';
            foreach ($list as $v){
                //定义一个变量 用来保存空格
                $space = '';
                for ($i=0;$i<$v['level'];$i++){
                    $space .= '&nbsp;&nbsp;&nbsp;&nbsp';
                }
                //定义一个url
                $url = url('admin/Index/addCategory',['id'=>$v['id']]);
                //拼接一个前端字符串
                $str .= <<<aaa
                        <tr class = "x{$pid}">
                        <td>{$v['id']}</td>
                        <td>{$space}|--{$v['name']}</td>
                        <td><a href="{$url}">添加</a></td>
                        <td><a data-id = "{$v['id']}" class="point-e children" data-op ="plus"><i class="fa fa-plus"></i></a></td>
                        </tr>
aaa;

            }
            //把这个字符串通过ajax提交到前端
            return $str;
        }else{
            //如果不是ajax请求
            //找到所有的顶级目录下的新闻
            $list = category::where('pid',0)->select();
            //自定义标签
            $this->assign('list',$list);
            //返回 并调用fetch方法
            return $this->fetch();
        }
    }

    public function categoryTree()
    {
        $all = category::select()->toArray();
        $new = $this->toTree($all);
        $this->assign('data',json_encode($new));
        return $this->fetch();
    }


    /** 将一个记录层级结构的二维数组转换成树形结构
     * @param array $data 记录有层级信息的二维数组
     * @param int $pid 从pid为哪个开始
     * @return  array
     */
    public function toTree($data,$pid=0)
    {
        $newData = [];
        foreach ($data as $v){
            if ($v['pid'] == $pid){
                //找儿子
                $v['text'] = $v['name'];
                $v['children'] = $this->toTree($data,$v['id']);
                $newData[]= $v;
            }
        }
        return $newData;
    }

}






























