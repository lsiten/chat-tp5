<?php
/**
 *产品模型
 */

namespace app\admin\controller;

use think\Config;
use think\Controller;
use think\Db;
use think\Request;

class Product extends Base
{
    /*
     * 产品表
     */
    private static $_table = 'product';

    public function __construct()
    {
        parent::__construct();
        //分类
        $catgeroy = Db::name('category')->field('id,pid,name')->where('modelid', 3)->select();
        $this->assign('category', create_tree($catgeroy));
    }

    /**
     * 显示资源列表
     *param int $id cid
     * @return \think\Response
     */
    public function index()
    {
        $id = input('param.id/d',0);
        if(request()->isAjax()){
            
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['p.title|c.name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            if (isset($param['startTime']) && !empty($param['startTime'])) {
                $where['p.publishtime'] = ['> time', $param['startTime']];
            }
            if (isset($param['endTime']) && !empty($param['endTime'])) {
                $where['p.publishtime'] = ['< time', $param['endTime']];
            }
            if (isset($param['cat_id']) && !empty($param['cat_id'])) {
                $where['cid'] =  $param['cat_id'];
            }
            $selectResult = Db::field('p.id,p.title,p.publishtime,p.cid,p.click,p.flag,c.name')
                ->table($this->prefix.'product p,'.$this->prefix.'category c')
                ->where('p.cid = c.id')
                ->where('p.status',0)
                ->order('p.flag DESC,p.publishtime DESC');
            $selectResult = $selectResult->where($where)->limit($offset,$limit)->select();
            $return['total'] =  Db::field('p.id,p.title,p.publishtime,p.cid,p.click,p.flag,c.name')
                                ->table($this->prefix.'product p,'.$this->prefix.'category c')
                                ->where('p.cid = c.id')
                                ->where('p.status',0)
                                ->order('p.flag DESC,p.publishtime DESC')
                                ->where($where)->count();  //总数据
            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['title'] = "<a href='".url('index/show/index',['cid' => $vo['cid'],'id' => $vo['id']])."' target='_blank'>".$vo['title'];
                if($vo['flag']==1)
                {
                    $operate = [
                        '编辑'=> url('product/edit',['id' => $vo['id']]),
                        '取消置顶'=> "javascript:topit('".$vo['id']."',0)",
                        '删除' => "javascript:del('".$vo['id']."')"
                    ];
                    $selectResult[$key]['title'].="<button type='button' class='btn btn-danger btn-sm'>置顶</button></a>";
                }
                else{
                    $selectResult[$key]['title'].="</a>";
                    $operate = [
                        '编辑'=> url('product/edit',['id' => $vo['id']]),
                        '置顶'=> "javascript:topit('".$vo['id']."',1)",
                        '删除' => "javascript:del('".$vo['id']."')"
                    ];
                }
                
                $selectResult[$key]['name'] =  "<a href='".url('product/index',['id' => $vo['cid']])."' target='_blank'>".$vo['name']."</a>";
                $selectResult[$key]['publishtime'] =  date('Y年m月d日 H:i:s', $vo['publishtime']);
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        $this->assign("id", $id);
        return $this->fetch();
    }

    /**
     * 添加产品
     *
     */
    public function add()
    {
        //显示页面
        if (request()->isGet()) {
            $cid = input('param.cid/d',0);
            $this->assign("cid", $cid);
            return $this->fetch();
        } elseif (request()->isPost()) {
            $params = input('post.');
            $this->_getUpFile($params);
            if(!$params['cid']){
                return json( ['code' => -5, 'data' => '', 'msg' => '请先选择分类'] );
            }
            $params['publishtime'] = strtotime($params['publishtime']);
            //增加flag属性 判断是哪个编辑器添加的内容
            $flag = get_system_value('site_editor');
            $params['flag'] = $flag == 'markdown'?9:8;
           //新增
            unset($params['id']);
            //描述为空 则截取内容填补
            if(!$params['description']){
                $content = strip_tags($params['content']);
                $params['description'] = mb_substr($content,0,180,'utf-8');
            }
            $flag = Db::name(self::$_table)->insert($params);
            if ($flag) {
                return json( ['code' => 1, 'data' => '', 'msg' => '添加成功'] );
            } else {
                return json( ['code' => 0, 'data' => '', 'msg' => '添加失败'] );
            }
        }
    }

    /*
     * 更新文章信息
     *
     * $id 资源id
     */
    public function edit($id = 0) {
        if (request()->isPost()) {
            $params = input('post.');
            $this->_getUpFile($params);
            if(!$params['cid']){
                return json( ['code' => -5, 'data' => '', 'msg' => '请先选择分类'] );
            }
            $params['publishtime'] = strtotime($params['publishtime']);
            $flag = get_system_value('site_editor');
            $params['flag'] = $flag == 'markdown'?9:8;
            //更新
            $id = $params['id'];
            unset($params['id']);
            $params['updatetime'] = strtotime("now");
            $flag = Db::name(self::$_table)->where('id', $id)->update($params);

            if ($flag) {
                return json( ['code' => 1, 'data' => '', 'msg' => '更新成功'] );
            } else {
                return json( ['code' => 0, 'data' => '', 'msg' => '更新失败，请稍后重试'] );
            }
        } else {
            $data = Db::name(self::$_table)->where('id',$id)->find();
            $this->assign('item',$data);
            return $this->fetch();
        }
    }

    /*
     * 置顶文章
     *
     * $id 资源id
     */
    public function topit() {
        $id = input('param.id');
        $flag = input('param.flag');
        $res = Db::name(self::$_table)->where('id',$id)->update(['flag' => $flag]);
        if($res){
            return json( ['code' => 1, 'data' => '', 'msg' => '操作成功'] );
        }else{
            return json( ['code' => 0, 'data' => '', 'msg' => '操作失败'] );
        }
    }

    /*
     * 删除资源
     * @param id int 资源id
     */
    public function dele() {
        if(input('?param.checkbox')){
            $ids = input('param.checkbox/a');
        }else{
            $ids = input('param.id/d',0);
        }
        //逻辑删除
        $flag = Db::name(self::$_table)->where('id','in',$ids)->update(['status' => 1]);

        if($flag){
            return json( ['code' => 1, 'data' => '', 'msg' => '删除成功'] );
        }else{
            return json( ['code' => 0, 'data' => '', 'msg' => '删除失败'] );
        }
    }

     /**
     * 上传图片方法
     * @param $param
     */
    private function _getUpFile(&$param)
    {
        // 获取表单上传文件
        $file = request()->file('litpic');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if( !is_null( $file ) ){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $param['pictureurls'] = $param['litpic'] = '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['litpic'] );
        }

    }
}
