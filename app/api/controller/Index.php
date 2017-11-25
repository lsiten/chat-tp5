<?php
/*
 * @Author: lsiten 
 * @Date: 2017-11-19 15:28:41 
 * @Last Modified by: lsiten
 * @Last Modified time: 2017-11-19 15:30:00
 */
namespace app\api\controller;
use app\api\controller\Base;
use think\Request;
use \GatewayWorker\Lib\Gateway;

class Index extends Base
{
  private $return = [];
  public function _initialize(){
    parent::_initialize();
    $this->return = config('return');
  }
  public function index(Request $request){
    
  }
 /**
  * 获取图床的signatrue
  *@params $request
  *return 响应内容
  */
  public function signature(Request $request){
    hasToken();
    $cloud = $request->put('cloud');
    if("qiniu"==$cloud)
    {
      $type = $request->put('type');
      //获取七牛token
      $data = getQiniuToken($type);
      $this->return['obj'] = [
              "signature"=>$data["token"],
              "key"=>$data["key"]
            ];
      return $this->return;
    }
    else
    {
      //获取cloudinary图床
      $timestamp = $request->put('timestamp');
      $type = $request->put('type');
      if(!$timestamp || !$type)
      {
        $this->return['success'] = false;
        $this->return['code'] = 4020;
        $this->return['obj'] = ['errorMsg'=>"参数不全，请确认参数！"];
        return $this->return;
      }
      $token = getCloudinaryToken($timestamp,$type);
      $this->return['obj'] = [
                              "signature"=>$signature,
                              "folder"=>$folder,
                              "tags"=>$tags,
                            ];
      return $this->return;
    }
  }



}
