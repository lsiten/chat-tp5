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
        //uploadToCloudinary($src,$video["key"]);
        return $this->return;

    }
    //视频上传到cloudinary后信息保存
    public function saveCloudinaryInfo(Request $request){
        hasToken();
        $qiniu_key = $request->put("qiniu_key");
        $src = $request->put("src");
        //将视频上传到cloudinary
        $video = uploadToCloudinary($src,$qiniu_key);
        $this->return["obj"] = $video?["video"=>$video]:["message"=>"no data"];
        return $this->return;
    }

    //合并视频音频
    public function MergeVideo(Request $request){
        hasToken();
        $audio = json_decode($request->put("audio"),true);
        print_r($audio);
        $audio_public_id = str_replace("/",":",$audio["public_id"]);
        $videoName = str_replace("/","_",$audio["video_public_id"]).".mp4";
        //合并视频音频
        $videoUrl = "http://res.cloudinary.com/lsiten/video/upload/e_volume:-100/e_volume:400,l_video:".$audio_public_id."/".$audio["video_public_id"].".mp4";
        $thumbName = str_replace("/","_",$audio["video_public_id"]).".jpg";
        $thumbUrl = "http://res.cloudinary.com/lsiten/video/upload/".$audio["video_public_id"].".jpg";
        //同步到七牛
        $videoinfo = saveToQiniu($videoUrl,$videoName);
        $thumbInfo = saveToQiniu($thumbUrl,$thumbName);
        if($videoinfo["status"] || $thumbInfo["status"])
        {
            $this->return["code"] = 4030;
            $this->return["success"] = false;
            $this->return["obj"] = ["errorMsg"=>"视频同步出错，请重新录音"];
            return $this->return;
        }
        else
        {
            $videoModel = new VideoModel();
            $data = [
                "audio_public_id"=>$audio["public_id"],
                "qiniu_final_key"=>$videoinfo["message"],
                "qiniu_final_poster"=>$thumbInfo["message"]
            ];
            $where = ['qiniu_key'=>$audio["video_public_id"]];
            $videoModel->allowField(true)->save($data,$where);
            $this->return["obj"] = [
                                    "video_key"=>$videoinfo["message"],
                                    "poster_key"=>$videoinfo["message"]
                                ];
            return $this->return;
        }
    }
}