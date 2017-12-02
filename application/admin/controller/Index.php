<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Db;
class Index extends Base
{
    public function index()
    {
        return $this->fetch('/index');
    }

    /**
     * 后台默认首页
     * @return mixed
     */
    public function indexPage()
    {
        return $this->fetch('index');
    }
    /**
     * 系统设置
     */
    public function system(){
        if (!request()->isAjax()) {
            //获取系统设置项
            $list = Db::name('system')->select();
            $slist = [];
            foreach ($list as $key => $item){
                list($pk,$ck) = explode('_',$item['name']);
                $slist[$pk][$ck] = ['name' => $item['name'],'title' => $item['title'],'tvalue' => $item['tvalue'],'value' => $item['value'],'remark' => $item['remark']];
            }
            $this->assign('slist',$slist);
            return $this->fetch();
        } else {
            //插入、更新操作
            try {
                $params = input('post.');
                foreach ($params as $name => $value) {
                    $flag = Db::name('system')->where('name',$name)->update(['value' => $value]);
                }
            }catch (Exception $e) {
                return json(['code' => 0, 'msg' => '更新操作异常，请稍后重试', 'data' => '']);
            }
            return json(['code' => 1, 'msg' => '更新成功', 'data' => '']);
        }
    }
}
