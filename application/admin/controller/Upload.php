<?php
// 本类由系统自动生成，仅供测试用途
namespace app\admin\controller;

class Upload extends Base
{
    public function index()
    {
        $this->display();
    }

    public function indeximg()
    {
        //查找带回字段
        $fbid = input('param.fbid');
        $isall = input('param.isall');
        $this->assign('fbid', $fbid);
        $this->assign('isall', $isall);
        $page = '1,8';
        $m = model('Uploadimg');
        $cache = $m->page($page)->order('id desc')->select();
        $this->assign('cache', $cache);
        return $this->fetch();
    }

    public function doupimg()
    {

        $config = array(
            'mimes' => array(), //允许上传的文件MiMe类型
            'maxSize' => 0, //上传的文件大小限制 (0-不做限制)
            'exts' => array('jpg', 'gif', 'png', 'jpeg'), //允许上传的文件后缀
            'autoSub' => true, //自动子目录保存文件
            'subName' => array('date', 'Y-m-d'), //子目录创建方式，[0]-函数名，[1]-参数，多个参数使用数组
            'rootPath' => './Upload/', //保存根路径
            'savePath' => 'img/', //保存路径
            'saveName' => array('uniqid', ''), //上传文件命名规则，[0]-函数名，[1]-参数，多个参数使用数组
            'saveExt' => '', //文件保存后缀，空则使用原后缀
            'replace' => false, //存在同名是否覆盖
            'hash' => true, //是否生成hash编码
            'callback' => false, //检测文件是否存在回调，如果存在返回文件信息数组
            'driver' => '', // 文件上传驱动
            'driverConfig' => array(), // 上传驱动配置
        );
        //var_dump($_FILES);
        $list = request()->file('appfile');
       
        if ($list) {
            //dump($list);
            
            $pic = db('upload_img');
            $count = 0;
            $arr = array();
            foreach ($list as $k => $v) {
                // 移动到框架应用根目录/public/uploads/ 目录下
                $info = $v->move(ROOT_PATH . 'public' . DS . 'uploads');
                if($info)
                {
                    $arr['name'] = $info->getInfo("name");
                    $arr['ext'] = $info->getExtension();
                    $arr['type'] = 'img';
                    $arr['savename'] = $info->getFilename();
                    $arr['savepath'] =  '/' . date('Ymd') . '/';
                    $re = $pic->insert($arr);
                    if ($re) {
                        $count += 1;
                    }
                }
                
            }

            if ($count) {
                $backstr = "'" . $count . "张图片上传成功！'" . ',' . "true";
                echo "<script>parent.doupimgcallback(" . $backstr . ")</script>";
            } else {
                echo "<script>parent.doupimgcallback('图片保存时失败！',false)</script>";
            };

        } else {
            echo "<script>parent.doupimgcallback('" . $up->getError() . "',false)</script>";
        };

    }

    public function delimgs()
    {
        if (request()->isAjax()) {
            $m = db('upload_img');
            $list = $m->delete(input("param.ids"));
            if ($list == true) {
                $data['status'] = 1;
                $data['msg'] = '成功删除' . $list . '张图片！';
            } else {
                $data['status'] = 0;
                $data['msg'] = '删除失败，请重试或联系管理员！';
            }
            return json($data);
        } else {
            $this->error('微专家提醒您：禁止外部访问！');
        }
    }


    public function getmoreimg()
    {
        $page = input('param.p') . ',8';
        $m = model('Uploadimg');
        $cache = $m->page($page)->order('id desc')->select();
        if ($cache) {
            $this->assign('cache', $cache);
            return $this->fetch();
        } else {
            return json([]);
        }

    }

}