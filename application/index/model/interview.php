<?php

namespace app\index\model;

use think\Model;

class interview extends Model
{
    public function author(){
        return $this->belongsTo('user','uid');
    }
}
