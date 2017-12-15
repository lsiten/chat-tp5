<?php
namespace app\admin\model;

use think\Model;

class VipCard extends Model
{
    protected $table = 'lsiten_vip_card';
    protected $autoWriteTimestamp = true;
    // 定义时间戳字段名
    protected $createTime = 'ctime';
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
     * 添加广告数据
     * @param $param
     */
    public function insertData($param)
    {
        if ($param['usetime'] != '') {
            $timeArr = explode(" - ", $param['usetime']);
            $param['stime'] = strtotime($timeArr[0]);
            $param['etime'] = strtotime($timeArr[1]);
        }
        $num = $param['num'];
        unset($param['usetime']);
        unset($param['num']);
        
        try{
            $Data = [];
            for ($i = 0; $i < $num; $i++) {
                $cardnopwd = $this->getCardNoPwd();
                $param['cardno'] = $cardnopwd['no'];
                $param['cardpwd'] = $cardnopwd['pwd'];
                $Data[] = $param;
            }
            $result = $this->saveAll($Data);
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
    private function getCardNoPwd()
    {
        $dict_no = "0123456789";
        $length_no = 10;
        $dict_pwd = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $length_pwd = 10;
        $card['no'] = "";
        $card['pwd'] = "";
        for ($i = 0; $i < $length_no; $i++) {
            $card['no'] .= $dict_no[rand(0, (strlen($dict_no) - 1))];
        }
        for ($i = 0; $i < $length_pwd; $i++) {
            $card['pwd'] .= $dict_pwd[rand(0, (strlen($dict_pwd) - 1))];
        }
        return $card;
    }

}