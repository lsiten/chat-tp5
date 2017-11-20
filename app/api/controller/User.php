<?php
/*
 * @Author: lsiten 
 * @Date: 2017-11-19 15:19:14 
 * @Last Modified by: lsiten
 * @Last Modified time: 2017-11-19 19:01:13
 */
namespace app\api\controller;
use app\api\model\Doguser;
use app\api\controller\Base;
use think\Request;
class User extends Base{

    public function _initialize(){
        parent::_initialize();
    }
    /*
    **function 更新狗狗信息
    **params  $request 请求对象
    **return  返回响应数据 
    */
    public function update(Request $request){
        $phoneNumber = $request->get("phoneNumber");
        $user =  new Doguser();
        $data = $user->where(["phoneNumber"=>$phoneNumber])
                     ->find();
       if(!$data)
        {
            $user-> data([
                 "phoneNumber"=>$phoneNumber
            ]);
        }
        else
        {
            $verifyCode = "123";
            $user-> data([
                 "verifyCode"=>$verifyCode
            ]);
        }
        $user->save();
         return $data;
    }
}