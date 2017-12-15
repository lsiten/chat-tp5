<?php
namespace app\admin\controller;
use app\admin\model\VipCard;
class Vip extends Base
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
    }
    
    //返现
    public function cashback()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '财务管理',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '返现管理',
                'url' => U('Admin/Vip/cashback'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        // $status = I('status');
        // $this->assign('status', $status);
        // if ($status || $status == '0') {
        //     $map['status'] = $status;
        // }
        // $this->assign('status', $status);
        //绑定搜索条件与分页
        $m = M('Cashback');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('order_id') ? I('order_id') : '';
        if ($name) {
            //提现人姓名
            $map['order_id'] = $name;
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cashback = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '会员返现订单', 'App-search');
        $this->assign('cashback', $cashback);
        $this->display();
    }

    public function cashbackExport()
    {
        $id = I('id');
        $data = M('Cashback')->where($map)->select();
        foreach ($data as $k => $v) {
            switch ($v['status']) {
                case 0:
                    $data[$k]['status'] = "正在进行中...";
                    break;
                case 1:
                    $data[$k]['status'] = "返现完成";
                    break;
            }
            $data[$k]['lasttime'] = date('Y-m-d H:i:s', $v['lasttime']);
        }
        $title = array('ID', '会员ID', '返现金额', '订单号', '每天返现金额', '上一次返现时间', '已经返现天数', '返现状态','时间');
        $this->exportexcel($data, $title, $tt . '订单' . date('Y-m-d H:i:s', time()));
    }

    public function set()
    {
        $m = M('vip_set');
        $data = $m->find();
        if (IS_POST) {
            $post = I('post.');
            if ($post['isgift'] == 1) {
                $post['gift_detail'] = $post['gift_type'] . "," . $post['gift_money'] . "," . $post['gift_days'] . "," . $post['gift_usemoney'];
            }
            unset($post['gift_type']);
            unset($post['gift_money']);
            unset($post['gift_days']);
            unset($post['gift_usemoney']);
            $r = $data ? $m->where('id=' . $data['id'])->save($post) : $m->add($post);
            if (FALSE !== $r) {
                $info['status'] = 1;
                $info['msg'] = '设置成功！';
            } else {
                $info['status'] = 0;
                $info['msg'] = '设置失败！';
            }
            $this->ajaxReturn($info, "json");
        } else {
            //设置面包导航，主加载器请配置
            $bread = array(
                '0' => array(
                    'name' => '会员中心',
                    'url' => U('Admin/Vip/#'),
                ),
                '1' => array(
                    'name' => '会员设置',
                    'url' => U('Admin/Vip/set'),
                ),
            );
            $this->assign('breadhtml', $this->getBread($bread));
            $data = $m->find();
            if ($data['isgift'] == 1) {
                $gift = explode(",", $data['gift_detail']);
                $data['gift_type'] = $gift[0];
                $data['gift_money'] = $gift[1];
                $data['gift_days'] = $gift[2];
                $data['gift_usemoney'] = $gift[3];
            }
            $this->assign('data', $data);
            $this->display();
        }
    }

    // 获取层级
    public function vipTree()
    {
        $mvip = M('vip');
        $data = I('data');
        $id = I('id');
        $str = '<br>';
        $vipids = explode('-', $data);
        $vip = $mvip->where('id=' . $id)->find();
        if (count($vipids) <= 1) {
            $str .= "<div style='float:left;position:absolute'><img style='width:30px' src='" . $vip['headimgurl'] . "'/>" . "&nbsp&nbsp&nbsp&nbsp" . $vip['nickname'] . "(当前用户)" . "</div>";
        } else {
            foreach ($vipids as $k => $v) {
                # code...
                if ($k == 0) {
                } else {
                    $temp = $mvip->where('id=' . $v)->find();
                    $str .= "<div style='float:left;position:absolute'><img style='width:30px' src='" . $temp['headimgurl'] . "'/>" . "&nbsp&nbsp&nbsp&nbsp" . $temp['nickname'] . "<br>&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp↑<br></div>";
                    $str .= "<br><br><br>";
                }
            }
            $str .= "<div style='float:left;position:absolute'><img style='width:30px' src='" . $vip['headimgurl'] . "'/>" . "&nbsp&nbsp&nbsp&nbsp" . $vip['nickname'] . "(当前用户)" . "</div>";
        }

        $this->ajaxReturn(array('msg' => $str), "json");
    }

    // 层级树
    public function vipTrack()
    {
        // 获取模型
        $dvip = D('Vip');
        if (IS_POST) {
            $vipid = I('vipid');
            $cache = D('Vip')->getChildren($vipid);
            $str = '<ul>';
            // 组装返回数据
            if (count($cache) > 0) {
                foreach ($cache as $k => $vip) {
                    if ($vip['type'] == 1) {
                        $str .= '<li id="node' . $vip['id'] . '" data-id="' . $vip['id'] . '" class="parent">';
                        $str .= '<span onclick="javascript:pathopen(this);"><i class="glyphicon glyphicon-plus"></i> ' . $vip['nickname'] . '</span> <a href="javascript:;"></a><span class="numPer redCol">' . $vip['count1'] . '</span><span class="numPer blueCol">' . $vip['count2'] . '</span><span class="numPer greenCol">' . $vip['count3'] . '</span><span class="numPer rouCol">' . $vip['ocount'] . '单：共计' . $vip['osum'] . '</span><span class="numPer eyeCol" data-id="' . $vip['id'] . '" onclick="userInfo(this)"><i class="glyphicon glyphicon-eye-open"></i></span>';
                    } else {
                        $str .= '<li id="node' . $vip['id'] . '" data-id="' . $vip['id'] . '" class="leaf">';
                        $str .= '<span><i class="glyphicon glyphicon-leaf"></i> ' . $vip['nickname'] . '</span> <a href="javascript:;"></a><span class="numPer rouCol" style="color:black">' . $vip['ocount'] . '单：共计' . $vip['osum'] . '</span><span class="numPer eyeCol eyeColCol" data-id="' . $vip['id'] . '" onclick="userInfo(this)"><i class="glyphicon glyphicon-eye-open"></i></span>';
                    }
                    $str .= '</li>';
                }
            }
            $str .= '</ul>';
            $this->ajaxReturn(array('msg' => $str, 'id' => $vipid), "json");
            exit();
        }
        $top = $dvip->getChildren();
        $this->assign('cache', $top);
        $this->display();
    }

    // 获取个人信息
    public function vipInfo()
    {
        if (IS_AJAX) {
            $id = I('id');
            $mvip = D('Vip');
            $str = $mvip->getVipForMessage($id);
            if ($str) {
                $this->ajaxReturn(array('msg' => $str), "json");
            } else {
                $this->ajaxReturn(array('msg' => "通信失败"), "json");
            }
        }
    }

    // 设置
    public function vipReborn()
    {
        if (IS_AJAX) {
            $dvip = D('Vip');
            $id = I('id');
            $ppid = I('ppid');

            if ($ppid == $id) {
                $info['status'] = 0;
                $info['msg'] = "调配失败";
            }

            $re = $dvip->vipReborn($id, $ppid);
            if ($re) {
                $info['status'] = 1;
                $info['msg'] = "调配成功";
            } else {
                $info['status'] = 0;
                $info['msg'] = "调配失败";
            }
            $this->ajaxReturn($info);
        }
    }

    // Vip未分配会员列表
    public function vipRebornList()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '可调配会员',
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));

        // 员工介入
        $temp = M('employee')->select();
        $employee = array();
        foreach ($temp as $k => $v) {
            $employee[$v['id']] = $v;
        }

        //绑定搜索条件与分页
        $m = M('vip');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['nickname|mobile'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $map['plv'] = 1;
        $map['pid'] = 0;
        $map['isfx'] = 0;
        $map['total_xxlink'] = 0;
        //$map['employee']=0;
        $cache = $m->where($map)->page($p, $psize)->select();
        foreach ($cache as $k => $v) {
            $cache[$k]['levelname'] = M('vip_level')->where('id=' . $cache[$k]['levelid'])->getField('name');
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


    // 设置
    public function vipAlloc()
    {
        if (IS_AJAX) {
            $dvip = D('Vip');
            $id = I('vipid');
            $eid = I('empid');
            $employee = M('employee')->where(array('id' => $eid))->find();
            $vip = M('vip')->where(array('id' => $id, 'plv' => 1))->find();

            if ($employee && $vip) {
                $re = $dvip->setEmployee($id, $eid);
                if ($re) {
                    $info['status'] = 1;
                    $info['msg'] = "员工账户绑定成功";
                } else {
                    $info['status'] = 0;
                    $info['msg'] = "员工账户绑定失败";
                }
                //$info['msg'] = json_encode($re);

            } else {
                $info['status'] = 0;
                $info['msg'] = "员工账户不存在";
            }
            $this->ajaxReturn($info);

        }
    }

    // Vip未分配会员列表
    public function vipAllocList()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员分配中心',
                'url' => U('Admin/Vip/#'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        // 员工介入
        $temp = M('employee')->select();
        $employee = array();
        foreach ($temp as $k => $v) {
            $employee[$v['id']] = $v;
        }
        //绑定搜索条件与分页
        $m = M('vip');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['nickname|mobile'] = array('like', "%$search%");
            //$map['mobile'] = array('like', "%$search%");
            //$map['_logic'] = 'OR';
            $this->assign('search', $search);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $map['plv'] = 1;
        //$map['employee']=0;
        $cache = $m->where($map)->page($p, $psize)->select();
        foreach ($cache as $k => $v) {
            $cache[$k]['levelname'] = M('vip_level')->where('id=' . $cache[$k]['levelid'])->getField('name');
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

    // VIP列表
    public function vipList()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '会员列表',
                'url' => U('Admin/Vip/vipList'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        // 员工介入
        $temp = M('employee')->select();
        $employee = array();
        foreach ($temp as $k => $v) {
            $employee[$v['id']] = $v;
        }
        //绑定搜索条件与分页
        $m = M('vip');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        $plv = I('plv') ? I('plv') : 0;
        if ($search) {
            $map['nickname|mobile'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        if ($plv) {
            $map['plv'] = $plv;
            $this->assign('plv', $plv);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        foreach ($cache as $k => $v) {
            $cache[$k]['levelname'] = M('vip_level')->where('id=' . $cache[$k]['levelid'])->getField('name');
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

    //CMS后台商品设置
    public function vipSet()
    {
        $id = I('id');
        $m = M('Vip');
        //dump($m);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '会员列表',
                'url' => U('Admin/Vip/vipList'),
            ),
            '1' => array(
                'name' => '会员编辑',
                'url' => U('Admin/Vip/vipSet', array('id' => $id)),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //die('aa');
            $data = I('post.');
            if ($id) {
                $re = $m->save($data);
                if (FALSE !== $re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $info['status'] = 0;
                $info['msg'] = '未获取会员ID！';
            }
            $this->ajaxReturn($info);
        }

        //处理编辑界面
        if ($id) {
            $cache = $m->where('id=' . $id)->find();
            $this->assign('cache', $cache);
        } else {
            $info['status'] = 0;
            $info['msg'] = '未获取会员ID！';
            $this->ajaxReturn($info);
        }
        $this->display();
    }

    //CMS后台商品设置
    public function vipFxtj()
    {
        header("Content-type: text/html; charset=utf-8");
        $id = I('id');
        $mvip = M('Vip');
        //dump($m);
        //设置面包导航，主加载器请配置
        //		$bread=array(
        //			'0'=>array(
        //				'name'=>'会员中心',
        //				'url'=>U('Admin/Vip/#')
        //			),
        //			'1'=>array(
        //				'name'=>'会员列表',
        //				'url'=>U('Admin/Vip/vipList')
        //			),
        //			'1'=>array(
        //				'name'=>'会员编辑',
        //				'url'=>U('Admin/Vip/vipSet',array('id'=>$id))
        //			)
        //		);
        //		$this->assign('breadhtml',$this->getBread($bread));

        $vip = $mvip->where('id=' . $id)->find();
        if (!$vip) {
            $this->die('不存在此用户！');
        }
        echo '会员分销统计预估开始：<br><br>';
        echo '<br><br>*********************************************<br><br>';
        echo '会员名：' . $vip['nickname'] . '<br>';
        echo '会员层级：' . $vip['plv'] . '<br>';
        echo '会员路由：' . $vip['path'] . '<br>';
        echo '会员余额：' . $vip['money'] . '<br>';
        echo '<br><br>*********************************************<br><br>';
        echo '第一步：取出3层下线所有用户<br><br>';
        $maxlv = $vip['plv'] + 3;
        $likepath = $vip['path'] . '-' . $vip['id'];
        echo '层级条件：最大层级不超过' . $maxlv . '<br>';
        echo '路由条件：' . $likepath . '<br>';
        //两次模糊查询
        //1:取出第一层，2:取出其他层
        $firstlv = $vip['plv'] + 1;
        $firstpath = $likepath;
        $mapfirst['plv'] = $firstlv;
        $mapfirst['path'] = $firstpath;
        $firstsub = $mvip->field('id,plv,path,nickname')->where($mapfirst)->select();
        if ($firstsub) {
            //模糊查询第二层和第三层
            $maplike['plv'] = array('gt', $firstlv);
            $maplike['plv'] = array('elt', $maxlv);
            $maplike['path'] = array('like', $likepath . '-%');
            $sesendsub = $mvip->field('id,plv,path,nickname')->where($maplike)->select();
            //dump($firstsub);
            //dump($sesendsub);
            //合并两个数组
            if ($sesendsub) {
                $sub = array_merge($firstsub, $sesendsub);
            } else {
                $sub = $firstsub;
            }
            echo '3层下线总数：' . count($sub) . ' 人<br>';
            echo '列出所有下线会员：<br>';
            dump($sub);
            echo '将下线会员按照层级与会员ID重新整理：<br>';
            $subarr = array();
            foreach ($sub as $v) {
                //按层级分组
                $subarr[$v['plv']] = $subarr[$v['plv']] . $v['id'] . ',';
                //array_push($subarr[$v['plv']],$v['id']);
            }
            dump($subarr);
            echo '再次整理下线分层数组：<br>';
            $subarr = array_values($subarr);
            dump($subarr);
            echo '<br><br>*********************************************<br><br>';
            echo '第二步：取出系统佣金比例设置<br><br>';
            $shopset = M('Shop_set')->find();
            $morder = M('Shop_order');
            $fx1rate = $shopset['fx1rate'];
            $fx2rate = $shopset['fx2rate'];
            $fx3rate = $shopset['fx3rate'];
            echo '第一层分销比例：' . $fx1rate . '%<br>';
            echo '第二层分销比例：' . $fx2rate . '%<br>';
            echo '第三层分销比例：' . $fx3rate . '%<br>';
            echo '<br><br>*********************************************<br><br>';
            echo '第三步：逐级分析算出分销佣金<br><br>';
            if ($fx1rate && $subarr[0]) {
                $tmprate = $fx1rate;
                $tmplv = $data['plv'] + 1;
                $maporder['ispay'] = 1;
                $maporder['status'] = array('in', array('2', '3'));
                $maporder['vipid'] = array('in', in_parse_str($subarr[0]));
                echo '第一层分销佣金统计开始：<br>';
                echo '列出订单检索条件：<br>';
                echo '订单支付条件：已支付<br>';
                echo '订单状态条件：已支付或已发货<br>';
                echo '订单购买会员ID：' . $subarr[0] . '<br><br>';
                $tmpod = $morder->field('id,oid,vipid,vipname,payprice,paytime')->where($maporder)->select();
                if ($tmpod) {
                    $tmpodtotal = count($tmpod);
                    echo '根据条件检索出：' . $tmpodtotal . '个订单，列出所有结果<br>';
                    dump($tmpod);
                } else {
                    echo '没有第一层的订单，支付总额为0<br>';
                }

                $tmptotal = $morder->where($maporder)->sum('payprice');
                if (!$tmptotal) {
                    $tmptotal = 0;
                }
                echo '第一层会员所有订单合计支付总额：' . $tmptotal . '元<br>';
                $fx1total = $tmptotal * ($tmprate / 100);
                echo '第一层会员所有订单应贡献佣金[公式=支付总额*(第一层分销率/100)]：' . $fx1total . '元<br>';
                echo '第一层统计结束。<br><br>';
            } else {
                $fx1total = 0;
                echo '不存在第一层会员，该层分销佣金为0。<br><br>';
            }
            if ($fx2rate && $subarr[1]) {
                $tmprate = $fx2rate;
                $tmplv = $data['plv'] + 2;
                $maporder['ispay'] = 1;
                $maporder['status'] = array('in', array('2', '3'));
                $maporder['vipid'] = array('in', in_parse_str($subarr[1]));
                echo '第二层分销佣金统计开始：<br>';
                echo '列出订单检索条件：<br>';
                echo '订单支付条件：已支付<br>';
                echo '订单状态条件：已支付或已发货<br>';
                echo '订单购买会员ID：' . $subarr[1] . '<br><br>';
                $tmpod = $morder->field('id,oid,vipid,vipname,payprice,paytime')->where($maporder)->select();
                if ($tmpod) {
                    $tmpodtotal = count($tmpod);
                    echo '根据条件检索出：' . $tmpodtotal . '个订单，列出所有结果<br>';
                    dump($tmpod);
                } else {
                    echo '没有第二层的订单，支付总额为0<br>';
                }

                $tmptotal = $morder->where($maporder)->sum('payprice');
                if (!$tmptotal) {
                    $tmptotal = 0;
                }
                echo '第二层会员所有订单合计支付总额：' . $tmptotal . '元<br>';
                $fx2total = $tmptotal * ($tmprate / 100);
                echo '第二层会员所有订单应贡献佣金[公式=支付总额*(第二层分销率/100)]：' . $fx2total . '元<br>';
                echo '第二层统计结束。<br><br>';
            } else {
                $fx2total = 0;
                echo '不存在第二层会员，该层分销佣金为0。<br><br>';
            }
            if ($fx3rate && $subarr[2]) {
                $tmprate = $fx3rate;
                $tmplv = $data['plv'] + 3;
                $maporder['ispay'] = 1;
                $maporder['status'] = array('in', array('2', '3'));
                $maporder['vipid'] = array('in', in_parse_str($subarr[2]));
                echo '第三层分销佣金统计开始：<br>';
                echo '列出订单检索条件：<br>';
                echo '订单支付条件：已支付<br>';
                echo '订单状态条件：已支付或已发货<br>';
                echo '订单购买会员ID：' . $subarr[2] . '<br><br>';
                $tmpod = $morder->field('id,oid,vipid,vipname,payprice,paytime')->where($maporder)->select();
                if ($tmpod) {
                    $tmpodtotal = count($tmpod);
                    echo '根据条件检索出：' . $tmpodtotal . '个订单，列出所有结果<br>';
                    dump($tmpod);
                } else {
                    echo '没有第三层的订单，支付总额为0<br>';
                }

                $tmptotal = $morder->where($maporder)->sum('payprice');
                if (!$tmptotal) {
                    $tmptotal = 0;
                }
                echo '第三层会员所有订单合计支付总额：' . $tmptotal . '元<br>';
                $fx3total = $tmptotal * ($tmprate / 100);
                echo '第三层会员所有订单应贡献佣金[公式=支付总额*(第三层分销率/100)]：' . $fx3total . '元<br>';
                echo '第三层统计结束。<br><br>';
            } else {
                $fx3total = 0;
                echo '不存在第三层会员，该层分销佣金为0。<br><br>';
            }
            $totalfxmoney = number_format(($fx1total + $fx2total + $fx3total), 2);
            echo '当前会员的代收佣金预估值为[公式=第一层贡献佣金+第二层贡献佣金+第三层贡献佣金，保留2位小数格式化处理]：' . $totalfxmoney . '<br><br>';
            echo '**********************本次分析结束！*****************';

        } else {
            echo '此会员没有下线成员，代收佣金为0，直接结束统计分析！';
        }

    }

    public function vipExport()
    {
        $id = I('id');
        if ($id) {
            $map['id'] = array('in', in_parse_str($id));
        }

        $data = M('Vip')->where($map)->select();
        foreach ($data as $k => $v) {
            unset($data[$k]['pid']);
            unset($data[$k]['path']);
            unset($data[$k]['password']);
            unset($data[$k]['cur_exp']);
            unset($data[$k]['levelid']);
            unset($data[$k]['language']);
            unset($data[$k]['headimgurl']);
            unset($data[$k]['status']);
            unset($data[$k]['sign']);
            unset($data[$k]['signtime']);
            unset($data[$k]['total_buy']);
            unset($data[$k]['total_yj']);
            $data[$k]['ctime'] = $v['ctime'] ? date('Y-m-d H:i:s', $v['ctime']) : '无';
            $data[$k]['cctime'] = $v['cctime'] ? date('Y-m-d H:i:s', $v['cctime']) : '无';
        }
        $title = array('会员ID', '会员层级', '真实电话', '真实姓名', 'E-mail', '金钱', '积分', '经验', 'openid', '微信昵称', '性别', '城市', '省份', '国家', '关注情况', '关注时间', '创建时间', '交互时间', '是否分销商', '是否正常', '历史佣金', '团队人数', '下线关注次数', '下线取消关注次数', '下线购买次数', '提现金额', '提现姓名', '提现电话', '提现银行', '提现分行', '提现银行所在地', '提现银行卡卡号');
        $this->exportexcel($data, $title, '会员数据' . date('Y-m-d H:i:s', time()));
    }

    public function message()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '消息管理',
                'url' => U('Admin/Vip/message'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('vip_message');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $search = I('search') ? I('search') : '';
        if ($search) {
            $map['title'] = array('like', "%$search%");
            $this->assign('search', $search);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->order('id desc')->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '消息管理', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    public function messageSet()
    {
        $id = I('id');
        $m = M('vip_message');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '消息管理',
                'url' => U('Admin/Vip/message'),
            ),
            '2' => array(
                'name' => '消息设置',
                'url' => $id ? U('Admin/Vip/messageSet', array('id' => $id)) : U('Admin/Vip/messageSet'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            $data = I('post.');
            $data['ctime'] = time();
            if ($id) {
                $re = $m->save($data);
                if (FALSE !== $re) {
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
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
        //处理编辑界面
        if ($id) {
            $cache = $m->where('id=' . $id)->find();
            $this->assign('cache', $cache);
        }
        if (I('pids')) {
            $cache['pids'] = I('pids');
            $this->assign('cache', $cache);
        }
        $this->display();
    }

    public function mailSet()
    {
        $pids = I('pids');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '会员列表',
                'url' => U('Admin/Vip/viplist'),
            ),
            '2' => array(
                'name' => '发送邮件',
                'url' => U('Admin/Vip/messageSet'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            $m = M('vip');
            $data = I('post.');
            $id_arr = explode(',', $data['pids']);
            foreach ($id_arr as $k => $v) {
                $mail_addr = $m->where('id=' . $v)->getField('email');
                if ($mail_addr != '') {
                    think_send_mail($mail_addr, '系统会员', $data['title'], $data['content']);
                }
            }

            $info['status'] = 1;
            $info['msg'] = ' 发送成功！';

            $this->ajaxReturn($info);
        }
        $this->assign('pids', $pids);
        $this->display();
    }

    public function messageDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = M('vip_message');
        if (!id) {
            $info['status'] = 0;
            $info['msg'] = 'ID不能为空!';
            $this->ajaxReturn($info);
        }
        $re = $m->delete($id);
        if ($re) {
            //删除消息浏览记录
            M('vip_log')->where('type=5 and opid in (' . $id . ')')->delete();
            $info['status'] = 1;
            $info['msg'] = '删除成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '删除失败!';
        }
        $this->ajaxReturn($info);
    }

    public function card()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (isset($param['searchText']) && !empty($param['searchText'])) {
                $where['cardno'] = ['like', '%' . $param['searchText'] . '%'];
            }
            if (isset($param['type']) && !empty($param['type'])) {
                $where['type'] = $param['type'];
            }
            //绑定搜索条件与分页
            $ads = model('vip_card');
            $return['total'] = $ads->where($where)->count(); //总数据
            $selectResult = $ads->where($where)->limit($offset,$limit)->order('id DESC')->select();

            foreach($selectResult as $key=>$vo){
                $selectResult[$key]['type_text'] = $vo['type']==1?"<div id='type".$vo['id']."'>充值卡</div>":"<div id='type".$vo['id']."'>代金券</div>";   
                $selectResult[$key]['checkbox'] = '<div class="checkbox" style="margin-bottom: 0px; margin-top: 0px;">';
                $selectResult[$key]['checkbox'] .='<label style="padding-left: 4px;"> <input name="checkvalue" type="checkbox" class="colored-blue App-check" value="'.$vo['id'].'">';
                $selectResult[$key]['checkbox'] .='<span class="text"></span>';
                $selectResult[$key]['checkbox'] .='</label> </div>';
                
                
                $selectResult[$key]['money_text'] = "<div id='money".$vo['id']."'>".$vo['money']."</div>";
                if($vo['stime'])
                {
                    $selectResult[$key]['time_text'] =date("Y-m-d",$vo['stime'])."-".date("Y-m-d",$vo['etime']);               
                } 
                else{
                    $selectResult[$key]['time_text'] = "";
                } 
                if($vo['usemoney'])
                {
                    $selectResult[$key]['usemoney_text'] ="满".$vo['usemoney']."元使用";               
                } 
                else{
                    $selectResult[$key]['usemoney_text'] = "";
                }               
                $selectResult[$key]['ctime_text'] = date("Y-m-d",$vo['ctime']);
                switch($vo['status'])
                {
                    case 0:
                        $selectResult[$key]['status_text'] = "<div id='status".$vo['id']."'>生成</div>";               
                    break;
                    case 1:
                        $selectResult[$key]['status_text'] = "<div id='status".$vo['id']."'>已发卡</div>";                                       
                    break;
                    case 2:
                        $selectResult[$key]['status_text'] = "<div id='status".$vo['id']."'>已使用</div>"; 
                        $selectResult[$key]['usetime_text'] =date("Y-m-d",$vo['usetime']);                                     
                    break;
                }           
                            
                $operate = [
                    '删除' => "javascript:cardDel('".$vo['id']."')"
                ];

                $selectResult[$key]['operate'] = showOperate($operate);

            }
            $return['rows'] = $selectResult;

            return json($return);
        }
        $type = input('param.type')?input('param.type'):2;
        $this->assign('type', $type);
        return $this->fetch();
    }

    public function cardadd(){
        if (request()->isPost()) {
            $keyword = new VipCard();
            //新增处理
            $params = input('post.');
            $flag = $keyword->insertData( $params );

            if( 1 != $flag['code'] ){
                return json( ['code' => -6, 'data' => '', 'msg' => '添加失败'] );
            }
            return json( ['code' => 1, 'data' => "", 'msg' => '添加成功'] );
        }else{
            return $this->fetch();
        }
    }

    public function cardDel()
    {
        $id = input('param.id'); //必须使用get方法
        $m = model('Vip_card');
        if (!$id) {
            $return['code'] = 0;
            $return['msg'] = 'ID不能为空!';
            return json($return);
        }
        $re = $m->where(['id'=>$id])->delete();
        if ($re) {
            $return['code'] = 1;
            $return['msg'] = '删除成功!';
        } else {
            $return['code'] = 0;
            $return['msg'] = '删除失败!';
        }
            return json($return);
    }



    public function sendCard()
    {
        $post = input('post.');
        $m = model('Vip_card');
        if ($post['vipid'] == '') {
            $return['status'] = 0;
            $return['msg'] = '请输入发送会员ID！';
            return json($return);
        }
        if (!db('vip')->where('id=' . $post['vipid'])->find()) {
            $return['status'] = 0;
            $return['msg'] = '该会员不存在！';
            return json($return);
        }
        $data['vipid'] = $post['vipid'];
        $data['status'] = 1;
        $re = $m->save($data,['id'=>$post['cardid']]);
        if ($re) {
            $return['status'] = 1;
            $return['msg'] = '发送成功!';
        } else {
            $return['status'] = 0;
            $return['msg'] = '发送失败!';
        }
        return json($return);
    }

    //CMS后台会员等级列表
    public function level()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '分组列表',
                'url' => U('Admin/Vip/level'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = M('Vip_level');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->order('exp')->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '分组列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台会员等级设置
    public function levelSet()
    {
        $id = I('id');
        $m = M('vip_level');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '分组列表',
                'url' => U('Admin/Vip/level'),
            ),
            '2' => array(
                'name' => '分组设置',
                'url' => $id ? U('Admin/Vip/levelSet', array('id' => $id)) : U('Admin/Vip/levelSet'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            $data = I('post.');
            $re = $id ? $m->save($data) : $m->add($data);
            if (FALSE !== $re) {
                $info['status'] = 1;
                $info['msg'] = '设置成功！';
            } else {
                $info['status'] = 0;
                $info['msg'] = '设置失败！';
            }
            $this->ajaxReturn($info);
        } else {
            if ($id) {
                $cache = $m->where('id=' . $id)->find();
                $this->assign('cache', $cache);
            }
            $this->display();
        }
    }

    public function levelDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = M('Vip_level');
        if (!id) {
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

    public function cardExport()
    {
        $id = I('id');
        $type = I('type');
        if ($id) {
            $map['id'] = array('in', in_parse_str($id));
        } else {
            $map['type'] = $type;
        }
        $data = M('vip_card')->where($map)->field('id,type,cardno,cardpwd,status')->select();
        foreach ($data as $k => $v) {
            switch ($v['type']) {
                case 1:
                    $data[$k]['type'] = "充值卡";
                    break;
                case 2:
                    $data[$k]['type'] = "代金券";
                    break;
            }
            switch ($v['status']) {
                case 0:
                    $data[$k]['status'] = "可制作";
                    break;
                case 1:
                    $data[$k]['status'] = "已发放";
                    break;
                case 2:
                    $data[$k]['status'] = "已使用";
                    break;
            }
        }
        $title = array('id', '类型', '卡号', '卡密', '状态');
        $this->exportexcel($data, $title, '卡券数据');
    }

    //CMS后台Vip提现订单
    public function txorder()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '会员中心',
                'url' => U('Admin/Vip/#'),
            ),
            '1' => array(
                'name' => '提现订单',
                'url' => U('Admin/Vip/txorder'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        $status = I('status');
        $this->assign('status', $status);
        if ($status || $status == '0') {
            $map['status'] = $status;
        }
        $this->assign('status', $status);
        //绑定搜索条件与分页
        $m = M('Vip_tx');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            //提现人姓名
            $map['txname'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '会员提现订单', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    public function txorderOk()
    {

        $options['appid'] = self::$SYS['set']['wxappid'];
        $options['appsecret'] = self::$SYS['set']['wxappsecret'];
        $wx = new \Util\Wx\Wechat($options);

        $arr = array_filter(explode(',', $_GET['id'])); //必须使用get方法
        $m = M('Vip_tx');
        $mlog = M('Vip_message');
        $mvip = M('Vip');

        $err = TRUE;
        foreach ($arr as $k => $v) {
            if ($v) {
                $old = $m->where('id=' . $v)->find();
                $old['status'] = 2;
                $old['txtime'] = time();
                $rv = $m->save($old);
                if ($rv !== FALSE) {
                    $data_msg['pids'] = $old['vipid'];
                    $data_msg['title'] = "亲爱的用户，提现已完成！" . $old['txprice'] . self::$SHOP['set']['yjname'] . "已成功发放到您的提现帐户里面了！";
                    $data_msg['content'] = "提现订单编号：" . $old['id'] . "<br><br>提现申请" . self::$SHOP['set']['yjname'] . "：" . $old['txprice'] . "<br><br>提现完成时间：" . date('Y-m-d H:i', $old['txtime']) . "<br><br>您的提现申请已完成，如有异常请联系客服！";
                    $data_msg['ctime'] = time();

                    // 发送信息===============
                    $customer = M('Wx_customer')->where(array('type' => 'tx2'))->find();
                    $vip = $mvip->where(array('id' => $old['vipid']))->find();
                    $msg = array();
                    $msg['touser'] = $vip['openid'];
                    $msg['msgtype'] = 'text';
                    $str = $customer['value'];
                    $msg['text'] = array('content' => $str);
                    $ree = $wx->sendCustomMessage($msg);
                    // 发送消息完成============

                    $rmsg = $mlog->add($data_msg);
                } else {
                    $err = FALSE;
                }
            } else {
                $err = FALSE;
            }
        }
        if ($err) {
            $info['status'] = 1;
            $info['msg'] = '批量设置成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '批量设置可能存在部分失败，请刷新后重新尝试!';
        }
        $this->ajaxReturn($info);
    }

    public function txorderCancel()
    {
        $id = I('id');
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取ID数据！';
            $this->ajaxReturn($info);
        }
        $m = M('Vip_tx');
        $mvip = M('Vip');
        $mlog = M('Shop_order_log');
        $old = $m->where('id=' . $id)->find();
        if (!$old) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取提现订单数据！';
            $this->ajaxReturn($info);
        }
        if ($old['status'] != 1) {
            $info['status'] = 0;
            $info['msg'] = '只可以操作新申请订单！';
            $this->ajaxReturn($info);
        }
        $vip = $mvip->where('id=' . $old['vipid'])->find();
        if (!$vip) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取相关会员信息！';
            $this->ajaxReturn($info);
        }
        $rold = $m->where('id=' . $id)->setField('status', 0);
        if ($rold !== FALSE) {
            $rvip = $mvip->where('id=' . $old['vipid'])->setInc('money', $old['txprice']);
            if ($rvip) {
                $data_msg['pids'] = $vip['id'];
                $data_msg['title'] = "提现申请未通过审核！" . $old['txprice'] . self::$SHOP['set']['yjname'] . "已成功退回您的帐户余额！";
                $data_msg['content'] = "提现订单编号：" . $old['id'] . "<br><br>提现申请" . self::$SHOP['set']['yjname'] . "：" . $old['txprice'] . "<br><br>提现退回时间：" . date('Y-m-d H:i', time()) . "<br><br>您的提现申请未通过审核，如有疑问请联系客服！";
                $data_msg['ctime'] = time();
                $rmsg = M('Vip_message')->add($data_msg);
                $info['status'] = 1;
                $info['msg'] = '取消提现申请成功！提现' . self::$SHOP['set']['yjname'] . '已自动退回用户帐户余额！';

                // 发送信息===============
                $customer = M('Wx_customer')->where(array('type' => 'tx3'))->find();
                $options['appid'] = self::$SYS['set']['wxappid'];
                $options['appsecret'] = self::$SYS['set']['wxappsecret'];
                $wx = new \Util\Wx\Wechat($options);
                $msg = array();
                $msg['touser'] = $vip['openid'];
                $msg['msgtype'] = 'text';
                $str = $customer['value'];
                $msg['text'] = array('content' => $str);
                $ree = $wx->sendCustomMessage($msg);
                // 发送消息完成============

                $this->ajaxReturn($info);
            } else {
                $info['status'] = 0;
                $info['msg'] = '取消成功，但自动退还' . self::$SHOP['set']['yjname'] . '至用户余额失败，请联系此会员！';
                $this->ajaxReturn($info);
            }
        } else {
            $info['status'] = 0;
            $info['msg'] = '操作失败，请重新尝试！';
            $this->ajaxReturn($info);
        }
    }

    public function txorderExport()
    {
        $id = I('id');
        $status = I('status');
        if ($id) {
            $map['id'] = array('in', in_parse_str($id));
        } else {
            $map['status'] = $status;
        }
        switch ($status) {
            case 0:
                $tt = "提现失败";
                break;
            case 1:
                $tt = "新申请";
                break;
            case 2:
                $tt = "提现完成";
                break;
        }
        $data = M('Vip_tx')->where($map)->select();
        foreach ($data as $k => $v) {
            switch ($v['status']) {
                case 0:
                    $data[$k]['status'] = "提现失败";
                    break;
                case 1:
                    $data[$k]['status'] = "新申请";
                    break;
                case 2:
                    $data[$k]['status'] = "提现完成";
                    break;
            }
            $data[$k]['txsqtime'] = date('Y-m-d H:i:s', $v['txsqtime']);
            $data[$k]['txtime'] = $v['txtime'] ? date('Y-m-d H:i:s', $v['txtime']) : '未执行';
        }
        $title = array('ID', '会员ID', '提现金额', '提现姓名', '提现电话', '提现银行', '提现分行', '提现银行所在地', '提现银行卡卡号', '提现申请时间', '提现完成时间', '订单状态');
        $this->exportexcel($data, $title, $tt . '订单' . date('Y-m-d H:i:s', time()));
    }

    /**
     * 导出数据为excel表格
     * @param $data    一个二维数组,结构如同从数据库查出来的数组
     * @param $title   excel的第一行标题,一个数组,如果为空则没有标题
     * @param $filename 下载的文件名
     * @examlpe
    $stu = M ('User');
     * $arr = $stu -> select();
     * exportexcel($arr,array('id','账户','密码','昵称'),'文件名!');
     */
    private function exportexcel($data = array(), $title = array(), $filename = 'report')
    {
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=" . $filename . ".xls");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)) {
            foreach ($title as $k => $v) {
                $title[$k] = iconv("UTF-8", "GB2312", $v);
            }
            $title = implode("\t", $title);
            echo "$title\n";
        }
        if (!empty($data)) {
            foreach ($data as $key => $val) {
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck] = iconv("UTF-8", "GB2312", $cv);
                }
                $data[$key] = implode("\t", $data[$key]);

            }
            echo implode("\n", $data);
        }

    }

}