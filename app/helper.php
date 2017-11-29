<?php
require ROOT_PATH.'/vendor/qiniu/autoload.php';
use think\Request;
use app\api\model\Doguser;
use \Qiniu\Auth;
use \Qiniu\Storage\BucketManager;
/**
 * 生成用户唯一标识
 * @params $isNeed 是否需要用{}扩起来 
* @return string
*/
function uuid($isNeed=true) {
    $charid = md5(uniqid(mt_rand(), true));
    $hyphen = chr(45);// "-"
    if($isNeed)
    {
        $uuid = chr(123)// "{"
        .substr($charid, 0, 8).$hyphen
        .substr($charid, 8, 4).$hyphen
        .substr($charid,12, 4).$hyphen
        .substr($charid,16, 4).$hyphen
        .substr($charid,20,12)
        .chr(125);// "}"
    }
    else
    {
        $uuid = substr($charid, 0, 8).$hyphen
            .substr($charid, 8, 4).$hyphen
            .substr($charid,12, 4).$hyphen
            .substr($charid,16, 4).$hyphen
            .substr($charid,20,12);
    }
    return $uuid;
}

/*
**随机生成手机验证码
**@params $length Number 验证码长度
**@params $codeSet String 验证码字符集
**return string
*/
function speakeasy($length=4,$codeSet = "1234567890"){
    for ($i = 0; $i < $length; $i++) {
        $code[$i] = $codeSet[mt_rand(0, strlen($codeSet) - 1)];
    }
    return join($code);
}

/*
**curl发送短信验证码
**@params $code Number 验证码
**@params $phone Number 电话号码
**@return json curl返回值
*/
function sendMessageByLuosimao($code,$phone){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://sms-api.luosimao.com/v1/send.json");
    
    curl_setopt($ch, CURLOPT_HTTP_VERSION  , CURL_HTTP_VERSION_1_0 );
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 8);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    
    curl_setopt($ch, CURLOPT_HTTPAUTH , CURLAUTH_BASIC);
    curl_setopt($ch, CURLOPT_USERPWD  , 'api:key-5f1db12aa6cc3f4dd0b8065a80d7e9a7');
    
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, array('mobile' => $phone,'message' => '您的验证码为：'.$code.'【雷诗城】'));
    $res = curl_exec( $ch );
    curl_close( $ch );
    return $res;
}

/*
**检测是否有token
**return void
*/
function hasToken(){
    $request = Request::instance();
    //输出类型
    $type = config('default_return_type');
    switch($type)
    {
        case 'json':
         $response = json();
        break;
        case 'jsonp':
         $response = jsonp();
        break;
        case 'xml':
         $response = xml();
        break;
        default:
         $response = json();
        break;
    }
    
    $return = config("return");
    $accessToken = $request->param("accessToken");
    if (!$accessToken)
    {
        $accessToken = $request->put("accessToken");
    }
    if (!$accessToken)
    {
        $return["success"] = false;
        $return["code"] = 4000;
        $return["obj"] = ["errorMsg"=>"accessToken丢失！"];
        
        $response->data($return);
        $response->send();
        die();
    }

    $user = new Doguser();
    $userData = $user->where(['accessToken'=>$accessToken])
                     ->find(); 
    if(!$userData)
    {
        $return["success"] = false;
        $return["code"] = 4000;
        $return["obj"] = ["errorMsg"=>"accessToken无效！"];
        
        $response->data($return);
        $response->send();
        die();
    }
    else
    {
        //如果有用户将用户信息保存到session
        session("user",$userData);
    }

}

/*
**获取cloudinary的token（signature）
**@params $timestamp string 时间戳，前台传递的
**@params $type String【image|avatar|video|audio】 上传类型
**return string signature
*/
function getCloudinaryToken($timestamp,$type){
    $folder = "";
    $tags = "";
    // 生成上传Token
    $key = uuid(false);
    //匹配市那种类型，选择folder，tags
    switch($type)
    {
      case 'image':
        $folder = "appDog/image";
        $tags = "appDog,image";
      break;
      case 'avatar':
        $folder = "appDog/avatar";
        $tags = "appDog,avatar";
      break;
      case 'video':
        $folder = "appDog/video";
        $tags = "appDog,video";
      break;
      case 'audio':
        $folder = "appDog/audio";
        $tags = "appDog,audio";
      break;
    }
    $cloudinary = config('cloudinary');
    $signature = "folder=".$folder."&public_id=".$key."&tags=".$tags."&timestamp=".$timestamp.$cloudinary['api_secret'];
    $signature = sha1($signature);

    return [
        "signature"=>$signature,
        "folder"=>$folder,
        "tags"=>$tags,
        "key"=>$key
    ];
}

/*
**获取七牛的token
**return string token
*/
function getQiniuToken($type){
    $qiniuConfig = config('qiniu');
    $auth = new Auth($qiniuConfig['AK'], $qiniuConfig['SK']);
    // 生成上传Token
    $key = uuid(false);
    $token = "";
    switch($type)
    {
        case "avatar":
         $key .=".jpeg";
         $token = $auth->uploadToken($qiniuConfig['bucket'],$key);
        break;
        case "video":
         $key .=".mp4";
         $policy = [
             "persistentOps"=>"avthumb/mp4/an/1",
             "persistentNotifyUrl"=>"http://fake.com/qiniu/notify"
         ];
         $token = $auth->uploadToken($qiniuConfig['bucket'],$key,3600,$policy);         
        break;
        case "audio":
            
        break;
    }
    return ["token"=>$token,"key"=>$key];
}

/**
 * 上传到cloudinary
 * @params $url 上传视频的url
 * @params $public_id cloudinary的public_id
 * @return 
 */
function uploadToCloudinary($url,$public_id=0){
    $cloudinaryConfig = config("cloudinary");
    \Cloudinary::config(array(
        "cloud_name" => $cloudinaryConfig['cloud_name'], 
        "api_key" => $cloudinaryConfig['api_key'], 
        "api_secret" => $cloudinaryConfig['api_secret'],
      ));
      $optins =  array(
        "tags"=>array("app","video"),
        "folder"=>"appDog/video",
        "resource_type" => "video"
        // "async"=>true,
        // "callback"=>$cloudinaryConfig['videoCallback']
      );
      if($public_id)
            $optins["public_id"]=basename($public_id,".mp4");
        else
           return false;
      $result = \Cloudinary\Uploader::upload($url,$optins);
      $videoModel = new app\api\model\Video();
      $videoData = $videoModel->where('qiniu_key', $public_id)
                              ->update(["cloudinary"=>json_encode($result)]);
    return [
        "video_id"=>$videoData,
        "public_id"=>$result["public_id"]
    ];
}
/**
 * 同步到七牛
 * @params $url 上传视频的url
 * @params $name 七牛key
 * @return Array 资源获取状态和最终视频信息
 */
function saveToQiniu($url,$name){
    $qiniuConfig = config('qiniu');
    $auth = new Auth($qiniuConfig['AK'], $qiniuConfig['SK']);
    $bucketManager = new BucketManager($auth);
    list($ret, $err) = $bucketManager->fetch($url, $qiniuConfig['bucket'], $name);
    if ($err !== null) {
        return ["status"=>false,"message"=>$err['error']];
    } else {
        return ["status"=>true,"message"=>$ret['key']];
    }
}