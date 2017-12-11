<?php
namespace app\index\controller;

use think\Controller;
use think\Request;

class Wx extends Controller
{
     //全局相关
     public static $_set; //缓存全局配置
     public static $_shop; //缓存全局配置
 
     public static $_wx; //缓存微信对象
     public static $_ppvip; //缓存会员通信证模型
     public static $_ppvipmessage; //缓存会员消息模型
     public static $_fx; //缓存分销模型
     public static $_fxlog; //缓存分销新用户推广模型	qd(渠道)=1为朋友圈，2为渠道场景二微码
     public static $_token;
     public static $_location; //用户地理信息
     //信息接收相关
     public static $_revtype; //微信发来的信息类型
     public static $_revdata; //微信发来的信息内容
     //信息推送相关
     //public static $_url='http://shop.hylanca.com/';//推送地址前缀
     public static $_url;
     public static $_wecha_id;
     public static $_actopen;
 
     public static $WAP;//CMS全局静态变量
 
     // 自动计算模型
     public static $_demployee;
 
     public function _initialize()
     {
         // 读取商城全局配置
         self::$_shop = model('Shop_set')->find();
         //读取用户配置存全局
         self::$_set = model('Set')->find();
         self::$_url = self::$_set['wxurl'];
         self::$_token = self::$_set['wxtoken'];
         //检测token是否合法
         $tk = input('param.token');
         if ($tk != self::$_token) {
             die('token error');
         }
         //缓存微信API模型类
         $options['token'] = self::$_token;
         $options['appid'] = self::$_set['wxappid'];
         $options['appsecret'] = self::$_set['wxappsecret'];
         self::$_wx = new \wx\Wechat($options);
         //缓存通行证数据模型
         self::$_ppvip = model('Vip');
         self::$_ppvipmessage = model('Vip_message');
         self::$_fx = model('Vip');
         self::$_fxlog = model('Vip_log_sub');
         self::$_demployee = db('employee');
 
         self::$WAP['vipset'] = $this->checkVipSet();
 
         //判断验证模式
         if (Request::instance()->isGet()) {
             self::$_wx->valid();
         } else {
             if (!self::$_wx->valid(true)) {
                 die('no access!!!');
             }
             //读取微信平台推送来的信息类型存全局
             self::$_revtype = self::$_wx->getRev()->getRevType();
             //读取微型平台推送来的信息存全局
             self::$_revdata = self::$_wx->getRevData();
             self::$_wecha_id = self::$_wx->getRevFrom();
             //读取用户地理信息
             //self::$_location=self::$_wx->getRevData();
             $str = "";
             foreach (self::$_revdata as $k => $v) {
                 $str = $str . $k . "=>" . $v . '  ';
             }
             file_put_contents('./Data/app_rev.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
 
         }
 
     }

     //返回VIP配置
    public function checkVipSet()
    {
        $set = model('Vip_set')->find();
        return $set ? $set : utf8error('会员设置未定义！');
    }

    public function index()
    {

        $this->go();

    } //index类结束

    /*微信访问判断主路由控制器by App
    return
     */
    public function go()
    {

        switch (self::$_revtype) {
            case \wx\Wechat::MSGTYPE_TEXT:
                $this->checkKeyword(self::$_revdata['Content']);
                //self::$_wx->text(self::$_revdata['Content'])->reply();
                break;
            case \wx\Wechat::MSGTYPE_EVENT:
                $this->checkEvent(self::$_revdata['Event']);
                break;
            case \wx\Wechat::MSGTYPE_IMAGE:
                //$this -> checkImg();
                self::$_wx->text('本系统暂不支持图片信息！')->reply();
                break;
            default:
                self::$_wx->text("本系统暂时无法识别您的指令！")->reply();
        }

    } //end go

    /*关键词指引
    return
     */
    public function checkKeyword($key)
    {
        file_put_contents('./Data/app_debug.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:111'. PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);                
        //更新认证服务号的微信用户表信息（24小时内）
        $reUP = $this->updateUser(self::$_wecha_id);
        //App调试模式
        if (substr($key, 0, 5) == 'App-') {
            $this->toApp(substr($key, 5));
        }

        //强制关键词匹配
        //*********************************************************************
        if ($key == '操作指导') {
            $msg = '未配置';
            self::$_wx->text($msg)->reply();
        }
        if ($key == "员工二维码") {

            // 获取用户信息
            $map['openid'] = self::$_revdata['FromUserName'];
            $vip = self::$_ppvip->where($map)->find();

            // 用户校正
            if (!$vip) {
                $msg = "用户信息缺失，请重新关注公众号";
                self::$_wx->text($msg)->reply();
                exit();
            }

            // 获取员工信息
            $employee = model('Employee')->where(array('vipid' => $vip['id']))->find();

            // 员工校正
            if (!$employee) {
                $msg = "抱歉，您不是员工，请先联系系统管理员！";
                self::$_wx->text($msg)->reply();
                exit();
            }

            // 过滤连续请求-打开
            if (F("employee" . $vip['openid']) != null) {
                $msg = "员工二维码正在生成，请稍等！";
                self::$_wx->text($msg)->reply();
                exit();
            } else {
                F("employee" . $vip['openid'], $vip['openid']);
            }

            // 生产二维码基本信息，存入本地文档，获取背景
            $background = $this->createQrcodeBgEmp();
            //$qrcode = $this->createQrcode($vip['id'],$vip['openid']);
            $qrcode = $this->createEmployeeQrcode($employee['id'], $vip['openid']);
            if (!$qrcode) {
                $msg = "员工二维码 生成失败";
                self::$_wx->text($msg)->reply();
                F("employee" . $vip['openid'], null);
                exit();
            }
            // 生产二维码基本信息，存入本地文档，获取背景 结束

            // 获取头像信息
            $mark = false; // 是否需要写入将图片写入文件
            $headimg = $this->getRemoteHeadImage($vip['headimgurl']);
            if (!$headimg) {// 没有头像先从头像库查找，再没有就选择默认头像
                if (file_exists('./QRcode/headimg/' . $vip['openid'] . '.jpg')) { // 获取不到远程头像，但存在本地头像，需要更新
                    $headimg = file_get_contents('./QRcode/headimg/' . $vip['openid'] . '.jpg');
                } else {
                    $headimg = file_get_contents('./QRcode/headimg/' . 'default' . '.jpg');
                }
                $mark = true;
            }
            $headimg = imagecreatefromstring($headimg);
            // 获取头像信息 结束

            // 生成二维码推广图片=======================

            // Combine QRcode and background and HeadImg
            $b_width = imagesx($background);
            $b_height = imagesy($background);
            $q_width = imagesx($qrcode);
            $q_height = imagesy($qrcode);
            $h_width = imagesx($headimg);
            $h_height = imagesy($headimg);
            imagecopyresampled($background, $qrcode, $b_width * 0.24, $b_height * 0.5, 0, 0, $q_width * 1.5, $q_height * 1.5, $q_width, $q_height);
            imagecopyresampled($background, $headimg, $b_width * 0.10, 12, 0, 0, 120, 120, $h_width, $h_height);

            // Set Font Type And Color
            $fonttype = './static/common/fonts/wqy-microhei.ttc';
            $fontcolor = imagecolorallocate($background, 0x00, 0x00, 0x00);

            // Combine All And Text, Then store in local
            imagettftext($background, 18, 0, 280, 100, $fontcolor, $fonttype, $vip['nickname']);
            imagejpeg($background, './QRcode/promotion/' . "employee" . $vip['openid'] . '.jpg');

            // 生成二维码推广图片 结束==================

            // 上传下载相应
            if (file_exists(getcwd() . "/QRcode/promotion/" . "employee" . $vip['openid'] . '.jpg')) {
                $filepath = getcwd() . "/QRcode/promotion/" . "employee" . $vip['openid'] . '.jpg';
                if (class_exists('\CURLFile')) {
                    $data = array('media' => new \CURLFile(realpath($filepath)));
                } else {
                    $data = array('media' => '@' . realpath($filepath));
                }
                $uploadresult = self::$_wx->uploadMedia($data, 'image');
                self::$_wx->image($uploadresult['media_id'])->reply();
                exit();
            } else {
                $msg = "员工二维码生成失败";
                self::$_wx->text($msg)->reply();
            }
            // 上传下载相应 结束

            // 过滤连续请求-关闭
            F("employee" . $vip['openid'], null);

            // 后续数据操作（写入头像到本地，更新个人信息）
            if ($mark) {
                $tempvip = $this->apiClient(self::$_revdata['FromUserName']);
                $vip['nickname'] = $tempvip['nickname'];
                $vip['headimgurl'] = $tempvip['headimgurl'];
            } else {
                // 将头像文件写入
                imagejpeg($headimg, './QRcode/headimg/' . $vip['openid'] . '.jpg');
            }
        }


        if ($key == "推广二维码") {

            // 获取用户信息
            $map['openid'] = self::$_revdata['FromUserName'];
            $vip = self::$_ppvip->where($map)->find();

            // 用户校正
            if (!$vip) {
                $msg = "用户信息缺失，请重新关注公众号";
                self::$_wx->text($msg)->reply();
                exit();
            } else if ($vip['isfx'] == 0) {
                $msg = "您还未成为" . self::$_shop['fxname'] . "，请先购买成为" . self::$_shop['fxname'] . "！";
                self::$_wx->text($msg)->reply();
                exit();
            }

            // 过滤连续请求-打开
            if (F($vip['openid']) != null) {
                $msg = "推广二维码正在生成，请稍等！";
                self::$_wx->text($msg)->reply();
                exit();
            } else {
                F($vip['openid'], $vip['openid']);
            }

            // 生产二维码基本信息，存入本地文档，获取背景
            $background = $this->createQrcodeBg();
            $qrcode = $this->createQrcode($vip['id'], $vip['openid']);
            if (!$qrcode) {
                $msg = "专属二维码 生成失败";
                self::$_wx->text($msg)->reply();
                F($vip['openid'], null);
                exit();
            }
            // 生产二维码基本信息，存入本地文档，获取背景 结束

            // 获取头像信息
            $mark == false; // 是否需要写入将图片写入文件
            $headimg = $this->getRemoteHeadImage($vip['headimgurl']);
            if (!$headimg) {// 没有头像先从头像库查找，再没有就选择默认头像
                if (file_exists('./QRcode/headimg/' . $vip['openid'] . '.jpg')) { // 获取不到远程头像，但存在本地头像，需要更新
                    $headimg = file_get_contents('./QRcode/headimg/' . $vip['openid'] . '.jpg');
                } else {
                    $headimg = file_get_contents('./QRcode/headimg/' . 'default' . '.jpg');
                }
                $mark = true;
            }
            $headimg = imagecreatefromstring($headimg);
            // 获取头像信息 结束

            // 生成二维码推广图片=======================

            // Combine QRcode and background and HeadImg
            $b_width = imagesx($background);
            $b_height = imagesy($background);
            $q_width = imagesx($qrcode);
            $q_height = imagesy($qrcode);
            $h_width = imagesx($headimg);
            $h_height = imagesy($headimg);
            imagecopyresampled($background, $qrcode, $b_width * 0.24, $b_height * 0.5, 0, 0, 297, 297, $q_width, $q_height);
            imagecopyresampled($background, $headimg, $b_width * 0.10, 12, 0, 0, 120, 120, $h_width, $h_height);

            // Set Font Type And Color
            $fonttype = './Public/Common/fonts/wqy-microhei.ttc';
            $fontcolor = imagecolorallocate($background, 0x00, 0x00, 0x00);

            // Combine All And Text, Then store in local
            imagettftext($background, 18, 0, 280, 100, $fontcolor, $fonttype, $vip['nickname']);
            imagejpeg($background, './QRcode/promotion/' . $vip['openid'] . '.jpg');

            // 生成二维码推广图片 结束==================

            // 上传下载相应
            if (file_exists(getcwd() . "/QRcode/promotion/" . $vip['openid'] . '.jpg')) {
                $filepath = getcwd() . "/QRcode/promotion/" . $vip['openid'] . '.jpg';
                if (class_exists('\CURLFile')) {
                    $data = array('media' => new \CURLFile(realpath($filepath)));
                } else {
                    $data = array('media' => '@' . realpath($filepath));
                }
                $uploadresult = self::$_wx->uploadMedia($data, 'image');
                self::$_wx->image($uploadresult['media_id'])->reply();
                exit();
            } else {
                $msg = "专属二维码生成失败";
                self::$_wx->text($msg)->reply();
            }
            // 上传下载相应 结束

            // 过滤连续请求-关闭
            F($vip['openid'], null);

            // 后续数据操作（写入头像到本地，更新个人信息）
            if ($mark) {
                $tempvip = $this->apiClient(self::$_revdata['FromUserName']);
                $vip['nickname'] = $tempvip['nickname'];
                $vip['headimgurl'] = $tempvip['headimgurl'];
            } else {
                // 将头像文件写入
                imagejpeg($headimg, './QRcode/headimg/' . $vip['openid'] . '.jpg');
            }

        }
        //用户自定义关键词匹配
        //*********************************************************************
        $mapkey['keyword'] = $key;
        file_put_contents('./Data/app_debug.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $key . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);        
        //用户自定义关键词
        $keyword = model('Wx_keyword');
        $ruser = $keyword->where($mapkey)->find();
        file_put_contents('./Data/app_debug.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $ruser['type'] . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);        
        
        
        if ($ruser) {
            //进入用户自定义关键词回复
            $this->toKeyUser($ruser);
        }
        //*********************************************************************

        //系统自定义关键词数组
        //$osWgw=array('官网','首页','微官网','Home','home','Index','index');
        //if(in_array($key,$osWgw)){$this->toWgw('index',false);}

        //未知关键词匹配
        //*********************************************************************
        $this->toKeyUnknow($key);
    }


    public function checkEvent($event)
    {
        switch ($event) {
            //首次关注事件
            case 'subscribe':
                //用户关注：判断是否已存在
                //检查用户是否已存在
                $openid = self::$_revdata['FromUserName'];
                $isold = self::$_ppvip->where(array("openid" => $openid))->find();
                if ($isold) {
                    $data['subscribe'] = 1;
                    $re = self::$_ppvip->where($old)->setField('subscribe', 1);
                    //增加上线关注人数
                    if ($isold['pid']) {
                        $fxs = self::$_fx->where('id=' . $isold['pid'])->find();
                        if ($fxs) {
                            $dlog['ppid'] = $isold['pid'];
                            $dlog['from'] = $isold['id'];
                            $dlog['fromname'] = $isold['nickname'];
                            $dlog['to'] = $fxs['id'];
                            $dlog['toname'] = $fxs['nickname'];
                            $dlog['issub'] = 1;
                            $dlog['ctime'] = time();
                            $rdlog = self::$_fxlog->save($dlog);
                            $rfxs = self::$_fx->where('id=' . $isold['pid'])->setInc('total_xxsub', 1);    //下线累计关注
                        } else {
                            $dlog['ppid'] = 0;
                            $dlog['from'] = $isold['id'];
                            $dlog['fromname'] = $isold['nickname'];
                            $dlog['to'] = 0;
                            $dlog['toname'] = self::$_shop['name'];
                            $dlog['issub'] = 1;
                            $dlog['ctime'] = time();
                            $rdlog = self::$_fxlog->save($dlog);
                        }
                    }

                    $tourl = self::$_url . '/App/Shop/index/ppid/' . $isold['id'] . '/';
                    $str = "<a href='" . $tourl . "'>" . htmlspecialchars_decode(self::$_set['wxsummary']) . "</a>";
                    // self::$_wx->text($str)->reply();
                } else {
                    $pid = 0;
                    $old = array();
                    if (!empty(self::$_revdata['Ticket'])) {
                        $ticket = self::$_revdata['Ticket'];
                        $old = self::$_ppvip->where(array("ticket" => $ticket))->find();
                        $pid = $old["id"];
                    }

                    $user = $this->apiClient(self::$_revdata['FromUserName']);
                    unset($user['groupid']);
                    if ($user) {
                        //新用户注册政策
                        $vipset = model('Vip_set')->find();
                        $user['score'] = $vipset['reg_score'];
                        $user['exp'] = $vipset['reg_exp'];
                        $user['cur_exp'] = $vipset['reg_exp'];
                        //$level=$this->getLevel($user['exp']);报错
                        $user['levelid'] = 1;
                        //追入首次时间和更新时间
                        $user['ctime'] = $user['cctime'] = time();

                        //系统追入path 追入员工
                        if ($old['id']) {
                            $user['pid'] = $old['id'];
                            $user['path'] = $old['path'] . '-' . $old['id'];
                            $user['plv'] = $old['plv'] + 1;
                            $user['employee'] = $old['employee'];
                        } else {
                            $user['pid'] = 0;
                            $user['path'] = 0;
                            $user['plv'] = 1;
                            $user['employee'] = model('Employee')->randomEmployee();
                        }
                        $ff = self::$_ppvip->where(array("openid" => $openid))->find();
                        if($ff){
                            $str = "<a href='" . $tourl . "'>" . htmlspecialchars_decode(self::$_set['wxsummary']) . "</a>";
                            $this->subscribeReturn($str);
                        }
                        $revip = self::$_ppvip->save($user);
                        if ($revip) {
                            //赠送操作
                            if ($vipset['isgift']) {
                                $gift = explode(",", $vipset['gift_detail']);
                                $cardnopwd = $this->getCardNoPwd();
                                $data_card['type'] = $gift[0];
                                $data_card['vipid'] = $revip;
                                $data_card['money'] = $gift[1];
                                $data_card['usemoney'] = $gift[3];
                                $data_card['cardno'] = $cardnopwd['no'];
                                $data_card['cardpwd'] = $cardnopwd['pwd'];
                                $data_card['status'] = 1;
                                $data_card['stime'] = $data_card['ctime'] = time();
                                $data_card['etime'] = time() + $gift[2] * 24 * 60 * 60;
                                $rcaSrd = model('Vip_card')->save($data_card);
                            }
                            //发送注册通知消息
                            //记录日志
                            $data_log['ip'] = 'wechat';    //源自微信注册
                            $data_log['vipid'] = $revip;
                            $data_log['ctime'] = time();
                            $data_log['openid'] = $user['openid'];
                            $data_log['nickname'] = $user['nickname'];
                            $data_log['event'] = "会员注册";
                            $data_log['score'] = $user['score'];
                            $data_log['exp'] = $user['exp'];
                            $data_log['type'] = 4;
                            $rlog = model('Vip_log')->save($data_log);
                        }
                        //追入新用户关注日志
                        $dlog['ppid'] = 0;
                        $dlog['from'] = $revip;
                        $dlog['fromname'] = $user['nickname'];
                        $dlog['to'] = 0;
                        $dlog['toname'] = self::$_shop['name'];
                        $dlog['issub'] = 1;
                        $dlog['ctime'] = time();
                        $rdlog = self::$_fxlog->save($dlog);

                        //处理父亲
                        $mvip = self::$_ppvip;
                        $old = $mvip->where(array('id' => $pid))->find();
                        if ($old) {
                            $tj_score = self::$WAP['vipset']['tj_score'];
                            $tj_exp = self::$WAP['vipset']['tj_exp'];
                            $tj_money = self::$WAP['vipset']['tj_money'];
                            if ($tj_score || $tj_exp || $tj_money) {
                                $msg = "推荐新用户奖励：<br>新用户：" . $user['nickname'] . "<br>奖励内容：<br>";
                                $mglog = "获得新用户注册奖励:";
                                if ($tj_score) {
                                    $old['score'] = $old['score'] + $tj_score;
                                    $msg = $msg . $tj_score . "个积分<br>";
                                    $mglog = $mglog . $tj_score . "个积分；";
                                }
                                if ($tj_exp) {
                                    $old['exp'] = $old['exp'] + $tj_exp;
                                    $msg = $msg . $tj_exp . "点经验<br>";
                                    $mglog = $mglog . $tj_exp . "点经验；";
                                }
                                if ($tj_money) {
                                    $old['money'] = $old['money'] + $tj_money;
                                    $msg = $msg . $tj_money . "元余额<br>";
                                    $mglog = $mglog . $tj_money . "元余额；";
                                }
                                $msg = $msg . "此奖励已自动打入您的帐户！感谢您的支持！";
                                $rold = $mvip->save($old);
                                if (FALSE !== $rold) {
                                    $data_msg['pids'] = $old['id'];
                                    $data_msg['title'] = "你获得一份推荐奖励！";
                                    $data_msg['content'] = $msg;
                                    $data_msg['ctime'] = time();
                                    $rmsg = model('Vip_message')->save($data_msg);
                                    $data_mglog['vipid'] = $old['id'];
                                    $data_mglog['nickname'] = $old['nickname'];
                                    $data_mglog['xxnickname'] = $user['nickname'];
                                    $data_mglog['msg'] = $mglog;
                                    $data_mglog['ctime'] = time();
                                    $rmglog = model('Fx_log_tj')->save($data_mglog);
                                }
                            }

                            //三层上线追溯统计
                            // 三层上线追溯客服接口
                            $old['total_xxlink'] = $old['total_xxlink'] + 1;
                            $r1 = $mvip->save($old);
                            // 上下级自定义及Wechat配置
                            // $customerdown = model('Wx_customer')->where(array('type'=>'down'))->find();
                            $customerup = model('Wx_customer')->where(array('type' => 'up'))->find();
                            $shopset = model('Shop_set')->find();

                            // 发送信息给自己===============
                            //$msg = array();
                            //$msg['touser'] = $vip['openid'];
                            //$msg['msgtype'] = 'text';
                            //$str = $customerdown['value'];
                            //$msg['text'] = array('content'=>$str);
                            //$ree = $wx->sendCustomMessage($msg);
                            // 发送消息完成============
                            // 发送信息给父级===============
                            $msg = array();
                            $msg['touser'] = $old['openid'];
                            $msg['msgtype'] = 'text';
                            $str = "[" . $user['nickname'] . "]通过您的推广，成为了您的[" . $shopset['fx1name'] . "]，" . $customerup['value'];
                            $msg['text'] = array('content' => $str);
                            $ree = self::$_wx->sendCustomMessage($msg);
                            // 发送消息完成============
                            if ($old['pid']) {
                                $oldold = $mvip->where('id=' . $old['pid'])->find();
                                $oldold['total_xxlink'] = $oldold['total_xxlink'] + 1;
                                $r2 = $mvip->save($oldold);
                                // 发送信息给父级的父级===============
                                $msg = array();
                                $msg['touser'] = $oldold['openid'];
                                $msg['msgtype'] = 'text';
                                $str = "[" . $user['nickname'] . "]通过您的推广，成为了您的[" . $shopset['fx2name'] . "]，" . $customerup['value'];
                                $msg['text'] = array('content' => $str);
                                $ree = self::$_wx->sendCustomMessage($msg);
                                // 发送消息完成============
                                if ($oldold['pid']) {
                                    $oldoldold = $mvip->where('id=' . $oldold['pid'])->find();
                                    $oldoldold['total_xxlink'] = $oldoldold['total_xxlink'] + 1;
                                    $r3 = $mvip->save($oldoldold);
                                    // 发送信息给父级的父级的父级===============
                                    $msg = array();
                                    $msg['touser'] = $oldoldold['openid'];
                                    $msg['msgtype'] = 'text';
                                    $str = "[" . $user['nickname'] . "]通过您的推广，成为了您的[" . $shopset['fx3name'] . "]，" . $customerup['value'];
                                    $msg['text'] = array('content' => $str);
                                    $ree = self::$_wx->sendCustomMessage($msg);
                                    // 发送消息完成============
                                }
                            }
                            // 上报员工	向员工发送信息
                            $employee = model('Employee')->where(array('id' => $old['employee']))->find();
                            if ($old['employee'] && $employee && $employee['vipid']) {
                                $customeremp = model('Wx_customer')->where(array('type' => 'emp'))->find();
                                $empvip = $mvip->where(array('id' => $employee['vipid']))->find();
                                if ($empvip) {
                                    $msg = array();
                                    $msg['touser'] = $empvip['openid'];
                                    $msg['msgtype'] = 'text';
                                    $str = "[" . $user['nickname'] . "]通过您的推广，成为了您的[" . $shopset['fxname'] . "]，" . $customeremp['value'];
                                    $msg['text'] = array('content' => $str);
                                    $ree = self::$_wx->sendCustomMessage($msg);
                                }
                            }

                        }

                        $tourl = self::$_url . '/App/Shop/index/ppid/' . $revip . '/';
                        $str = "<a href='" . $tourl . "'>" . htmlspecialchars_decode(self::$_set['wxsummary']) . "</a>";
                    } else {
                        $tourl = self::$_url . '/App/Shop/index/';
                        $str = "<a href='" . $tourl . "'>" . htmlspecialchars_decode(self::$_set['wxsummary']) . "</a>";
                    }
                  file_put_contents('./Data/app_debug.txt', '收到请求:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $user['errmsg'] . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
             
                }
                $this->subscribeReturn($str);
                break;
                //取消关注事件
            case
                'unsubscribe':
                //更新库内的用户关注状态字段
                $map['openid'] = self::$_revdata['FromUserName'];
                $old = self::$_ppvip->where($map)->find();
                if ($old) {
                    $rold = self::$_ppvip->where($map)->setField('subscribe', 0);
                    if ($old['ppid']) {
                        $fxs = self::$_fx->where('id=' . $old['ppid'])->find();
                        if ($fxs) {
                            $dlog['ppid'] = $old['ppid'];
                            $dlog['from'] = $old['id'];
                            $dlog['fromname'] = $old['nickname'];
                            $dlog['to'] = $fxs['id'];
                            $dlog['toname'] = $fxs['nickname'];
                            $dlog['issub'] = 0;
                            $dlog['ctime'] = time();
                            $rdlog = self::$_fxlog->save($dlog);
                            $rfxs = self::$_fx->where('id=' . $old['ppid'])->setInc('total_xxunsub', 1);    //下线累计取消关注
                        }
                    } else {
                        $dlog['ppid'] = 0;
                        $dlog['from'] = $old['id'];
                        $dlog['fromname'] = $old['nickname'];
                        $dlog['to'] = 0;
                        $dlog['toname'] = self::$_shop['name'];
                        $dlog['issub'] = 0;
                        $dlog['ctime'] = time();
                        $rdlog = self::$_fxlog->save($dlog);
                    }
                }
                break;
            //自定义菜单点击事件
            case 'CLICK':
                $key = self::$_revdata['EventKey'];
                //self::$_wx->text('菜单点击拦截'.self::$_revdata['EventKey'].'!')->reply();
                switch ($key) {
                    case '#sy':
                     break;
                }
                //不存在拦截命令,走关键词流程
                $this->checkKeyword($key);

                break;

        }
    }

    /*高级调试模式 by App
    $type=调试命令
    $App-openid:获取用户openid
     */
    public function toApp($type)
    {
        $title = "App管理员模式：\n命令：" . $type . "\n结果：\n";

        switch ($type) {
            case 'dkf':
                $str = "人工客服接入！";
                self::$_wx->dkf($str)->reply();
                break;
            case 'openid':
                self::$_wx->text($title . self::$_revdata['FromUserName'])->reply();
                break;
            default:
                self::$_wx->text("App:未知命令")->reply();
        }

    }

    /*自定义关键词模式 by App
    $ruser=关键词记录
     */
    public function toKeyUser($ruser)
    {
        $type = $ruser['type'];
        switch ($type) {
            //文本
            case "1":
                self::$_wx->text($ruser['summary'])->reply();
                break;
            //单图文
            case "2":
                $news[0]['Title'] = $ruser['name'];
                $news[0]['Description'] = $ruser['summary'];
                $img = $this->getPic($ruser['pic']);
                $news[0]['PicUrl'] = $img['imgurl'];
                $news[0]['Url'] = $ruser['url'];
                self::$_wx->news($news)->reply();
                break;
            //多图文
            case "3":
                $pagelist = model('Wx_keyword_img')->where(array('kid' => $ruser['id']))->order('sorts desc')->select();
                $news = array();
                foreach ($pagelist as $k => $v) {
                    $news[$k]['Title'] = $v['name'];
                    $news[$k]['Description'] = $v['summary'];
                    $img = $this->getPic($v['pic']);
                    $news[$k]['PicUrl'] = $img['imgurl'];
                    $news[$k]['Url'] = $v['url'];
                }
                self::$_wx->news($news)->reply();
                break;
            default:
                self::$_wx->text("未知类型的关键词，请联系客服！")->reply();
                break;
        }
    }

    /*未知关键词匹配 by App
     */
     public function toKeyUnknow($key)
    {
        // self::$_wx->text("未找到此关键词匹配！")->reply();
        //或取所有客服
        $kfList=self::$_wx->getCustomServiceKFlist();
        $num=rand(0,count($kfList['kf_list']));
        
        //向客服传递的消息
        //获取用户openid；
        $map['openid'] = self::$_revdata['FromUserName'];
        $vip = self::$_ppvip->where($map)->find();
        $openid=$vip['openid'];
        
        self::$_wx->createKFSession($openid,$num,$key);
       
        // file_put_contents('vip.txt',json_encode($vip));
    }

    /*具体微管网推送方式 by App
    $type=对应应用的类型
    $imglist=true/false 是否以多条返回/最多10条
     */
    public function toWgw($type, $imglist)
    {
        $wgw = F(self::$_uid . "/config/wgw_set"); //微官网设置缓存
        switch ($type) {
            case 'index':
                //准备各项参数
                $title = $wgw['title'] ? $wgw['title'] : '欢迎访问' . self::$_userinfo['wxname'];
                $summary = $wgw['summary'];
                $picid = $wgw['pic'];
                $picurl = $picid ? $this->getPic($picid) : false;
                //封装图文信息
                $news[0]['Title'] = $title;
                $news[0]['Description'] = $summary;
                $news[0]['PicUrl'] = $picurl['imgurl'] ? $picurl['imgurl'] : '#';
                $news[0]['Url'] = self::$_url . '/App/Wgw/Index/uid/' . self::$_uid;
                //推送图文信息
                self::$_wx->news($news)->reply();
                break;
        }
    }

    /*将图文信息封装为二维数组 by App
    $array(Title,Description,PicUrl,Url),$return=false
    Return:新闻数组/或直接推送
     */
    public function makeNews($array, $return = false)
    {
        if (!$array) {
            die('no items!');
        }
        $news[0]['Title'] = $array[0];
        $news[0]['Description'] = $array[1];
        $news[0]['PicUrl'] = $array[2];
        $news[0]['Url'] = $array[3];
        if ($return) {
            return $news;
        } else {
            self::$_wx->news($news)->reply();
        }
    }

    /*获取单张图片 by App
    return
     */
    public function getPic($url)
    {
        $list['imgurl'] = self::$_url . $url;
        return $list ? $list : false;
    }
    //根据微信接口获取用户信息
    //return array/false 用户信息/未获取。
    public function apiClient($openid)
    {
        $user = self::$_wx->getUserInfo($openid);
        return $user ? $user : FALSE;
    }

    /*认证服务号微信用户资料更新 by App
    return
     */
    public function updateUser($openid)
    {
        $old = self::$_ppvip->where(array('openid' => $openid))->find();
        if ($old) { 
            if ((time() - $old['cctime']) > 86400) {
                $user = self::$_wx->getUserInfo($openid);
                //当成功拉去数据后
                if ($user) {
                    $user['cctime'] = time();
                    unset($user['groupid']);
                    $re = self::$_ppvip->where(array('id' => $old['id']))->save($user);
                } else {
                    $str = '更新用户资料失败，用户为：' . $openid;
                    file_put_contents('./Data/app_fail.txt', '微信接口失败:' . date('Y-m-d H:i:s') . PHP_EOL . '通知信息:' . $str . PHP_EOL . PHP_EOL . PHP_EOL, FILE_APPEND);
                }
            } else {  
                //1天内，直接保存最后的交互时间
                $old['cctime'] = time();
                $re = self::$_ppvip->save($old);
            }
        }
        return true;

    }

    ///////////////////增值方法//////////////////////////
    public function getlevel($exp)
    {
        $data = model('Vip_level')->order('exp')->select();
        if ($data) {
            $level = array();
            foreach ($data as $k => $v) {
                if ($k + 1 == count($data)) {
                    if ($exp >= $data[$k]['exp']) {
                        $level['levelid'] = $data[$k]['id'];
                        $level['levelname'] = $data[$k]['name'];
                    }
                } else {
                    if ($exp >= $data[$k]['exp'] && $exp < $data[$k + 1]['exp']) {
                        $level['levelid'] = $data[$k]['id'];
                        $level['levelname'] = $data[$k]['name'];
                    }
                }
            }
        } else {
            return false;
        }
        return $level;
    }

    public function getCardNoPwd()
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

    // 获取头像函数
    function getRemoteHeadImage($headimgurl)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_URL, $headimgurl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        $headimg = curl_exec($ch);
        curl_close($ch);
        return $headimg;
    }

    public function getQRCode($id, $openid)
    {
        $ticket = self::$_wx->getQRCode($id, 1);

        self::$_ppvip->where(array("id" => $id))->save(array("ticket" => $ticket["ticket"]));
        $qrUrl = self::$_wx->getQRUrl($ticket["ticket"]);

        $data = file_get_contents($qrUrl);
        file_put_contents('./QRcode/qrcode/' . $openid . '.png', $data);
    }

    // 创建二维码
    function createQrcode($id, $openid)
    {
        if ($id == 0 || $openid == '') {
            return false;
        }
        if (!file_exists('./QRcode/qrcode/' . $openid . '.png')) {
            //二维码进入系统
//            $url = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/App/Shop/index/ppid/' . $id;
//            \Util\QRcode::png($url, './QRcode/qrcode/' . $openid . '.png', 'L', 6, 2);

            //二维码进入公众号
            $this->getQRCode($id, $openid);
        }
        $qrcode = imagecreatefromstring(file_get_contents('./QRcode/qrcode/' . $openid . '.png'));
        return $qrcode;
    }

    // 创建二维码
    function createEmployeeQrcode($id, $openid)
    {
        if ($id == 0 || $openid == '') {
            return false;
        }
        if (!file_exists('./QRcode/qrcode/' . $id . "employee" . $openid . '.png')) {
            $url = 'http://' . $_SERVER['HTTP_HOST'] . __ROOT__ . '/App/Shop/index/ppid/' . $id;
            \wx\QRcode::png($url, './QRcode/qrcode/' . $id . "employee" . $openid . '.png', 'L', 6, 2);
        }
        $qrcode = imagecreatefromstring(file_get_contents('./QRcode/qrcode/' . $id . "employee" . $openid . '.png'));
        return $qrcode;
    }

    // 创建背景
    function createQrcodeBg()
    {
        $autoset = model('Autoset')->find();
        if (!file_exists('./' . $autoset['qrcode_background'])) {
            $background = imagecreatefromstring(file_get_contents('./QRcode/background/default.jpg'));
        } else {
            $background = imagecreatefromstring(file_get_contents('./' . $autoset['qrcode_background']));
        }
        return $background;
    }

    // 创建背景
    function createQrcodeBgEmp()
    {
        $autoset = model('Autoset')->find();
        if (!file_exists('./' . $autoset['qrcode_emp_background'])) {
            $background = imagecreatefromstring(file_get_contents('./QRcode/background/default.jpg'));
        } else {
            $background = imagecreatefromstring(file_get_contents('./' . $autoset['qrcode_emp_background']));
        }
        return $background;
    }

    // 关注时返回信息
    function subscribeReturn($msg)
    {
        //关注返回图文消息
        $ruser['name'] = "青梅煮酒，共论英雄";
        $ruser['summary'] = "青梅煮酒，与你共论英雄\n很高兴，终于等到你\n青梅世界有你更加精彩...";
        $ruser['type'] = 2;
        $ruser['pic'] = '/uploads/default/subscribe.jpg';
        $ruser['url'] = 'http://www.lsiten.cn';
        $this->toKeyUser($ruser);
        exit();
        //关注返回图文消息
        $temp = getcwd() . $this->getSubscribePic(self::$_set['wxpicture']);
        $switchs = file_exists($temp);
        if (self::$_set['wxswitch'] == '0' || !$switchs) {
            self::$_wx->text($msg)->reply();
        } else {
            if (class_exists('\CURLFile')) {
                $data = array('media' => new \CURLFile(realpath($temp)));
            } else {
                $data = array('media' => '@' . realpath($temp));
            }
            $uploadresult = self::$_wx->uploadMedia($data, 'image');
            self::$_wx->image($uploadresult['media_id'])->reply();
        }
    }

    // 获取单张图片
    function getSubscribePic($pic)
    {
        return $pic ? $pic : '';
    }

}
