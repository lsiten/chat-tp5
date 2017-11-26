<?php
namespace app\api\controller;
use app\api\controller\Base;
use think\Request;
use app\api\model\Video as VideoModel;
class Video extends Base{
    public function _initialize(){
        parent::_initialize();
        $this->return = config("return");
    }
    public function index(){
        $cloudinaryConfig = config("cloudinary");
        \Cloudinary::config(array( 
            "cloud_name" => $cloudinaryConfig['cloud_name'], 
            "api_key" => $cloudinaryConfig['api_key'], 
            "api_secret" => $cloudinaryConfig['api_secret'],
          ));
    }

    //视频上传到七牛的信息保存
    public function saveInfo(Request $request){
        hasToken();
        $video = json_decode($request->put("video"),true);
        $src = $request->put("videoSrc");
        $user = session("user");
        if(!$video && !$video["key"])
        {
            $this->return["code"] = 4020;
            $this->return["success"] = false;
            $this->return["obj"] = ["errorMsg"=>"参数不全，上传错误！"];
            return $this->return;
        }
        $videoModel = new VideoModel();
        $videoData = $videoModel->where(["qiniu_key"=>$video["key"]])->find();
        if(!$videoData)
        {
            $videoData = $videoModel->data([
                "user"=>$user["id"],
                "qiniu_key"=>$video["key"],
                "persistentId"=>$video["persistentId"],
                "src"=>$src
            ]);
            $videoModel->save();
        }

        $this->return["obj"] = ["video"=>$videoData];

        //将视频上传到cloudinary
        uploadToCloudinary($src,$video["key"]);
        return $this->return;

    }
    //视频上传到cloudinary后信息保存
    public function saveCloudinaryInfo(Request $request){
        hasToken();
        $qiniu_key = $request->put("qiniu_key");
        //将视频上传到cloudinary
        $video = uploadToCloudinary($src,$qiniu_key);
        $this->return["obj"] = $video?["video"=>$video]:["message"=>"no data"];
        return $this->return;
    }
}