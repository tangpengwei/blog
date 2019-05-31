<?php

namespace app\admin\model;

use think\Model;

class article extends Model
{
    //开启自动设置时间
    protected $autoWriteTimestamp = true;
    public function category()
    {
        //一对多关联
        return $this->belongsTo('category','category_id');
    }
}
