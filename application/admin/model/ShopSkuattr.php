<?php
namespace app\admin\model;

use think\Model;

class ShopSkuattr extends Model
{
    protected $table = 'lsiten_shop_skuattr';
    // 定义时间戳字段名
    protected $createTime = 'cctime';
    protected $updateTime = 'utime';
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
            $dt['name'] = $param['name'];
            $result = $this->save($dt);
            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                if ($param['newitem']) {
                    $mitem = db('shop_skuattr_item');
                    $dit['pid'] = $this->id;
                    $items = array_filter(explode(',', $param['newitem']));
                    foreach ($items as $v) {
                        $rit = $mitem->insert(['name'=>$v,'pid'=>$this->id]);
                        if ($rit) {
                            $rr['path'] = $this->id . $mitem->getLastInsID();
                            $rerr = $mitem->where('id',$mitem->getLastInsID())->update($rr);
                        }
                    }
                    $son = $mitem->where('pid=' . $this->id)->field('name,path')->select();
                    $dson['items'] = "";
                    $dson['itemspath'] = "";
                    foreach ($son as $v) {
                        $dson['items'] = $dson['items'] . $v['name'] . ',';
                        $dson['itemspath'] = $dson['itemspath'] . $v['path'] . ',';
                    }
                    $rfather = $this->save($dson,['id'=>$result]);
                }
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
            $id = $param['id'];
            $result =  $this->save($param, ['id' => $id]);

            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                if ($param['newitem']) {
                    $items = array_filter(explode(',', $param['newitem']));
                    foreach ($items as $v) {
                        $mitem = db('shop_skuattr_item');               
                        $rit = $mitem->insert(['name'=>$v,'pid'=>$id]);
                        if ($rit) {
                            $rr['path'] = $id . $mitem->getLastInsID();
                            $rerr = $mitem->where('id',$mitem->getLastInsID())->update($rr);
                        }
                    }
                    $son = $mitem->where('pid=' . $id)->field('name,path')->select();
                    $dson['items'] = "";
                    $dson['itemspath'] = "";
                    foreach ($son as $v) {
                        $dson['items'] = $dson['items'] . $v['name'] . ',';
                        $dson['itemspath'] = $dson['itemspath'] . $v['path'] . ',';
                    }
                    $rfather = $this->save($dson,['id'=>$id]);
                }
                return ['code' => 1, 'data' => '', 'msg' => '编辑用户成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}