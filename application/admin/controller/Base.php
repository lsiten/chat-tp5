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

use app\admin\model\Node;
use think\Config;
use think\Controller;

class Base extends Controller
{
    protected $prefix = '';
    protected static $SYS; //系统级全局静态变量
    protected static $CMS; //CMS全局静态变量
    protected static $SHOP; //Shop变量全局设置
    public function _initialize()
    {
        if(empty(session('username'))){

            $this->redirect(url('login/index'));
        }

        $this->prefix = Config::get('database.prefix');
        //刷新系统全局配置
        self::$SYS['set'] = $_SESSION['SYS']['set'] = $this->checkSysSet();
        //刷新CMS全局配置
        self::$CMS['set'] = $_SESSION['CMS']['set'] = $this->checkSet();
        //刷新SHOP全局配置
        self::$SHOP['set'] = $_SESSION['SHOP']['set'] = $this->checkShopSet();
        //检测权限
        $control = request()->controller();
        $action = request()->action();

        //跳过登录系列的检测以及主页权限
        if(!in_array($control, ['login', 'index'])){

            if(!in_array($control . '/' . $action, session('action'))){
                $this->error('没有权限');
            }
        }

        //获取权限菜单
        $node = new Node();

        $this->assign([
            'username' => session('username'),
            'menu' => $node->getMenu(session('rule')),
            'rolename' => session('role')
        ]);

    }


    //返回系统全局配置
    public function checkSysSet()
    {
        $set = model('Set')->find();
        return $set ? $set : utf8error('系统还未配置！');
    }

     //返回CMS全局配置
    public function checkSet()
    {
        $set = model('Cms_set')->find()->toArray();
        return $set ? $set : utf8error('系统还未配置！');
    }

    // 返回Shop商城名称
    public function checkShopSet()
    {
        $set = model('Shop_set')->find()->toArray();
        $_SESSION['CMS']['set']['name'] = $set['name'];
        return $set ? $set : utf8error('系统还未配置！');
    }
}