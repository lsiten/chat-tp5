<?php
namespace app\app\controller;
use think\Controller;
class Employee extends Controller{
    public function bindVip(){
        $emp = input('param.employee');
        $eid = input('param.eid');
        $employee = db('employee')->where(array('userpass' => $emp, 'id' => $eid))->find();
        $vip = db('vip')->where(array('openid' => session('sqopenid')))->find();
        print_r(session('sqopenid'));
        if (!$employee) {
            $this->redirect('/index/index');
        } else if (!$vip) {
            echo "用户信息不存在";
            exit();
        }

        $temp = db('employee')->where(array('vipid' => $vip['id']))->find();
        if ($temp) {
            $this->assign('img', "/static/app/images/binded.jpg");
            // echo "该账号已绑定，无法再进行绑定操作！请先到管理员处解除绑定再重新绑定";
            // exit();
        } else {
            $data['vipid'] = $vip['id'];
            $re = db('employee')->where('id',$employee->id)->update($data);
            if ($re) {
                $this->assign('img', "/static/app/images/bindsuccess.jpg");
                // echo "绑定成功";
                // exit();
            } else {
                $this->assign('img', "/static/app/images/bindfailure.jpg");
                // echo "绑定失败";
                // exit();
            }
        }
        return $this->fetch();
    }
}