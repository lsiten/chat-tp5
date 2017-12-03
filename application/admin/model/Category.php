<?php

namespace app\admin\model;

use think\Model;

class Category extends Model
{
    
    public function modelm()
    {
        return $this->hasOne('Modelm','id','modelid')->field('tablename,type');
    }
}
