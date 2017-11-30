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
    //数据返回模版
    private $return = [];
    //短信发送平台配置
    private $smsConfig = []; 
    public function _initialize(){
        parent::_initialize();
        $this->return = config("return");
        $this->smsConfig = config("smsConfig");
    }
    /*
    **function 用户登陆
    **params  $request 请求对象
    **return  返回响应数据 
    */
    public function signup(Request $request){
            $isUpdate = true;
            $phoneNumber = $request->put("phoneNumber");
            if(!$phoneNumber)
            {
                $this->return['success'] = false;
                $this->return['code'] = 4020;
                $this->return['obj'] = ['errorMsg'=>"电话号码不正确！"];
                return $this->return;
            }
            $user =  new Doguser();
            $data = $user->where(["phoneNumber"=>$phoneNumber])
                        ->find();
            $verifyCode = speakeasy();
            $accessToken = uuid();
            if(!$data)
            {
                $sqlData = [
                    "verifyCode" => $verifyCode,
                    "accessToken" => $accessToken,
                    "avatar" => "http://res.cloudinary.com/lsiten/image/upload/v1511313680/appDog/avatar/dog.jpg",
                    "nickname"=>"小狗宝",
                    "phoneNumber"=>$phoneNumber
                ];
                $isUpdate = false;
            }
            else
            {
                $data = $data->toArray();
                $sqlData = [
                    "id"=>$data["id"],
                    "verifyCode"=>$verifyCode
                ];
            }
            $id = $user->isUpdate($isUpdate)->save($sqlData);
            //判断是否数据库插入成功，如果数据库操作成功，则发送短信验证码，设置返回数据
            if($id)
            {
                //发送短信验证码
                $res = json_decode(sendMessageByLuosimao($verifyCode, $phoneNumber),true);
                //如果发送成功，则返回数据，如果没有发送成功，则返回错误
                if(0!==$res['error'])
                {
                    $this->return['success'] = false;
                    $this->return['code'] = 4001;
                    $this->return['obj'] = ['errorMsg'=>$this->smsConfig['errorMap'][$res['error']]];
                }
                //更新用户积分
                if(!$isUpdate){
                    updateScore("USER_REGISTER");
                }
            }
            else
            {
                //数据库更改或者插入失败
                $this->return['success'] = false;
                $this->return['code'] = 4011;
            }

         return $this->return;
    }


    /*
    **登陆验证接口
    **params  $request 请求对象
    **return  返回响应数据 
    */
    public function verify(Request $request){
        $phoneNumber = $request->put('phoneNumber');
        $verifyCode = $request->put('verifyCode');
        if( !$phoneNumber || !$verifyCode)
        {
            $this->return['success'] = false;
            $this->return['code'] = 4002;
            $this->return['obj'] = ['errorMsg'=>"验证未通过"];
            return $this->return;

        }
        $user = new Doguser();
        $userData = $user->where(['phoneNumber'=>$phoneNumber,'verifycode'=>$verifyCode])
                         ->find(); 
        if(!$userData)
        {
            //如果数据库没有，说明验证不成功
            $this->return['success'] = false;
            $this->return['code'] = 4003;
            $this->return['obj'] = ['errorMsg'=>"验证未通过"];
        }
        else
        {
            $this->return['obj'] = [
                'accessToken'=>$userData['accessToken'],
                'avatar'=>$userData['avatar'],
                'nickname'=>$userData['nickname'],
                'id'=>$userData['id']
            ];
        }
        
        return $this->return;
    }

    /*
    **用户资料更新
    **params  $request 请求对象
    **return  返回响应数据 
    */
    public function update(Request $request){
      //检测accessToken是否有效
      hasToken();
      $user = new Doguser();
      $userData = session("user");
      $fields = ['age','avatar','breed','nickname'];
      $data = [];
      foreach($fields as $field)
      {
        if($request->put($field))
        {
            $data[$field] = $request->put($field);
        }
      }
      //sex=0为男
      if($request->put("sex"))
      {
        $data['sex'] = 1;
      }
      else if(0 == $request->put("sex"))
      {
        $data['sex'] = 0;
      }
      if(empty($data))
      {
        $this->return['obj'] = ['tips'=>'修改项为空','isempty'=>true];
        return $this->return;
      }
      $data['id'] = $userData['id'];
      $saveStatus = $user->isUpdate(true)
                         ->save($data);
                         
      if(!$saveStatus)
      {
          //如果数据库没有，说明验证不成功
          $this->return['success'] = false;
          $this->return['code'] = 4011;
          $this->return['obj'] = ['errorMsg'=>"数据库更改失败！"];
      }
      else
      {
           $userData = $user->find($saveStatus);
           $this->return['obj'] = [
              'accessToken'=>$userData['accessToken'],
              'avatar'=>$userData['avatar'],
              'nickname'=>$userData['nickname'],
              'age'=>$userData['age'],
              'breed'=>$userData['breed'],
              'sex'=>$userData['sex'],
              'id'=>$userData['id']
            ];
      }

      return $this->return;
    }


}