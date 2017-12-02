<?php
/**
 * 多说评论系统专用类
 */
namespace app\admin\controller;

use think\Db;
use think\request;

class Comment extends Base
{

    public function index()
    {
        if(request()->isAjax()){
            
            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;
            $where = ['status' => 0,'rid'=>0];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['title|username|tel|tel|content'] = ['like', '%' . $param['searchText'] . '%'];
            }
            if (isset($param['startTime']) && !empty($param['startTime'])) {
                $where['create_time'] = ['> time', $param['startTime']];
            }
            if (isset($param['endTime']) && !empty($param['endTime'])) {
                $where['create_time'] = ['< time', $param['endTime']];
            }

            $selectResult = Db::name("comment")->where($where)->limit($offset,$limit)->order('id DESC')->select();
            $return['total'] = Db::name("comment")->where($where)->count();  //总数据
            foreach($selectResult as $key=>$vo){
                $operate = [
                    '回复'=> url('comment/add',['id' => $vo['id']]),
                    '删除' => "javascript:del('".$vo['id']."')"
                ];
                $selectResult[$key]['create_time'] =  date('Y年m月d日 H:i:s', strtotime($vo['create_time']));
                $selectResult[$key]['operate'] = showOperate($operate);
            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }



    /**
     * 添加/回复留言
     */
    public function add(){
        print_r(session("user"));
        if (request()->isPost()) {
            //新增处理
            $params = input('post.');
            $params['create_time'] = date('Y-m-d H:i');
            $flag = Db::name('comment')->insert($params);
            if ($flag) {
                return json( ['code' => 1, 'data' => '', 'msg' => '添加成功'] );
            } else {
                return json( ['code' => 1, 'data' => '', 'msg' => '添加失败'] );
            }
        }else{

            $id = input('param.id/d',0);
            $item = Db::name('comment')->field('id,username,title')->find($id);
            $this->assign('item',$item);
            return $this->fetch();
        }
    }

    /**
     * 删除友链、公告
     * @return [type] [description]
     */
    public function dele() {
        $id = input('param.id/d',0);
        //逻辑删除
        $flag = Db::name('comment')->where(['id' => $id])->update(['status' => 1]);
        if ($flag) {
            return json( ['code' => 1, 'data' => '', 'msg' => '删除成功'] );
        } else {
            return json( ['code' => 1, 'data' => '', 'msg' => '删除失败'] );
        }
    }

}
