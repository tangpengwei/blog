<?php
/**
 * Created by PhpStorm.
 * User: qingyun
 * Date: 19/5/30
 * Time: 下午1:51
 */
namespace app\index\controller;

use app\admin\model\category;
use think\Controller;

class Article extends Controller
{
    /** 添加文章
     * @return mixed
     * @throws \think\Exception\DbException
     */
    public function add()
    {

        $re = $this->request;
        //判断是不是GET请求
        if ($re->isPost()){
            //获取我们所取的值
            $data = $re->only(['title','category_id','author','content','thumb','minthumb','uid']);
            //验证
            $rule = [
                'title' => 'require|length:1,50',
                'category_id' => 'require|min:1',
                'author' =>'require|length:2,10',
                'content' => 'require|length:10,65535',

            ];
            $msg=[
                'title.require' => '标题为必填项',
                'title.length:1,50' => '标题长度过长或过短',
                'category_id.require' => '请选择正确的分类信息',
                'category_id.min'=>'请选择正确的分类信息',
                'author.require'=>'作者必须有',
                'author.length' => '署名长度应在2-10之间',
                'content.require' => '文章内容为必填项',
                'content.length' =>'文章内容长度过长或者过短',

            ];
            $check = $this->validate($data,$rule,$msg);
            //数据验证不通过的话 报错
            if ($check !== true){
                $this->error($check);
            }
            //在$data数据中在添加一条aid信息
            $data['aid'] = session('adminLoginInfo')->id;
            //入库
            if (\app\index\model\article::create($data)){
                $this->success('添加成功',url('index/Index/index'));
            }else{
                $this->error('添加失败');
            }
        }
        //判断是否是GET请求
        if ($re->isGet()){
            $Info = \app\index\model\category::where('pid',2)->select();
            $this->assign('Info',$Info);

            //获取分类信息 并自定义标签
            $all = \app\index\model\category::where('pid',0)->all();
            $this->assign('all',$all);
            return $this->fetch();
        }
    }





    /**
     * 百度富文本编辑器 添加图片配置
     */
    public function ueAdd()
    {

        $CONFIG = json_decode(preg_replace("/\/\*[\s\S]+?\*\//", "", file_get_contents("static/ui/library/ue/php/config.json")), true);
        if ($this->request->isGet()){

            return json($CONFIG);
        }
        if ($this->request->isPost()){
            $image = $this->request->file('upfile');
            $allow = 'png,jpg,jpeg,gif,bmp,flv,swf,mkv,avi,rm,rmvb,mpeg,mpg,ogg,ogv,mov,wmv,mp4,webm,mp3,wav,mid,rar,zip,tar,gz,7z,bz2,cab,iso,doc,docx,xls,xlsx,ppt,pptx,pdf,txt,md';
            $res = $image->validate(['size'=>1048576,'ext'=>$allow])->move('static/uploads');
            if ($res){
                $info = [
                    "originalName"=> $res->getFilename(),
                    "name"=>$res->getSaveName(),
                    "url"=>'/'.$res->getPathname(),
                    "size"=>$res->getSize(),
                    "type"=>$res->getExtension(),
                    "state"=>'SUCCESS'
                ];
                return json($info);
            }else{
                return [
                    "state" => "ERROR"
                ];
            }


        }

    }
}