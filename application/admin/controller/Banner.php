<?php
/**
 * 单页控制器
 */
namespace app\admin\controller;

use app\admin\model\Banner as BannerModel;
use think\Db;

class Banner extends Base
{

    public function index()
    {
       
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $type = $param['type'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }
            
            $banner = new BannerModel();
            if(1==$type)
            {
                $selectResult = $banner->getBannerByWhere($offset, $limit,$where);
                $return['total'] = $banner->getAllBanner($where);  //总数据
            }
            else
            {
                $selectResult = $banner->getAdvertisementByWhere($offset, $limit,$where);
                $return['total'] = $banner->getAllAdvertisement($where);  //总数据
            }
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => "javascript:edit(".$vo['id'].")",
                    '编辑列表' => url('banner/banlist', ['id' => $vo['id']]),
                    '删除' => "javascript:bannerDel('".$vo['id']."')"
                ];
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }

    /*
     * 添加内容
     */
    public function add(){
        if (request()->isPost()) {
            $banner = new BannerModel();
            //新增处理
            $params = input('post.');

            $has = $banner->checkName( $params['title'] );
            if ( !empty( $has ) ) {
                return json( ['code' => -5, 'data' => '', 'msg' => '标题重复'] );
            }
            $flag = $banner->insertData( $params );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '添加失败'] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '添加成功'] );
        }else{
            return $this->fetch();
        }
    }

    /**
     * 修改
     */
    public function edit(){
        $banner = new BannerModel();
        if (request()->isPost()) {
            $params = input('post.');

            $flag = $banner->editData( $params );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '修改失败'] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '修改成功'] );
        } else {
            $id = input('param.id');

            $this->assign([
                'item' => $banner->getOneData($id),
            ]);
            return $this->fetch();
        }
    }

    /**
     * 删除
     */
    public function dele($id){

        $id = input('param.id');

        $banner = new BannerModel();
        $flag = $banner->delData($id);

        return json(['code' => $flag['code'], 'data' => '', 'msg' => $flag['msg']]);
    }

    /**
     * Banner 已添加图片列表
     * @return 
     */
    public function banlist($id)
    {

        if(request()->isAjax()){
            
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['pid' => $id];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }
            
            $selectResult = Db::name('banner_detail')->where($where)->limit($offset,$limit)->select();
            $return['total'] = Db::name('banner_detail')->where($where)->count();  //总数据
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => "javascript:edit(".$vo['id'].")",
                    '删除' => "javascript:bannerDel('".$vo['id']."')"
                ];
                $selectResult[$key]['img'] = "<img src='".$vo['img']."' width='50px' height='50px'>";
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        $cate = Db::name('banner')->field('title,type,id')->find($id);
        $this->assign([
            'cate' => $cate
            ]);
        return $this->fetch();
    }

    /**
     * 添加banner内容
     */
    public function addDetail($id = 0){


        if (request()->isPost()) {
            //新增处理
            $params = input('post.');

            if ( empty($params['pid']) ){
                return json( ['code' => -1, 'data' => '', 'msg' => '所属banner不能为，请刷新重试！'] );
            }
            $this->_getUpFile( $params );  //处理上传图片

            $flag = Db::name('banner_detail')->insert($params);

            if(!$flag){
                return json( ['code' => -6, 'data' => '', 'msg' => '添加失败'] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '添加成功'] );
        }else{
            $this->assign('pid',$id);
            return $this->fetch();
        }
    }

    /**
     * 编辑大图
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    public function editDetail(){
        if (request()->isPost()) {
            $params = input('post.');
            $this->_getUpFile( $params );  //处理上传图片
            $flag = Db::name('banner_detail')->where(['id' => $params['id']])->update($params);
            if ($flag) {
                return json( ['code' => 1, 'data' => '', 'msg' => '修改成功'] );
            }else{
                return json( ['code' => 0, 'data' => '', 'msg' => '修改失败'] );
            }
        } else {
            $id = input('param.id/d',0);
            $item = Db::name('banner_detail')->find($id);
            $this->assign('item',$item);
            return $this->fetch();
        }
    }

    public function deleDetail($id){
        $flag = Db::name('banner_detail')->delete($id);
        if ($flag) {
            return json(['code' => 1, 'data' => '', 'msg' => "删除成功！"]);
        }else{
            return json(['code' => 0, 'data' => '', 'msg' => "删除失败！"]);   
        }
        
    }


     /**
     * 上传图片方法
     * @param $param
     */
    private function _getUpFile(&$param)
    {
        // 获取表单上传文件
        $file = request()->file('img');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if( !is_null( $file ) ){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $param['img'] =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['img'] );
        }

    }
}
