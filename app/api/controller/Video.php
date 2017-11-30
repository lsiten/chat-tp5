<?php
namespace app\api\controller;
use app\api\controller\Base;
use think\Request;
use app\api\model\Video as VideoModel;
use app\api\model\Creation;
use app\api\model\Videolike;
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
        if(!$audio["public_id"] || !$audio["video_public_id"]|| !$audio["qiniu_key"])
        {
            $this->return["code"] = 4020;
            $this->return["success"] = false;
            $this->return["obj"] = ["errorMsg"=>"视频同步出错，请重新录音"];
            return $this->return;
        }
        $audio_public_id = str_replace("/",":",$audio["public_id"]);
        $videoName = str_replace("/","_",$audio["video_public_id"]).".mp4";
        //合并视频音频
        $videoUrl = "http://res.cloudinary.com/lsiten/video/upload/e_volume:-100/e_volume:600,l_video:".$audio_public_id."/".$audio["video_public_id"].".mp4";
        $thumbName = str_replace("/","_",$audio["video_public_id"]).".jpg";
        $thumbUrl = "http://res.cloudinary.com/lsiten/video/upload/".$audio["video_public_id"].".jpg";
        //同步到七牛
        $videoinfo = saveToQiniu($videoUrl,$videoName);
        $thumbInfo = saveToQiniu($thumbUrl,$thumbName);
        if(!$videoinfo["status"] || !$thumbInfo["status"])
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
            $where = ['qiniu_key'=>$audio["qiniu_key"]];
            $videoModel->where($where)->update($data);
            $this->return["obj"] = [
                                    "video_key"=>$videoinfo["message"],
                                    "poster_key"=>$thumbInfo["message"]
                                ];
            return $this->return;
        }
    }

    //创意发布
    public function creation(Request $request){
        hasToken();
        $qiniu_key = $request->put("qiniu_key");
        $title = $request->put("title");
        if(!$qiniu_key || !$title)
        {
            $this->return["code"] = 4020;
            $this->return["success"] = false;
            $this->return["obj"] = ["errorMsg"=>"创意创建出错，请稍后重新创建！"];
            return $this->return;
        }
        $video =  new VideoModel();
        $creationModel = new Creation();
        $videoData = $video->where(["qiniu_key"=>$qiniu_key])->find()->toArray();   
        if(!$videoData["audio_public_id"] || !$videoData["qiniu_final_key"] || !$videoData["qiniu_final_poster"])
        {
            $this->return["code"] = 4031;
            $this->return["success"] = false;
            $this->return["obj"] = ["errorMsg"=>"视频源出错，请重新录制，再上传！"];
            return $this->return;
        }
        //去重
       $creationData = $creationModel->where(["audio_public_id"=>$videoData["audio_public_id"],"video_qiniu_key"=>$videoData["qiniu_final_key"]])->find();
       if($creationData)
       {
            $this->return["code"] = 4032;
            $this->return["success"] = false;
            $this->return["obj"] = ["errorMsg"=>"视频发布重复！"];
            return $this->return; 
       } 
       $data = [
            "title"=>$title,
            "videoid"=>$videoData["id"],
            "user"=>$videoData["user"],
            "audio_public_id"=>$videoData["audio_public_id"],
            "video_qiniu_key"=>$videoData["qiniu_final_key"],
            "video_qiniu_thumb"=>$videoData["qiniu_final_poster"]
        ];
        $creationModel->data($data)->save();

        //更新视频发布状态
        $video->where('id',$videoData["id"])->update(["isPublish"=>1]);
        //更新用户视频发布数以及其分数
        updateScore("VIDEO_PUBLISH","videoNumberAdd",[1]);
        $this->return["obj"] = [
            "id"=>$creationModel->id,
            "videoid"=>$videoData["id"],
            "video_qiniu_key"=>$videoData["qiniu_final_key"]
        ];
        return $this->return;
    }

    //视频列表
    public function videoList(Request $request){
        $page = intval($request->get("page")) || 1;
        $count = 5;
        $offset = ($page-1)*$count;
        //用户信息
        $user = session("user");
        //七牛配置
        $qiniuConfig = config('qiniu');
        $VideolikeModel = new Videolike();
        $creationModel = new Creation();
        $total = $creationModel->count();
        $videoData =Creation::with('userdata,likecount')
                            ->limit($count)
                            ->page($page)
                            ->order('createAt', 'desc')
                            ->select();
        foreach($videoData as $videoitem)
        {
            $videolist["id"] = $videoitem->id;
            $videolist["thumb"] = $qiniuConfig["base"]."/".$videoitem->video_qiniu_thumb;
            $videolist["video"] = $qiniuConfig["base"]."/".$videoitem->video_qiniu_key;
            $videolist["title"] = $videoitem->title;
            $videolist["isLike"] = $VideolikeModel->where(["userid",$user["id"]])->count()?1:0;
            $videolist["author"]["avatar"] = $videoitem->userdata->avatar;
            $videolist["author"]["nickname"] = $videoitem->userdata->nickname;
            $this->return["data"][]= $videolist;
        }
        $this->return["obj"] = [
                        "total" => $total
                    ];
                    return $this->return;
        
    }
}