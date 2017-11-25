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
    $data = $request->param(-1);
    if($data)
    {
      $videoModel = new Video();
      $videoModel->where("id=1")->data(["cloudinary"=>json_encode($data)])->save();
    }
  }
}