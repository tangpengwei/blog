<?php
namespace app\index\controller;

use app\admin\model\article;
use app\index\model\category;
use app\index\model\interview;
use app\index\model\user;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $Info = category::where('pid',2)->select();
        $this->assign('Info',$Info);
        $id = $this->request->param('id',0);
        $a = new article();
        if ($id){
            $list = $a->order('create_time', 'desc')->where('category_id',$id)->paginate(10);
        }else{
            $list = $a->order('create_time', 'desc')->paginate(10);
        }

       $this->assign('list',$list);
        $list1 = article::select()->count();
        $this->assign('list1',$list1);
       return $this->fetch();
    }

    public function centent()
    {
        $Info = category::where('pid',2)->select();
        $this->assign('Info',$Info);

        $id = $this->request->param('id');
        $list =  article::where('id',$id)->select()->toArray();
        $this->assign('list',$list);

        $shu = interview::count();
        $this->assign('shu',$shu);

        $num = user::with('article')->select()->toArray();
        $count = count($num[0]['article']);
        $this->assign('count',$count);



        $info =  Interview::with('author')->where('company_id',$id)->paginate(10);
        $this->assign('info',$info);
        return $this->fetch();
    }


    public function search (){
        $Info = category::where('pid',2)->select();
        $this->assign('Info',$Info);

        $keyword = $this->request->param('wd');
        //empty检查变量是否为空
        if (empty($keyword)){
            $this->redirect('index/Index/index');
        }
        //设置分页模式  并查询出符合搜索条件的列表
        $num = \think\Db::table('article')->where('title','like','%'.$keyword.'%')->count();
        $list = \think\Db::table('article')
            ->where('title','like','%'.$keyword.'%')
            ->paginate(10,false,['query'=>['wd'=>$keyword]]);

        //处理让关键字变红
        $newList = $list->toArray()['data'];
        foreach ($newList as $k=>$v){
            $newList[$k]['title'] = str_replace($keyword,('<span class="text-danger">'.$keyword.'</span>'),$v['title']);

        }
        //把我们设置的模板变量赋值给钱面的name
        $this->assign('keyword',$keyword);
        $this->assign('list',$list);
        $this->assign('newList',$newList);
        $this->assign('num',$num);
        return $this->fetch();

    }


    /**
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * 添加评论
     */

    public function add_interview(){
        $user = session('userloginInfo');
        if (empty($user)){
            $this->error('您需要登录后才能进行操作',url('index/Sign/in'));
        }
        //面试经历的内容
        $data['content'] = $this->request->param('content');
        //企业的id
        $data['company_id'] = $this->request->param('id');

        //验证数据
        $rule = [
            'content' => 'require|length:10,65535',
            'company_id' => 'require'
        ];
        $msg = [
            'content.require' => '评论详情为必填项',
            'content.length' => '评论长度错误',
            'company_id.require' => '评论信息不全'
        ];
        if ($this->validate($data,$rule,$msg) === true){
            //验证企业的有效性
            $company = \think\Db::table('article')->where('id',$data['company_id'])->find();
            if (empty($company)){
                $this->error('评论不存在');
            }
            //记录发表者的信息
            $data['uid'] = $user->id;
            //记录添加时间
            $data['create_time'] = date('Y-m-d H:i:s');
            if (\think\Db::table('interview')->data($data)->insert()){
                $this->success('添加成功');
            }else{
                $this->error('添加失败');
            }

        }else{
            $this->error($this->validate($data,$rule,$msg));
        }

    }

    /**
     * 精华帖
     */

    public function scene()
    {

        $Info = category::where('pid',2)->select();
        $this->assign('Info',$Info);
        return $this->fetch();
    }


    /**
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     * @throws \think\exception\PDOException
     * 判断点赞
     */

    public function ding(){
        //用户必须登录
        //当前登录用户的信息
        $user = session('userloginInfo');
        if (empty($user)){
            $this->error('你需要登录后才能操作');
        }
        //这是面试经历的id
        $id = $this->request->param('id');
        //数据验证
        if (empty($id)){
            $this->error('非法操作');
        }
        //一条面试经历的信息
        $info = \think\Db::table('interview')->where('id',$id)->find();
        //检验面试信息是否为null
        if (empty($info)){
            $this->error('非法操作');
        }
        //检验是否是给自己点赞
        if ($info['uid'] == $user->id){
            $this->error('不要自己给自己点赞');
        }
        //检验用户是否已经点赞过
        if (\think\Db::table('interview_ding')->where(['interview_id'=>$id, 'uid'=>$user->id])->find()){


            //取消赞
            if (\think\Db::table('interview')->where('id',$id)->setDec('ding')){
                \think\Db::table('interview_ding')->where(['interview_id' => $id,'uid'=>$user->id])->delete();
                $this->success('取消成功',null,1);
            }else{
                $this->error('操作失败');
            }
        }else{
            if (\think\Db::table('interview')->where('id',$id)->setInc('ding')){
                \think\Db::table('interview_ding')->data(['interview_id'=>$id,'uid'=>$user->id])->insert();
                $this->success('点赞成功',null,0);
            }else{
                $this->error('操作失败');
            }
        }


    }






}
