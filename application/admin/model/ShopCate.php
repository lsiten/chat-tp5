<?php
namespace app\admin\model;

use think\Model;

class ShopCate extends Model
{
    protected $table = 'lsiten_shop_cate';


    /**
     * 根据id获取关键字
     * @param $id
     */
    public function getOneData($id)
    {
        return $this->where('id', $id)->find();
    }


    public function getTreeCate($where=[],$limit,$offset,$order="sorts ASC"){
        $order = "path ASC,".$order;
        $data = $this->where($where)
                     ->limit($offset,$limit)
                     ->order($order)
                     ->select();
        $data = $this->toTree($data);
        return $data;

    }

    private function toTree($data){
        foreach($data as $item)
        {  
            $dataItem = $item->toArray();
            $dataResult[$item->id] = $dataItem;
        }
        foreach($dataResult as $key=>$item)
        {  
            if($item['pid'])
            {
                $dataResult[$item['pid']]['child'][] = $item;
                unset($dataResult[$item['id']]);
            }
        }
        return $dataResult;
    }

    /**
     * 添加分类数据
     * @param $param
     */
    public function insertData($param)
    {
        if ($param['pid']) {
            //更新父级，强制处理
            $path = setPath("Shop_cate", $param['pid']);
            $param['path'] = $path['path'];
            $param['lv'] = $path['lv'];
        } else {
            $param['path'] = 0;
            $param['lv'] = 1;
        }
        try{

            $result =  $this->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => -1, 'data' => '', 'msg' => $this->getError()];
            }else{
                //更新父级，暂不做错误处理
                if ($param['pid']) {
                    $re = setSoncate("Shop_cate", $param['pid']);
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

        $old = $this->where('id=' . $param['id'])->limit(1)->find();
        if ($old['pid'] != $param['pid']) {
            $hasson = $this->where('pid=' . $param['id'])->limit(1)->find();
            if ($hasson) {
                return ['code' => 0, 'data' => '', 'msg' => "此分类有子分类，不可以移动！"];
            }
        }

        if ($param['pid']) {
            //更新Path，强制处理
            $path = setPath("Shop_cate", $param['pid']);
            $param['path'] = $path['path'];
            $param['lv'] = $path['lv'];
        } else {
            $param['path'] = 0;
            $param['lv'] = 1;
        }
        try{

            $result =  $this->save($param, ['id' => $param['id']]);

            if(false === $result){
                // 验证失败 输出错误信息
                return ['code' => 0, 'data' => '', 'msg' => $this->getError()];
            }else{
                //更新新老父级，暂不做错误处理
                if ($old['pid'] != $param['pid']) {
                    $re = setSoncate("Shop_cate", $param['pid']);
                    $rold = setSoncate("Shop_cate", $old['pid']);
                    return ['code' => 1, 'data' => '', 'msg' => '编辑用户成功'];
                } else {
                    $re = setSoncate("Shop_cate", $param['pid']);
                }
                return ['code' => 1, 'data' => '', 'msg' => '编辑用户成功'];
            }
        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }


    /**
     * 删除分类
     * @param $id
     */
    public function delData($id)
    {
        if (!$id) {
            return ['code' => 0, 'data' => '', 'msg' => 'ID不能为空！'];
        }
       
        try{

            $self = $this->where('id=' . $id)->limit(1)->find();
            $re = $this->where('id', $id)->delete();
            // 删除所有子类
            $tempList = explode(',', $self['soncate']);
            foreach ($tempList as $k => $v) {
                $res = $this->where('id', $v)->delete();
            }
            if ($re) {
                //更新上级soncate
                if ($self['pid']) {
                    $re = setSoncate("Shop_cate", $self['pid']);
                }
                return ['code' => 1, 'data' => '', 'msg' => '删除成功!'];            
            } else {
                return ['code' => 0, 'data' => '', 'msg' => '删除失败!'];            
            }

        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}