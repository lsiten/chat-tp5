<?php
// +----------------------------------------------------------------------
// | 员工管理基础类--Admin分组Employee类
// +----------------------------------------------------------------------
namespace app\admin\controller;

class Employee extends Base
{

    public static $_memployee;

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
        self::$_memployee = D('employee');
    }

    //默认跳转至登陆页面
    public function index()
    {
        $this->redirect('Admin/Public/login');
    }

    // 员工列表
    public function employeeList()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '员工列表',
                'url' => U('Admin/Employee/employeeList'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('employee');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['username'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '员工列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    // 员工业绩
    public function achievement()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '业绩统计',
                'url' => U('Admin/Employee/achievement'),
            ),
            '1' => array(
                'name' => '详情',
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('employee');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['username'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $cache = D('employee')->caculateAchievement($cache);
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '员工列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
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

    // 员工设置
    public function employeeSet()
    {
        $id = I('id');
        $m = M('employee');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '员工列表',
                'url' => U('Admin/User/userList'),
            ),
            '1' => array(
                'name' => '员工编辑',
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //die('aa');
            $data = I('post.');
            // 总权重不能超标
            $weight = $m->where(array('id' => array('neq', $id)))->sum('weight');
            if ($weight + $data['weight'] > 100) {
                $info['status'] = 0;
                $info['msg'] = '所有员工总权重不可超过100！';
                $this->ajaxReturn($info);
            }
            if ($id) {
                if ($data['userpass']) {
                    $data['userpass'] = md5($data['userpass']);
                } else {
                    unset($data['userpass']);
                }
                $re = $m->save($data);
                if (FALSE !== $re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $data['userpass'] = md5($data['userpass']);
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
        // $oath = M('User_oath')->where(array('status' => 1))->select();
        // $this->assign('oath', $oath);
        //处理编辑界面
        if ($id) {
            $cache = $m->where('id=' . $id)->find();
            $this->assign('cache', $cache);
        }
        $this->display();
    }

    // 员工绑定二维码
    public function getqrcode()
    {
        $employeeid = I('eid');
        $employee = M('employee')->where(array('id' => $employeeid))->find();
        $url = self::$SYS['set']['wxurl'] . '/App/Employee/bindVip/employee/' . $employee['userpass'] . '/eid/' . $employee['id'];
        $QR = new \Util\QRcode();
        $QR::png($url);
    }

    // 解除绑定
    public function unbindVip()
    {
        $employeeid = $_GET['eid'];
        $employee = M('employee')->where(array('id' => $employeeid))->find();
        if ($employee) {
            $employee['vipid'] = 0;
            $re = M('employee')->save($employee);
            if ($re) {
                $info['status'] = 1;
                $info['msg'] = '解绑成功!';
            } else {
                $info['status'] = 0;
                $info['msg'] = '解绑失败!';
            }
        } else {
            $info['status'] = 0;
            $info['msg'] = '未知数据!';
        }
        $this->ajaxReturn($info);
    }

    // 删除员工
    public function employeeDel()
    {
        $id = $_GET['id'];//必须使用get方法
        $m = M('Employee');
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