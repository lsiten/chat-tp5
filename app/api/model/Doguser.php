<?php
/*
 * @Author: lsiten 
 * @Date: 2017-11-19 15:28:58 
 * @Last Modified by:   lsiten 
 * @Last Modified time: 2017-11-19 15:28:58 
 */
namespace app\api\model;
use think\Model;
class Doguser extends Model{
    protected $autoWriteTimestamp = true;
     // 定义时间戳字段名
     protected $createTime = 'createAt';
     protected $updateTime = 'updateAt';

}