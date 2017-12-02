<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

class Banner extends Model
{
    protected $table = 'lsiten_banner';

    /**
     * 根据搜索条件获去Banner列表信息
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getBannerByWhere( $offset, $limit ,$where)
    {
        if($where)
            $this->where($where);
        return $this->limit($offset, $limit)->where(["type"=>1,"status"=>0])->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的Banner
     * @param $where
     */
    public function getAllBanner($where)
    {
         if($where)
            $this->where($where);
        return $this->where(["type"=>1,"status"=>0])->count();
    }

    /**
     * 根据搜索条件获去广告列表信息
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getAdvertisementByWhere( $offset, $limit ,$where)
    {
         if($where)
            $this->where($where);
        return $this->limit($offset, $limit)->where(["type"=>2,"status"=>0])->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的广告
     * @param $where
     */
    public function getAllAdvertisement($where)
    {
         if($where)
            $this->where($where);
        return $this->where(["type"=>2,"status"=>0])->count();
    }
    /**
     * 根据搜索条件获取所有的用户
     * @param $where
     */
    public function checkName( $name )
    {
        return $this->where('title', $name)->find();
    }

    /**
     * 根据搜索条件获取所有的用户
     * @param $where
     */
    public function checkNameEdit( $name, $id )
    {
        return $this->where("username = '".$name."' and id != $id")->find();
    }

    /**
     * 添加用户数据
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

                return ['code' => 1, 'data' => $result, 'msg' => '添加成功'];
            }
        }catch( PDOException $e){

            return ['code' => -2, 'data' => '', 'msg' => $e->getMessage()];
        }
    }

    /**
     * 编辑用户数据
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

    /**
     * 根据id获取用户信息
     * @param $id
     */
    public function getOneData($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 根据id字符串查询用户信息
     * @param $id
     */
    public function findDataByIds($ids)
    {
        return $this->where("id in ($ids)")->select();
    }

    /**
     * 删除用户
     * @param $id
     */
    public function delData($id)
    {
        try{

            $this->where('id', $id)->update(["status"=>1]);
            return ['code' => 1, 'data' => '', 'msg' => '删除用户成功'];

        }catch( PDOException $e){
            return ['code' => 0, 'data' => '', 'msg' => $e->getMessage()];
        }
    }
}