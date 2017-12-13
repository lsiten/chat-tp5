<?php
namespace app\admin\model;

use think\Model;

class ShopLabel extends Model
{
    protected $table = 'lsiten_shop_label';
    /**
     * 根据id获取关键字
     * @param $id
     */
    public function getOneData($id)
    {
        return $this->where('id', $id)->find();
    }


    /**
     * 添加分类数据
     * @param $param
     */
    public function insertData($param)
    {
        
        try{
            $result =  $this->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                //更新父级，暂不做错误处理
                return ['code' => 1, 'data' => $result, 'msg' => '添加成功'];
            }
        }catch( PDOException $e){

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑分类数据
     * @param $param
     */
    public function editData($param)
    {
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