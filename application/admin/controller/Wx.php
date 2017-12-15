<?php
// +----------------------------------------------------------------------
// | 用户后台基础类--CMS分组魔法关键词类
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\admin\model\Keyword;
use think\Request;

class Wx extends Base
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }

    //CMS后台魔法关键词引导页
    public function index()
    {
        //设置面包导航，主加载器请配置
        return $this->fetch();
    }

    //CMS后台微信设置
    public function set()
    {
        $m = db('Set');
        //处理POST提交
        if (Request::instance()->isPost()) {
            $data = input('param.');
            $this->_getUpFile( $data );  //处理上传图片
            $old = $m->find();
            if ($old) {
                $re = $m->where('id', $old['id'])->update($data);
                if (FALSE !== $re) {
                    $info['code'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['code'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $info['code'] = 0;
                $info['msg'] = '设置失败！系统配置表不存在！';
            }
            return json(['code' => $info['code'], 'data' => '', 'msg' => $info['msg']]);
        }
        $cache = $m->find();
        $this->assign('cache', $cache);
        return $this->fetch();
    }


    //CMS后台关键词分组
    public function keyword()
    {

        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where['status'] = 0;
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['name|keyword'] = ['like', '%' . $param['searchText'] . '%'];
            }
            //绑定搜索条件与分页
            $keyword = db('wx_keyword');
            $return['total'] = $keyword->where($where)->count(); //总数据
            $selectResult = $keyword->where($where)->limit($offset,$limit)->order('id DESC')->select();

            $types = config('wx_keyword_type');

            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['type_text'] = $types[$vo['type']];
                if(3==$vo['type']){
                    $selectResult[$key]['type_imgs'] = '<a href="'.url('Admin/Wx/img/',array('kid'=>$vo['id'])).'" class="btn btn-azure btn-xs" data-loader="App-loader" data-loadername="多图文管理"><i class="fa fa-edit"></i> 管理</a>';                    
                }
                else{
                    $selectResult[$key]['type_imgs'] = "未启用";
                }

                $operate = [
                    '编辑' => "javascript:keywordedit('".$vo['id']."')",
                    '删除' => "javascript:keywordDel('".$vo['id']."')"
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }
    //CMS后台关键词添加
    public function keywordadd(){
        if (request()->isPost()) {
            $keyword = new Keyword();
            //新增处理
            $params = input('post.');

            $has = $keyword->checkName( $params['keyword'] );
            if ( !empty( $has ) ) {
                return json( ['code' => -5, 'data' => '', 'msg' => '关键字重复'] );
            }
            $this->_getUpKeywordFile($params);
            $flag = $keyword->insertData( $params );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '添加失败'] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '添加成功'] );
        }else{
            return $this->fetch();
        }
    }

    //CMS后台关键词修改
    public function keywordedit(){
        $keyword = new Keyword();
        if (request()->isPost()) {
            //新增处理
            $params = input('post.');

            // $has = $keyword->checkName( $params['keyword']);
            // if ( !empty( $has ) ) {
            //     return json( ['code' => -5, 'data' => '', 'msg' => '关键字重复'] );
            // }
            $this->_getUpKeywordFile($params);
            $flag = $keyword->editData( $params );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '修改失败'] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '修改成功'] );
        }else{
            $id = input('param.id');
            $this->assign([
                'item' => $keyword->getOneData($id),
            ]);
            return $this->fetch();
        }
    }

    public function keywordDel()
    {
        $id = input('param.id');

        $keyword = new Keyword();
        $flag = $keyword->delData($id);

        return json(['code' => $flag['code'], 'data' => '', 'msg' => $flag['msg']]);
    }

    //CMS后台关键词分组
    public function img()
    {
        $kid = input('kid') ? input('kid') : die('缺少KID参数！');
        //绑定keyword
        $keyword = db('Wx_keyword')->where('id=' . $kid)->find();
        $this->assign('keyword', $keyword);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '魔法关键词',
                'url' => U('Admin/Wx/keyword')
            ),
            '1' => array(
                'name' => '关键词图文列表',
                'url' => U('Admin/Wx/img', array('kid' => $kid))
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = db('Wx_keyword_img');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = input('name') ? input('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $map['kid'] = $kid;
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '关键词图文列表', 'App-search');
        $this->assign('cache', $cache);
        return $this->fetch();
    }

    //CMS后台关键词设置
    public function imgSet()
    {
        $kid = input('kid');
        $id = input('id');
        $m = db('Wx_keyword_img');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '魔法关键词',
                'url' => U('Admin/Wx/keyword')
            ),
            '1' => array(
                'name' => '关键词图文列表',
                'url' => U('Admin/Wx/img', array('kid' => $kid))
            ),
            '2' => array(
                'name' => '关键词图文设置',
                'url' => $id ? U('Admin/Wx/imgSet', array('id' => $id)) : U('Admin/Wx/imgSet')
            )
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (Request::instance()->isPost()) {
            //die('aa');
            $data = input('post.');
            if ($id) {
                $re = $m->save($data);
                if (FALSE !== $re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $re = $m->add($data);
                if ($re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            }
            $this->ajaxReturn($info);
        }
        //绑定keyword
        $keyword = db('Wx_keyword')->where('id=' . $kid)->find();
        $this->assign('keyword', $keyword);
        //处理编辑界面
        if ($id) {
            $cache = $m->where('id=' . $id)->find();
            $this->assign('cache', $cache);
        }
        return $this->fetch();
    }

    public function imgDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = db('Wx_keyword_img');
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = 'ID不能为空!';
            $this->ajaxReturn($info);
        }
        $re = $m->delete($id);
        if ($re) {
            $info['status'] = 1;
            $info['msg'] = '删除成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '删除失败!';
        }
        $this->ajaxReturn($info);
    }

    // 微信端推广二维码背景设置
    public function qrcodeBgSet()
    {
        //设置面包导航，主加载器请配置
        if (Request::instance()->isPost()) {

             // 获取表单上传文件
                $file = request()->file('qrcode');
        
                // 移动到框架应用根目录/public/uploads/ 目录下
                if( !is_null( $file ) ){
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        // 成功上传后 获取上传信息
                        $qrcode =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
                        db('autoset')->update(array('id' => 1, 'qrcode_background' => $qrcode));
                        return json(['code' => 1, 'data' => '', 'msg' => '二维码背景更改成功！']);
                    }else{
                        // 上传失败获取错误信息
                        return json(['code' => -1, 'data' => '', 'msg' => $file->getError()]);                    
                    }
                }else{
                    return json(['code' => -1, 'data' => '', 'msg' => '二维码背景为空！']);
                }

        }
        $autoset = db('autoset')->find();
        if (!$autoset) {
            echo "系统未配置";
        }
        $this->assign('img', $autoset['qrcode_background']);
        return $this->fetch();
    }




    // 微信端推广二维码背景设置
    public function qrcodeBgEmpSet()
    {

         //设置面包导航，主加载器请配置
        if (Request::instance()->isPost()) {

             // 获取表单上传文件
                $file = request()->file('qrcode');
        
                // 移动到框架应用根目录/public/uploads/ 目录下
                if( !is_null( $file ) ){
                    $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
                    if($info){
                        // 成功上传后 获取上传信息
                        $qrcode =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
                        db('autoset')->update(array('id' => 1, 'qrcode_emp_background' => $qrcode));
                        return json(['code' => 1, 'data' => '', 'msg' => '二维码背景更改成功！']);
                    }else{
                        // 上传失败获取错误信息
                        return json(['code' => -1, 'data' => '', 'msg' => $file->getError()]);                    
                    }
                }else{
                    return json(['code' => -1, 'data' => '', 'msg' => '二维码背景为空！']);
                }

        }
        $autoset = db('autoset')->find();
        if (!$autoset) {
            echo "系统未配置";
        }
        $this->assign('img', $autoset['qrcode_emp_background']);
        return $this->fetch(); 
    }

    // Admin后台微信自定义菜单设置
    public function menu()
    {

        // 生成Wechat对象
        $config['appid'] = self::$SYS['set']['wxappid'];
        $config['appsecret'] = self::$SYS['set']['wxappsecret'];
        $config['token'] = self::$SYS['set']['wxtoken'];
        $wechat = new \wx\Wechat($config);
        // 提交包括两种，一是删除，一是上传，两种执行完毕后可以继续运行
        if (Request::instance()->isPost()) {
            if ($_POST['do'] == 'remove') {
                // 删除菜单
                $re = $wechat->deleteMenu();
                if ($re) {
                    // 删除成功
                    echo "<script>parent.replaceok('删除成功！');</script>";
                } else {
                    // 删除失败
                    echo "<script>parent.replaceFuck('删除失败！');</script>";
                }
            } else {
                $menu = urldecode($_POST['do']);
                $menu = json_decode($menu, true);
                $button['button'] = $menu;
                $re = $wechat->createMenu($button);
                if ($re) {
                    echo "<script>parent.replaceok('更新菜单成功！');</script>";
                } else {
                    echo "<script>parent.replaceFuck('更新菜单失败！');</script>";
                }
            }
            exit();
        }
        // 获取Menu
        $menu = $wechat->getMenu();
        $this->assign('menu', $menu['menu']);
        return $this->fetch();
    }

    // 自定义客服消息
    public function customerSet()
    {
        if (Request::instance()->isPost()) {
            $mcustomer = db('wx_customer');
            // 获取那个ShortID
            $id = input('id');
            $value = input('value');
            $customer = $mcustomer->where(array('id' => $id))->find();
            if (!$customer) {
                $info['code'] = 0;
                $info['msg'] = '数据缺失';
                $this->ajaxReturn($info);
            }
            // 获取templateID
            $customer['value'] = $value;
            $re = $mcustomer->update($customer);
            if ($re) {
                $info['code'] = 1;
                $info['msg'] = '更新成功';
                // 更新数据库
            } else {
                $info['code'] = 0;
                $info['msg'] = '更新失败';
            }
            return json( ['code' => $info['code'], 'data' => "", 'msg' => $info['msg']] );
        }
    }

    // 自定义客服接口
    public function customer()
    {
        $cache = db('wx_customer')->select();
        $this->assign('cache', $cache);
        return $this->fetch();
    }

    // 自定义模版消息
    public function templateRemoteSet()
    {
        if (Request::instance()->isPost()) {
            $mtemplate = db('wx_template');
            // 获取那个ShortID
            $shortid = input('shortid');
            $template = $mtemplate->where(array('templateidshort' => $shortid))->find();
            if (!$template) {
                $info['shortid'] = $shortid;
                $info['code'] = 0;
                $info['msg'] = '数据缺失';
                return json( ['code' => $info['code'],'shortid'=> $info['shortid'], 'data' => "", 'msg' => $info['msg']] );
            }
            // 获取templateID
            $options['appid'] = self::$SYS['set']['wxappid'];
            $options['appsecret'] = self::$SYS['set']['wxappsecret'];
            $wx = new \wx\Wechat($options);
            $re = $wx->addTemplateMessage($shortid);
            if ($re) {
                $template['templateid'] = $re;
                $mtemplate->update($template);
                $info['shortid'] = $shortid;
                $info['templateid'] = $re;
                $info['code'] = 1;
                $info['msg'] = '更新成功，不需要再次更新';
                // 更新数据库
            } else {
                $info['shortid'] = $shortid;
                $info['templateid'] = 0;
                $info['code'] = 0;
                $info['msg'] = '更新失败';
            }
            return json( ['code' => $info['code'],'templateid'=> $info['templateid'],'shortid'=> $info['shortid'], 'data' => "", 'msg' => $info['msg']] );
        }
    }

    // 自定义模版消息
    public function templateSet()
    {
        if (Request::instance()->isPost()) {
            $mtemplate = db('wx_template');
            // 获取那个ShortID
            $shortid = input('shortid');
            $templateid = input('templateid');
            $template = $mtemplate->where(array('templateidshort' => $shortid))->find();
            if (!$template) {
                $info['shortid'] = $shortid;
                $info['code'] = 0;
                $info['msg'] = '数据缺失';
                 return json( ['code' => $info['code'], 'data' => "", 'msg' => $info['msg']] );
            }
            // 获取templateID
            $template['templateid'] = $templateid;
            $re = $mtemplate->update($template);
            if ($re) {
                $info['code'] = 1;
                $info['msg'] = '更新成功';
                // 更新数据库
            } else {
                $info['code'] = 0;
                $info['msg'] = '更新失败';
            }
            return json( ['code' => $info['code'], 'data' => "", 'msg' => $info['msg']] );
        }
    }

    // 自定义模版消息
    public function template()
    {
        $cache = db('wx_template')->select();
        $this->assign('cache', $cache);
        return $this->fetch();
    }

     /**
     * 上传图片方法
     * @param $param
     */
    private function _getUpKeywordFile(&$param)
    {
        // 获取表单上传文件
        $file = request()->file('pic');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if( !is_null( $file ) ){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $param['pic'] =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['pic'] );
        }

    }


     /**
     * 上传图片方法
     * @param $param
     */
    private function _getUpFile(&$param)
    {
        // 获取表单上传文件
        $file = request()->file('wxpicture');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if( !is_null( $file ) ){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $param['wxpicture'] =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['wxpicture'] );
        }

    }

}