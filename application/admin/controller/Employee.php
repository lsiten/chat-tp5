<?php
// +----------------------------------------------------------------------
// | 员工管理基础类--Admin分组Employee类
// +----------------------------------------------------------------------
namespace app\admin\controller;
use app\admin\model\Employee as EmployeeModel;
class Employee extends Base
{

    public static $_memployee;

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
        self::$_memployee = db('employee');
    }

    //默认跳转至登陆页面
    public function index()
    {
        $this->redirect('Admin/Public/login');
    }

    // 员工列表
    public function employeeList()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['username'] = ['like', '%' . $param['searchText'] . '%'];
            }
            //绑定搜索条件与分页
            $employee = model('Employee');
            $return['total'] = $employee->where($where)->count(); //总数据
            $selectResult = $employee->where($where)->limit($offset,$limit)->order('id DESC')->select();


            foreach($selectResult as $key=>$vo){

                if($vo['vipid'] == 0)
                {
                    $operate = '<a href="javascript:employeeedit('.$vo['id'].')" class="btn btn-success btn-xs" data-loader="App-loader" data-loadername="员工设置"><i class="fa fa-edit"></i> 编辑</a>';
                    $operate .= '<a href="javascript:employeeDel('.$vo['id'].')" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> 删除</a>';
                    $operate .= '<a data-id="'.$vo['id'].'" href="javascript:;" onclick="showQrcode(this);" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-resize-small"></i> 绑定</a>';
                }
                else{
                    $operate = '<a href="javascript:employeeedit('.$vo['id'].')" class="btn btn-success btn-xs" data-loader="App-loader" data-loadername="员工设置"><i class="fa fa-edit"></i> 编辑</a>';
                    $operate .= '<a href="javascript:employeeDel('.$vo['id'].')" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> 删除</a>';
                    $operate .= '<a data-id="'.$vo['id'].'" href="javascript:;" onclick="showQrcode(this);" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-resize-small"></i> 重绑</a>';
                    $operate .= '<a href="javascript:unbindvip('.$vo['id'].')" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-resize-full"></i> 解绑</a>';      
                }
                $selectResult[$key]['operate'] = $operate;

            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }
    public function employeeadd(){
         if (request()->isPost()) {
            $EmployeeModel = new EmployeeModel();
            //新增处理
            $params = input('post.');
            $flag = $EmployeeModel->insertData( $params );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => $flag['msg']] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '添加成功'] );
        }else{
            return $this->fetch();
        }
    }

    public function employeeedit(){
        $EmployeeModel = new EmployeeModel();
        if (request()->isPost()) {
           //新增处理
           $params = input('post.');
           $flag = $EmployeeModel->editData( $params );

           if( 1 != $flag['code'] ){
               return json( ['code' => -6, 'data' => '', 'msg' => $flag['msg']] );
           }
           return json( ['code' => 1, 'data' => "", 'msg' => '修改成功'] );
       }else{
           $id = input('param.id');
           $this->assign('item',$EmployeeModel->getOneData($id));
           return $this->fetch();
       }
   }
    // 员工业绩
    public function achievement()
    {
   
    if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['username'] = ['like', '%' . $param['searchText'] . '%'];
            }
            //绑定搜索条件与分页
            $employee = model('Employee');
            $return['total'] = $employee->where($where)->count(); //总数据
            $selectResult = $employee->caculateAchievement($where,$offset,$limit,'id DESC');


            foreach($selectResult as $key=>$vo){

                if($vo['vipid'] == 0)
                {
                    $operate = '<a href="javascript:employeeedit('.$vo['id'].')" class="btn btn-success btn-xs" data-loader="App-loader" data-loadername="员工设置"><i class="fa fa-edit"></i> 编辑</a>';
                    $operate .= '<a href="javascript:employeeDel('.$vo['id'].')" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> 删除</a>';
                    $operate .= '<a data-id="'.$vo['id'].'" href="javascript:;" onclick="showQrcode(this);" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-resize-small"></i> 绑定</a>';
                }
                else{
                    $operate = '<a href="javascript:employeeedit('.$vo['id'].')" class="btn btn-success btn-xs" data-loader="App-loader" data-loadername="员工设置"><i class="fa fa-edit"></i> 编辑</a>';
                    $operate .= '<a href="javascript:employeeDel('.$vo['id'].')" class="btn btn-danger btn-xs"><i class="fa fa-trash-o"></i> 删除</a>';
                    $operate .= '<a data-id="'.$vo['id'].'" href="javascript:;" onclick="showQrcode(this);" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-resize-small"></i> 重绑</a>';
                    $operate .= '<a href="javascript:unbindvip('.$vo['id'].')" class="btn btn-warning btn-xs"><i class="glyphicon glyphicon-resize-full"></i> 解绑</a>';      
                }
                $selectResult[$key]['operate'] = $operate;

            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        return $this->fetch();
    }

    // 会员业绩
    public function vipAchievement()
    {
        $memployee = M('employee');

        $eid = I('eid') ? I('eid') : 0;
        $employee = $memployee->where(array('id' => $eid))->find();

        // 过滤员工
        if (!$employee) {
            echo "非法操作！";
            exit();
        }
        $this->assign('employee', $employee);

        // 设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '业绩统计',
                'url' => U('Admin/Employee/achievement'),
            ),
            '1' => array(
                'name' => $employee['name'] . " 会员列表",
                'url' => U('Admin/Employee/vipAchievement', array('eid' => $eid)),
            ),
            '2' => array(
                'name' => '详情',
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));

        //绑定搜索条件与分页
        $mvip = D('Vip');
        $morder = M('Shop_order');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['name|nickname'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        $map['employee'] = $eid;
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $mvip->where($map)->page($p, $psize)->select();
        $cache = $mvip->caculateVipAchievement($cache, self::$SHOP['set']['fxname']);
        $count = $mvip->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '会员列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    // 员工绑定二维码
    public function getqrcode()
    {
        $employeeid = input('param.eid');
        $employee = model('employee')->where(array('id' => $employeeid))->find();
        $url = self::$SYS['set']['wxurl'] . '/App/Employee/bindVip/employee/' . $employee['userpass'] . '/eid/' . $employee['id'];
        $QR = new \wx\QRcode();
        $QR::png($url);
    }

    // 解除绑定
    public function unbindVip()
    {
        $employeeid = input('param.eid');
        $employee = model('Employee')->where(array('id' => $employeeid))->find();
        if ($employee) {
            $employeeData['vipid'] = 0;
            $re = model('employee')->save($employeeData,['id'=>$employee->id]);
            if ($re) {
                $return['code'] = 1;
                $return['msg'] = '解绑成功!';
            } else {
                $return['code'] = 0;
                $return['msg'] = '解绑失败!';
            }
        } else {
            $return['code'] = 0;
            $return['msg'] = '未知数据!';
        }
        return json($return);
    }

    // 删除员工
    public function employeeDel()
    {
        $id = input('param.id');//必须使用get方法
        $m = model('Employee');
        if (!$id) {
            $return['code'] = 0;
            $return['msg'] = 'ID不能为空!';
            return json($return);
        }
        $re = $m->where("id",$id)->delete();
        if ($re) {
            $return['code'] = 1;
            $return['msg'] = '删除成功!';
        } else {
            $return['code'] = 0;
            $return['msg'] = '删除失败!';
        }
        return json($return);
    }

    // =====================================================
    // 以下为员工个人模块控制器
    // =====================================================
    // 个人中心
    public function main()
    {
        /*
        $bread = array(
            '0' => array(
                'name' => '主控面板',
                'url' => U('Admin/Employee/main'),
            ),
        );
        $this->assign('breadhtml',$this->getBread($bread));
        */
        $this->display();
    }

    // 员工会员中心
    public function vipCenter()
    {

        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员列表',
                'url' => U('Admin/Vip/vipList'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));

        // 员工介入
        $employee = $_SESSION['CMS']['user'];
        // $temparr = array();
        // $temp = M('Vip')->field('id')->where(array('employee'=>$employee['id']))->select();
        // foreach($temp as $v){
        // 	array_push($temparr,$v['id']);
        // }
        if (!$employee) {
            echo "请重新登陆";
            exit();
        }

        $map['employee'] = $employee['id'];
        //绑定搜索条件与分页
        $m = M('Vip');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['nickname|mobile'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        foreach ($cache as $k => $v) {
            $cache[$k]['levelname'] = M('Vip_level')->where('id=' . $cache[$k]['levelid'])->getField('name');
            if ($v['isfxgd']) {
                $cache[$k]['fxname'] = '超级VIP';
            } else {
                if ($v['isfx']) {
                    $cache[$k]['fxname'] = $_SESSION['SHOP']['set']['fxname'];
                } else {
                    $cache[$k]['fxname'] = '会员';
                }
            }
            // 写入员工数据
            if ($v['employee']) {
                $cache[$k]['employee'] = $employee[$v['employee']]['nickname'];
            } else {
                $cache[$k]['employee'] = '无';
            }
        }
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '会员列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    // 员工订单中心
    public function orderCenter()
    {
        $bread = array(
            '0' => array(
                'name' => '订单中心',
                'url' => U('Admin/Employee/index'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        $status = I('status');
        if ($status || $status == '0') {
            $map['status'] = $status;
            //交易满7天
            if ($status == 8) {
                $map['status'] = 3;
                $seven = time() - 604800;
                $map['ctime'] = array('elt', $seven);
            }
            // 当天所有订单，零点算起
            if ($status == 9) {
                unset($map['status']);
                $today = strtotime(date("Y-m-d"));
                $map['ctime'] = array('egt', $today);
                //echo $today;
            }
        }
        $this->assign('status', $status);
        //绑定搜索条件与分页
        $employee = $_SESSION['CMS']['user'];
        $temparr = array();
        $temp = M('Vip')->field('id')->where(array('employee' => $employee['id']))->select();
        foreach ($temp as $v) {
            array_push($temparr, $v['id']);
        }
        $map['vipid'] = array('in', in_parse_str($temparr));
        $m = M('Shop_order');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            //订单号邦定
            $map['oid|vipmobile'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->order('ctime desc')->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '我的订单', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    // =====================================================
    // 以上为员工个人模块控制器
    // =====================================================
}

?>