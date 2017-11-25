<?php
/*
 * @Author: lsiten 
 * @Date: 2017-11-19 15:28:41 
 * @Last Modified by: lsiten
 * @Last Modified time: 2017-11-19 15:30:00
 */
namespace app\api\controller;
use app\api\controller\Base;
use app\api\model\Video;
use think\Request;

class Notify extends Base
{
  private $return = [];
  public function _initialize(){
    parent::_initialize();
    $this->return = config('return');
  }
  public function video(Request $request){
    $data = $request->param()."111SSS";
    $videoModel = new Video();
    $videoModel->where("1=1")->save(["cloudinary"=>json_encode($data)]);
  }
}