<?php
/**
 *产品模型
 */

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Main extends Base
{

    /**
     * 图片上传
     */
    public function upload()
    {
        if (!input('?param.act')) {
            $file = request()->file('pic_url');

            $info = $file->validate(['size'=> 1024*1024*2,'ext'=>['jpg', 'png', 'jpeg', 'gif', 'bmp']])->move(ROOT_PATH . 'public/uploads');
            
            if ($info) {
                // 成功上传后 获取上传信息
               
                $path = $info->getPath();
                $filename = $info->getFilename();
                $save_name = $info->getSaveName();
                if(__ROOT__){
                    $realpath =  __ROOT__.'/uploads/' . $save_name;
                }else{
                    $realpath =  '/uploads/' . $save_name;
                }
                return json(['code' => 1, 'path' => $realpath, 'save_name' => $save_name]);
            } else {
                // 上传失败获取错误信息
                return json(['code' => 0, 'error' => $file->getError()]);
            }
        } else {
            //删除图片
            $img_dir = input('param.path');
            $real_path = str_replace(__ROOT__,'',$img_dir);
            $path = str_replace(['/..\/','/../'],'/',ROOT_PATH.$real_path);  
            if (@unlink($path)) {
                return json(['code' => 1, 'msg' => "删除成功"]);
            } else {
                return json(['code' => 0, 'msg' => "删除失败"]);
            }
        }
    }

    /**
    **  富文本框图片上传
    **/

    public function uploadEditor(){
        $file = request()->file('editormd-image-file');
        $info = $file->validate(['size'=> 1024*1024*2,'ext'=>['jpg', 'png', 'jpeg', 'gif', 'bmp']])->move(ROOT_PATH . 'public/uploads');
        if ($info) {
            // 成功上传后 获取上传信息
            $path = $info->getPath();
            $filename = $info->getFilename();
            //$root = request()->domain();
            $save_name = $info->getSaveName();
            if(__ROOT__){
                $realpath =  __ROOT__.'/uploads/' . $save_name;
            }else{
                $realpath =  '/uploads/' . $save_name;
            }
            return json(['error' => 0, 'success' => 1, 'url' => $realpath]);
        } else {
            // 上传失败获取错误信息
            return json(['error' => 1, 'success' => 0, 'message' => $file->getError()]);
        }
    }

}
