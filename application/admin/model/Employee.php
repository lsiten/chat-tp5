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
}