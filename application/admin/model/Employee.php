<?php
namespace app\admin\model;

use think\Model;

class Employee extends Model
{
    protected $table = 'lsiten_employee';
    /**
     * 根据id获取员工
     * @param $id
     */
    public function getOneData($id)
    {
        return $this->where('id', $id)->find();
    }
     /**
     * 添加员工数据
     * @param $param
     */
    public function insertData($param)
    {
        $weight = $this->sum('weight');
        if ($weight + $param['weight'] > 100) {
            return ['code' => -1, 'data' => '', 'msg' => "所有员工总权重不可超过100！"];
        }
        try{
            $param['userpass'] = md5($param['userpass']);
            $result =  $this->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{

                return ['code' => 1, 'data' => $result, 'msg' => '添加成功'];
            }
        }catch( PDOException $e){

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑员工数据
     * @param $param
     */
    public function editData($param)
    {
        if ($param['userpass']) {
            $param['userpass'] = md5($param['userpass']);
        } else {
            unset($param['userpass']);
        }
        $weight = $this->where(array('id' => array('neq', $param['id'])))->sum('weight');
        if ($weight + $param['weight'] > 100) {
            return ['code' => -1, 'data' => '', 'msg' => "所有员工总权重不可超过100！"];
        }
        try{

            $result =  $this->save($param, ['id' => $param['id']]);

            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{

                return ['code' => 1, 'data' => '', 'msg' => '编辑用户成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }



    public function caculateAchievement($where=[],$offset=0,$limit=10,$order='id DESC')
    {
        $employees = $this->where($where)->limit($offset,$limit)->order($order)->select();
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $beginMonth = mktime(0, 0, 0, date('m'), 1, date('Y'));

        $morder = M('Shop_order');
        $mvip = M('vip');

        // 遍历所有员工
        foreach ($employees as $k => $v) {
            // 提取所有下线
            $map = array();
            $temparr = array();
            $temp = $mvip->field('id')->where(array('employee' => $v['id']))->select();
            foreach ($temp as $vv) {
                array_push($temparr, $vv['id']);
            }

            // 所有会员总数
            $count = $mvip->where(array('id' => array('in', in_parse_str($temparr))))->count();
            $employees[$k]['vip_number'] = $count ? $count : 0;

            // 基本条件
            $map['vipid'] = array('in', in_parse_str($temparr));

            // 所有订单量
            $count = $morder->where($map)->count();
            $employees[$k]['all_order_number'] = $count ? $count : 0;

            // 失败订单量
            $map['status'] = array('in', array('4', '7', '6', '0'));// 包括退货中、退货完成、已关闭、已取消
            $count = $morder->where($map)->count();
            $employees[$k]['failure_order_number'] = $count ? $count : 0;

            // 成功订单量
            $map['status'] = array('in', array('2', '3', '5'));// 包括已付款、已发货、已完成
            $count = $morder->where($map)->count();
            $employees[$k]['success_order_number'] = $count ? $count : 0;
            $employees[$k]['success_order_payprice'] = round($morder->where($map)->sum('payprice'), 2);

            // 当月成交量
            $map['ctime'] = array('egt', $beginMonth);
            $count = $morder->where($map)->count();
            $employees[$k]['month_order_number'] = $count ? $count : 0;
            $employees[$k]['month_order_payprice'] = round($morder->where($map)->sum('payprice'), 2);

            // 当天成交量
            $map['ctime'] = array('egt', $beginToday);
            $count = $morder->where($map)->count();
            $employees[$k]['today_order_number'] = $count ? $count : 0;
            $employees[$k]['today_order_payprice'] = round($morder->where($map)->sum('payprice'), 2);
        }

        return $employees;
    }

}