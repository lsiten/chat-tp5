<?php
/**
 * 单页控制器
 */
namespace app\admin\controller;

use think\Db;

class Flink extends Base
{
	private static $_table = 'flink';

	/**
	 * 友情链接管理界面
	 * @return 
	 */
    public function index()
    {
        if(request()->isAjax()){
            
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['status' => 0,'type' => 1];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }
            
            $selectResult = Db::name(self::$_table)->where($where)->limit($offset,$limit)->order('id DESC')->select();
            $return['total'] = Db::name(self::$_table)->where($where)->count();  //总数据
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => "javascript:edit(".$vo['id'].")",
                    '删除' => "javascript:del('".$vo['id']."')"
                ];
                if($vo['logo'])
                    $selectResult[$key]['logo'] = "<img src='".$vo['logo']."' width='80px' height='40px'>";
                $selectResult[$key]['create_time'] =  date('Y年m月d日 H:i:s', $vo['create_time']);
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }

    /**
     * 公告管理界面
     * @return 
     */
	public function annindex()
    {
        if(request()->isAjax()){
            
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['status' => 0,'type' => 2];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title'] = ['like', '%' . $param['searchText'] . '%'];
            }
            
            $selectResult = Db::name(self::$_table)->where($where)->limit($offset,$limit)->order('id DESC')->select();
            $return['total'] = Db::name(self::$_table)->where($where)->count();  //总数据
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '编辑' => "javascript:edit(".$vo['id'].")",
                    '删除' => "javascript:del('".$vo['id']."')"
                ];
                if($vo['logo'])
                    $selectResult[$key]['logo'] = "<img src='".$vo['logo']."' width='80px' height='40px'>";
                $selectResult[$key]['create_time'] =  date('Y年m月d日 H:i:s', $vo['create_time']);
                $selectResult[$key]['description'] =  mb_substr($vo['description'],0,30);
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }

     /**
     * 添加友链/公告
     *
     */
    public function add()
    {
        //显示页面
        if (request()->isGet()) {
        	if (input('?type') && input('param.type') == 2){
        		return $this->fetch('annadd');
        	}
            return $this->fetch();
        } elseif (request()->isPost()) {
            $params = input('post.');
            if ($params['type'] == 2){
				if ($params['title'] == '') {
                    return json( ['code' => -1, 'data' => '', 'msg' => '请填写公告标题'] );
	            }
            }else{
            	if ($params['title'] == '') {
                    return json( ['code' => -1, 'data' => '', 'msg' => '请填写网站名称'] );
	            }

	            if ($params['url'] == '') {
                    return json( ['code' => -1, 'data' => '', 'msg' => '请填写网站url'] );
	            }
            }
            
            $this->_getUpFile($params);
            //新增
            unset($params['id']);
            $params['create_time'] = strtotime("now");
            
            $flag = Db::name(self::$_table)->insert($params);
            if ($flag) {
                return json( ['code' => 1, 'data' => '', 'msg' => '添加成功'] );
            } else {
                return json( ['code' => 1, 'data' => '', 'msg' => '添加失败'] );
            }
        }
    }

    /*
     * 更新友链信息
     *
     * $id 资源id
     */
    public function edit() {
        //显示页面
        if (request()->isGet()) {
            $id = input('param.id/d',0);
            $data = Db::name(self::$_table)->where(['id' => $id])->find();
            $this->assign('item',$data);
            if (input('?type') && input('param.type/d') == 2){
                return $this->fetch('annedit');
            }else{
                return $this->fetch();
            }
        } elseif (request()->isPost()) {
            $params = input('post.');
            if ($params['type'] == 2){
                if ($params['title'] == '') {
                    return json( ['code' => -1, 'data' => '', 'msg' => '请填写公告标题'] );                    
                }
            }else{
                if ($params['title'] == '') {
                    return json( ['code' => -1, 'data' => '', 'msg' => '请填写网站名称'] );                                        
                }

                if ($params['url'] == '') {
                    return json( ['code' => -1, 'data' => '', 'msg' => '请填写网站url'] );                                        
                }
            }
            
            $this->_getUpFile($params);
            
             //更新
            $id = $params['id'];
            unset($params['id']);
            // $url = $params['type'] == 1?url('flink/index'):url('flink/annindex');
            $flag = Db::name(self::$_table)->where(['id' => $id])->update($params);
            if ($flag !== false) {
                return json( ['code' => 1, 'data' => '', 'msg' => '更新成功'] );                                        
            } else {
                return json( ['code' => 1, 'data' => '', 'msg' => '更新失败，请稍后重试'] );                                                        
            }
        }
    	
        
    }

    /**
     * 删除友链、公告
     * @return [type] [description]
     */
    public function dele() {
        $id = input('param.id/d',0);
        //逻辑删除
        $flag = Db::name(self::$_table)->where(['id' => $id])->update(['status' => 1]);
        if ($flag) {
            return json( ['code' => 1, 'data' => '', 'msg' => '更新成功'] );                                                    
        } else {
            return json( ['code' => 1, 'data' => '', 'msg' => '删除失败'] );                                                    
        }
    }

     /**
     * 上传图片方法
     * @param $param
     */
    private function _getUpFile(&$param)
    {
        // 获取表单上传文件
        $file = request()->file('logo');

        // 移动到框架应用根目录/public/uploads/ 目录下
        if( !is_null( $file ) ){

            $info = $file->move(ROOT_PATH . 'public' . DS . 'uploads');
            if($info){
                // 成功上传后 获取上传信息
                $param['logo'] =  '/uploads' . '/' . date('Ymd') . '/' . $info->getFilename();
            }else{
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }else{
            unset( $param['logo'] );
        }

    }


}
