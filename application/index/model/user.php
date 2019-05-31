<?php

namespace app\index\model;

use think\Model;

class user extends Model
{
    protected $autoWriteTimestamp = true;
    public function article()
    {
        return $this->hasMany('article','aid');
    }
}
