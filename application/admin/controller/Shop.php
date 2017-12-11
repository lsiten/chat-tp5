<?php
// +----------------------------------------------------------------------
// | 用户后台基础类--CMS分组商城管理类
// +----------------------------------------------------------------------
namespace app\admin\controller;

class Shop extends Base
{

    public function _initialize()
    {
        //你可以在此覆盖父类方法
        parent::_initialize();
        //初始化两个配置
        self::$CMS['shopset'] = model('Shop_set')->find();
        self::$CMS['vipset'] = model('Vip_set')->find();
    }

    //CMS后台商城管理引导页
    public function index()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
        );
        $this->display();
    }

    //CMS后台门店设置
    public function set()
    {
        $id = I('id');
        $m = model('Shop_set');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城管理',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城设置',
                'url' => U('Admin/Shop/set'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //die('aa');
            $data = I('post.');
            $old = $m->where('id=' . $id)->find();
            if ($old) {
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
        $cache = $m->where('id=1')->find();
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台商城分组
    public function goods()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商品管理',
                'url' => U('Admin/Shop/goods'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = model('Shop_goods');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '商品管理', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台商品设置
    public function goodsSet()
    {
        $id = I('id');
        $m = model('Shop_goods');
        //dump($m);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商品管理',
                'url' => U('Admin/Shop/goods'),
            ),
            '2' => array(
                'name' => '商品设置',
                'url' => $id ? U('Admin/Shop/goodsSet', array('id' => $id)) : U('Admin/Md/mdSet'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //die('aa');
            $data = I('post.');
            $data['content'] = trimUE($data['content']);
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
        //读取标签
        $label = model('Shop_label')->select();
        $this->assign('label', $label);
        //AppTree快速无限分类
        $field = array("id", "pid", "name", "sorts", "concat(path,'-',id) as bpath");
        $cate = appTree(model('Shop_cate'), 0, $field);
        $this->assign('cate', $cate);
        //处理编辑界面
        if ($id) {
            $cache = $m->where('id=' . $id)->find();
            $this->assign('cache', $cache);
        }
        $this->display();
    }

    public function goodsDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_goods');
        if (!$id) {
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

    public function goodsStatus()
    {
        $m = model('Shop_goods');
        $now = I('status') ? 0 : 1;
        $map['id'] = I('id');
        $re = $m->where($map)->setField('status', $now);
        if ($re) {
            $info['status'] = 1;
            $info['msg'] = '设置成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '设置失败!';
        }
        $this->ajaxReturn($info);
    }

    //CMS后台商城分类
    public function cate()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城分类',
                'url' => U('Admin/Shop/cate'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = model('Shop_cate');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        //AppTree快速无限分类
        $field = array("id", "pid", "lv", "name", "summary", "soncate", "sorts", "concat(path,'-',id) as bpath");
        $cache = appTree($m, 0, $field);
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台商城分类设置
    public function cateSet()
    {
        $id = I('id');
        $m = model('Shop_cate');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城分类',
                'url' => U('Admin/Shop/cate'),
            ),
            '2' => array(
                'name' => '分类设置',
                'url' => $id ? U('Admin/Shop/cateSet', array('id' => $id)) : U('Admin/Shop/cateSet'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //die('aa');
            $data = I('post.');
            if ($id) {
                //保存时判断
                $old = $m->where('id=' . $id)->limit(1)->find();
                if ($old['pid'] != $data['pid']) {
                    $hasson = $m->where('pid=' . $id)->limit(1)->find();
                    if ($hasson) {
                        $info['status'] = 0;
                        $info['msg'] = '此分类有子分类，不可以移动！';
                        $this->ajaxReturn($info);
                    }
                }
                if ($data['pid']) {
                    //更新Path，强制处理
                    $path = setPath($m, $data['pid']);
                    $data['path'] = $path['path'];
                    $data['lv'] = $path['lv'];
                } else {
                    $data['path'] = 0;
                    $data['lv'] = 1;
                }
                $re = $m->save($data);
                if (FALSE !== $re) {
                    //更新新老父级，暂不做错误处理
                    if ($old['pid'] != $data['pid']) {
                        $re = setSoncate($m, $data['pid']);
                        $rold = setSoncate($m, $old['pid']);
                        $info['status'] = 1;
                        $info['msg'] = $old['pid'];
                        $this->ajaxReturn($info);
                    } else {
                        $re = setSoncate($m, $data['pid']);
                    }
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                if ($data['pid']) {
                    //更新父级，强制处理
                    $path = setPath($m, $data['pid']);
                    $data['path'] = $path['path'];
                    $data['lv'] = $path['lv'];
                } else {
                    $data['path'] = 0;
                    $data['lv'] = 1;
                }
                $re = $m->add($data);
                if ($re) {
                    //更新父级，暂不做错误处理
                    if ($data['pid']) {
                        $re = setSoncate($m, $data['pid']);
                    }
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
        //AppTree快速无限分类
        $field = array("id", "pid", "name", "sorts", "concat(path,'-',id) as bpath");
        $map = array('lv' => 1);
        $cate = appTree($m, 0, $field, $map);
        $this->assign('cate', $cate);
        $this->display();
    }

    public function cateDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_cate');
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = 'ID不能为空!';
            $this->ajaxReturn($info);
        }
        //删除时判断
        $self = $m->where('id=' . $id)->limit(1)->find();
        // 存在子类不删除
        // if($self['soncate']){
        // 	$info['status']=0;
        // 	$info['msg']='不能删除，存在子分类！';
        // 	$this->ajaxReturn($info);
        // }
        $re = $m->delete($id);
        // 删除所有子类
        $tempList = split(',', $self['soncate']);
        foreach ($tempList as $k => $v) {
            $res = $m->delete($v);
        }
        if ($re) {
            //更新上级soncate
            if ($self['pid']) {
                $re = setSoncate($m, $self['pid']);
            }
            $info['status'] = 1;
            $info['msg'] = '删除成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '删除失败!';
            $this->ajaxReturn($info);
        }
        $this->ajaxReturn($info);
    }

    //CMS后台商城分组
    public function group()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城分组',
                'url' => U('Admin/Shop/group'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = model('Shop_group');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '商城分组', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台分组设置
    public function groupSet()
    {
        $id = I('id');
        $m = model('Shop_group');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城分组',
                'url' => U('Admin/Shop/group'),
            ),
            '2' => array(
                'name' => '分组设置',
                'url' => $id ? U('Admin/Shop/groupSet', array('id' => $id)) : U('Admin/Shop/groupSet'),
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
        $this->display();
    }

    // 设置分组显示
    public function setGroup()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_group');
        if (!id) {
            $info['status'] = 0;
            $info['msg'] = 'ID不能为空!';
            $this->ajaxReturn($info);
        }
        // 撤销原有分组
        $ree = $m->where(array('status' => 1))->save(array('status' => 0));
        $re = $m->where(array('id' => $id))->save(array('status' => 1));
        if ($re) {
            $info['status'] = 1;
            $info['msg'] = '分组显示更新成功!';
        } else {
            $info['status'] = 0;
            $info['msg'] = '设置失败!';
        }
        $this->ajaxReturn($info);
    }

    public function groupDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_group');
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

    //CMS后台SKU属性
    public function skuattr()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => 'SKU属性',
                'url' => U('Admin/Shop/skuattr'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = model('Shop_skuattr');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', 'SKU属性', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台SKU属性设置
    public function skuattrSet()
    {
        $id = I('id');
        $m = model('Shop_skuattr');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城分组',
                'url' => U('Admin/Shop/skuattr'),
            ),
            '2' => array(
                'name' => 'SKU属性设置',
                'url' => $id ? U('Admin/Shop/skuattrSet', array('id' => $id)) : U('Admin/Shop/skuattrSet'),
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
                    if ($data['newitem']) {
                        $mitem = model('Shop_skuattr_item');
                        $dit['pid'] = $id;
                        $items = array_filter(explode(',', $data['newitem']));
                        foreach ($items as $v) {
                            $dit['name'] = $v;
                            $rit = $mitem->add($dit);
                            if ($rit) {
                                $rr['path'] = $id . $rit;
                                $rerr = $mitem->where('id=' . $rit)->save($rr);
                            }
                        }
                        $son = $mitem->where('pid=' . $id)->field('name,path')->select();
                        $dson['items'] = "";
                        $dson['itemspath'] = "";
                        foreach ($son as $v) {
                            $dson['items'] = $dson['items'] . $v['name'] . ',';
                            $dson['itemspath'] = $dson['itemspath'] . $v['path'] . ',';
                        }
                        $rfather = $m->where('id=' . $id)->save($dson);
                    }
                    $info['status'] = 1;
                    $info['msg'] = '设置成功！';
                } else {
                    $info['status'] = 0;
                    $info['msg'] = '设置失败！';
                }
            } else {
                $dt['name'] = $data['name'];
                $dt['cctime'] = time();
                $re = $m->add($dt);
                if ($re) {
                    if ($data['newitem']) {
                        $mitem = model('Shop_skuattr_item');
                        $dit['pid'] = $re;
                        $items = array_filter(explode(',', $data['newitem']));
                        foreach ($items as $v) {
                            $dit['name'] = $v;
                            $rit = $mitem->add($dit);
                            if ($rit) {
                                $rr['path'] = $re . $rit;
                                $rerr = $mitem->where('id=' . $rit)->save($rr);
                            }
                        }
                        $son = $mitem->where('pid=' . $re)->field('name,path')->select();
                        $dson['items'] = "";
                        $dson['itemspath'] = "";
                        foreach ($son as $v) {
                            $dson['items'] = $dson['items'] . $v['name'] . ',';
                            $dson['itemspath'] = $dson['itemspath'] . $v['path'] . ',';
                        }
                        $rfather = $m->where('id=' . $re)->save($dson);
                    }
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
        $this->display();
    }

    public function skuattrDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_skuattr');
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

    //用于SKUINFO保存
    public function skuattrSave()
    {
        $id = $_GET['id']; //必须使用get方法
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = '商品ID不能为空!';
            $this->ajaxReturn($info);
        }
        //处理skuattr
        $data = I('data');
        if (!$data) {
            $info['status'] = 0;
            $info['msg'] = "您还没有选择任何属性！";
            $this->ajaxReturn($info);
        }
        $list;
        $arr = array_filter(explode(';', $data));
        foreach ($arr as $k => $v) {
            $arr2 = array_filter(explode('-', $v));
            $arrattr = explode(':', $arr2[0]);
            $arritem = array_filter(explode(',', $arr2[1]));
            $list[$k]['attrid'] = $arrattr[0];
            $list[$k]['attrlabel'] = $arrattr[1];
            $checked = "";
            //循环item
            foreach ($arritem as $kk => $vv) {
                $at = explode(':', $vv);
                $list[$k]['items'][$at[0]] = $at[1];
                $checked = $checked . $at[0] . ',';
            }
            $list[$k]['checked'] = $checked;
        }
        $list = list_sort_by($list, 'attrid', 'asc');
        //dump($list);
        //$info['status']=1;
        //$info['msg']=serialize($list);
        //$this->ajaxReturn($info);
        $m = model('Shop_goods');
        $skuinfo['skuinfo'] = serialize($list);
        $re = $m->where('id=' . $id)->save($skuinfo);
        if ($re !== FALSE) {
            $info['status'] = 1;
            $info['msg'] = 'SKU属性保存成功!如有变更请及时更新所有SKU!';
        } else {
            $info['status'] = 0;
            $info['msg'] = 'SKU属性保存失败!请重新尝试!';
        }
        $this->ajaxReturn($info);
    }

    //用于SKU生成
    public function skuattrMake()
    {
        $id = $_GET['id']; //必须使用get方法
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = '商品ID不能为空!';
            $this->ajaxReturn($info);
        }
        $m = model('Shop_goods');
        $goods = $m->where('id=' . $id)->find();
        $skuinfo = unserialize($goods['skuinfo']);
        //dump($skuinfo);
        if (!$skuinfo) {
            $info['status'] = 0;
            $info['msg'] = '您还未设置或保存SKU属性!';
            $this->ajaxReturn($info);
        }
        $cacheattrs = array(); //缓存所有属性表
        $cache; //缓存skupath列表
        $tmpsku; //缓存零时sku
        $tmpskuattrs; //sku属性对照表
        foreach ($skuinfo as $k => $v) {
            $cacheattrs = $cacheattrs + $skuinfo[$k]['items'];
            $cache[$k] = array_filter(explode(',', $v['checked']));
        }

        if (count($cache) > 1) {
            //快速排列
            $tmp = Descartes($cache);
            foreach ($tmp as $k => $v) {
                $sttr;
                foreach ($v as $kk => $vv) {
                    $sttr[$kk] = $cacheattrs[$vv];
                }
                $sk = $id . '-' . implode('-', $v);
                $tmpsku[$k] = $sk;
                $tmpskuattrs[$sk] = implode(',', $sttr);

            }
        } else {
            foreach ($cache[0] as $k => $v) {
                $sk = $id . '-' . $v;
                $tmpsku[$k] = $sk;
                $tmpskuattrs[$sk] = $cacheattrs[$v];
            }
        }
        //dump($tmpskuattrs);
        //dump($tmpsku);

        $fftmpsku = array_flip($tmpsku);
        //处理原始sku
        $msku = model('Shop_goods_sku');
        $oldsku = $msku->where('goodsid=' . $id)->select();
        if ($oldsku) {
            foreach ($oldsku as $k => $v) {
                //如果已经建立,判断状态
                if (!in_array($v['sku'], $tmpsku)) {
                    //如果不存在，禁用该sku
                    $v['status'] = 0;
                    $ro = $msku->save($v);
                } else {
                    //如果已经存在，开启该sku
                    $v['status'] = 1;
                    $ro = $msku->save($v);
                    //移除fftmpsku对应项目
                    unset($fftmpsku[$v['sku']]);
                }

            }
        }
        //最后需要添加的新sku
        $finaltmpsku = array_flip($fftmpsku);
        //dump($finaltmpsku);
        //die();
        if ($finaltmpsku) {
            $dsku;
            foreach ($finaltmpsku as $k => $v) {
                $dsku[$k]['goodsid'] = $id;
                $dsku[$k]['sku'] = $v;
                $dsku[$k]['skuattr'] = $tmpskuattrs[$v];
                $dsku[$k]['price'] = $goods['price'];
                $dsku[$k]['num'] = $goods['num'];
                $dsku[$k]['status'] = 1;
            }
            //强制重新排序
            sort($dsku);
            //计算总库存
            $re = $msku->addAll($dsku);
            if ($re) {
                $totalnum = $msku->where(array('goodsid' => $id, 'status' => 1))->sumodel('num');
                if ($totalnum) {
                    $rgg = $m->where('id=' . $id)->setField('num', $totalnum);
                }
                //计算总库存
                $info['status'] = 1;
                $info['msg'] = 'SKU更新成功!';
            } else {
                $info['status'] = 0;
                $info['msg'] = 'SKU更新失败!请重新尝试!';
            }
        } else {
            $totalnum = $msku->where(array('goodsid' => $id, 'status' => 1))->sumodel('num');
            if ($totalnum) {
                $rgg = $m->where('id=' . $id)->setField('num', $totalnum);
            }
            $info['status'] = 1;
            $info['msg'] = 'SKU更新成功!没有新增SKU!';
        }
        $this->ajaxReturn($info);
    }

    //CMS后台SKU管理
    public function sku()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商品管理',
                'url' => U('Admin/Shop/goods'),
            ),
            '1' => array(
                'name' => '商品SKU管理',
                'url' => U('Admin/Shop/skuattr', array('id' => $_GET['id'])),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        $goodsid = I('id');
        $this->assign('goodsid', $goodsid);
        //绑定商品和skuinfo
        $goods = model('Shop_goods')->where('id=' . $goodsid)->find();
        $this->assign('goods', $goods);
        if ($goods['skuinfo']) {
            $skuinfo = unserialize($goods['skuinfo']);
            $skm = model('Shop_skuattr_item');
            foreach ($skuinfo as $k => $v) {
                $checked = explode(',', $v['checked']);
                $attr = $skm->field('path,name')->where('pid=' . $v['attrid'])->select();
                foreach ($attr as $kk => $vv) {
                    $attr[$kk]['checked'] = in_array($vv['path'], $checked) ? 1 : '';
                }
                $skuinfo[$k]['allitems'] = $attr;
            }
        }
        //dump($skuinfo);

        $this->assign('skuinfo', $skuinfo);
        //绑定搜索条件与分页
        $m = model('Shop_goods_sku');
        //追入商品条件
        $map['goodsid'] = $goodsid;
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        $map['status'] = 1;
        if ($name) {
            $map['skuattr'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        //$psize=self::$CMS['set']['pagesize']?self::$CMS['set']['pagesize']:20;
        $psize = 50;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '商品SKU管理', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台sku设置
    public function skuSet()
    {
        $id = I('id');
        $m = model('Shop_goods_sku');
        //处理编辑界面
        $cache = $m->where('id=' . $id)->find();
        $this->assign('cache', $cache);
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商品SKU管理',
                'url' => U('Admin/Shop/sku', array('id' => $cache['goodsid'])),
            ),
            '2' => array(
                'name' => '商品SKU设置',
                'url' => U('Admin/Shop/skuSet', array('id' => $id)),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //处理POST提交
        if (IS_POST) {
            //只有保存模式
            $data = I('post.');
            $re = $m->where('id=' . $id)->save($data);
            if (FALSE !== $re) {
                //重新计算总库存
                $totalnum = $m->where(array('goodsid' => $cache['goodsid'], 'status' => 1))->sumodel('num');
                if ($totalnum) {
                    $rgg = model('Shop_goods')->where('id=' . $cache['goodsid'])->setField('num', $totalnum);
                }
                $info['status'] = 1;
                $info['msg'] = '设置成功！';
            } else {
                $info['status'] = 0;
                $info['msg'] = '设置失败！';
            }
            $this->ajaxReturn($info);
        }

        $this->display();
    }

    //CMS后台SKU查找带回管理器
    public function skuLoader()
    {
        $m = model('Shop_skuattr');
        $findback = I('fbid');
        $this->assign('findback', $findback);
        $map['id'] = array('not in', I('ids'));
        $cache = $m->where($map)->select();
        $this->assign('cache', $cache);
        $this->ajaxReturn($this->fetch());
    }

    //CMS后台SKU查找带回模板
    public function skuFindback()
    {
        if (IS_AJAX) {
            $m = model('Shop_skuattr');
            $id = I('id');
            $this->assign('findback', $findback);
            $map['id'] = $id;
            $cache = $m->where($map)->limit(1)->find();
            $this->assign('cache', $cache);
            $items = model('Shop_skuattr_item')->where('pid=' . $id)->select();
            $this->assign('items', $items);
            $this->ajaxReturn($this->fetch());
        } else {
            utf8error('非法访问！');
        }
    }

    //CMS后台广告分组
    public function ads()
    {
        //绑定搜索条件与分页
        $m = model('Shop_ads');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        foreach ($cache as $k => $v) {
            $listpic = $this->getPic($v['pic']);
            $cache[$k]['imgurl'] = $listpic['imgurl'];
        }
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '商城广告', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台广告设置
    public function adsSet()
    {
        $id = I('id');
        $m = model('Shop_ads');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城广告',
                'url' => U('Admin/Shop/ads'),
            ),
            '2' => array(
                'name' => '广告设置',
                'url' => $id ? U('Admin/Shop/adsSet', array('id' => $id)) : U('Admin/Shop/adsSet'),
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
        $this->display();
    }

    public function adsDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_ads');
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

    //CMS后台商城订单
    public function order()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '订单管理',
                'url' => U('Admin/Shop/order'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        $status = I('status');
        if ($status || $status == '0') {
            $map['status'] = $status;
            //交易满7天
            if ($status == 8) {
                $map['status'] = 3;
                $seven = time() - 604800;
                $map['ctime'] = array('elt', $seven);
            }
            // 当天所有订单，零点算起
            if ($status == 9) {
                unset($map['status']);
                $today = strtotime(date("Y-m-d"));
                $map['ctime'] = array('egt', $today);
                //echo $today;
            }
        }
        $this->assign('status', $status);
        //绑定搜索条件与分页
        $m = model('Shop_order');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        $timeRanges = I('timeRange') ? I('timeRange') : '';
        $oid = I('oid') ? I('oid') : '';
        if($timeRanges){
            //时间段搜索
            $timeRange = explode(" --- ", $timeRanges);
            $time1 =  strtotime($timeRange[0]);
            $time2 =  strtotime($timeRange[1]);
            $map['ctime']=array('between',array($time1,$time2));
            $this->assign('timeRanges', $timeRanges);
        }
        if($oid){
            //订单号搜索
            $map['oid'] = array('like', "%$oid%");
            $this->assign('oid', $oid);
        }
        if ($name) {
            //订单号邦定
            $map['vipmobile'] = array('like', "%$name%");
            $map['_logic'] = 'OR';
            $this->assign('name', $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->order('ctime desc')->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '商城订单', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    // Admin后台订单当天报表
    public function orderReport()
    {
        // Prepare Data
        $mgoods = model('Shop_goods');
        $msku = model('Shop_goods_sku');
        $morder = D('shop_order');
        $data = $morder->today();

        $goods = array();
        $sku = array();
        $temp = $mgoods->select();
        foreach ($temp as $k => $v) {
            $goods[$v['id']] = $v;
        }
        $temp = $msku->select();
        foreach ($temp as $k => $v) {
            $sku[$v['id']] = $v;
        }
        $this->assign('goods', $goods);
        $this->assign('sku', $sku);
        $this->assign('cache', $data);
        $this->display();
    }

    //CMS后台Order详情
    public function orderDetail()
    {
        $id = I('id');
        $m = model('Shop_order');
        $mlog = model('Shop_order_log');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '商城订单',
                'url' => U('Admin/Shop/order'),
            ),
            '2' => array(
                'name' => '订单详情',
                'url' => $id ? U('Admin/Shop/orderDetail', array('id' => $id)) : U('Admin/Shop/orderDetail'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        $cache = $m->where('id=' . $id)->find();
        //坠入vip
        $vip = model('vip')->where('id=' . $cache['vipid'])->find();
        $this->assign('vip', $vip);
        $cache['items'] = unserialize($cache['items']);
        $log = $mlog->where('oid=' . $cache['id'])->select();
        $fxlog = model('Fx_syslog')->where('oid=' . $cache['id'])->select();
        $this->assign('log', $log);
        $this->assign('fxlog', $fxlog);
        $this->assign('cache', $cache);
        $this->display();
    }

    //发货快递
    public function orderFhkd()
    {
        $map['id'] = I('id');
        $cache = model('Shop_order')->where($map)->find();

        Vendor("Express.Express");
        $Express = new \Express ();
        $result  = $Express -> getorder($cache['fahuokdnum']);

        $this->assign('express', $result);
        $this->assign('cache', $cache);
        $mb = $this->fetch();
        $this->ajaxReturn($mb);
    }

    public function orderFhkdSave()
    {
        $data = I('post.');
        if (!$data) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取数据！';
        }
        $data['changetime'] = time();
        $re = model('Shop_order')->where('id=' . $data['id'])->save($data);
        if (FALSE !== $re) {
            $info['status'] = 1;
            $info['msg'] = '操作成功！';
        } else {
            $info['status'] = 0;
            $info['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info);
    }

    //订单改价
    public function orderChange()
    {
        $map['id'] = I('id');
        $cache = model('Shop_order')->where($map)->find();
        $this->assign('cache', $cache);
        $mb = $this->fetch();
        $this->ajaxReturn($mb);
    }

    public function orderChangeSave()
    {
        $data = I('post.');
        if (!$data) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取数据！';
        }
        $data['changetime'] = time();
        $data['oid'] = date('YmdHis') . '-' . $data['id'];
        $re = model('Shop_order')->where('id=' . $data['id'])->save($data);
        $mlog = model('Shop_order_log');
        if (FALSE !== $re) {
            $log['oid'] = $cache['oid'];
            $log['msg'] = '订单价格改为' . $data['payprice'] . '-成功';
            $log['ctime'] = time();
            $rlog = $mlog->add($log);
            $info['status'] = 1;
            $info['msg'] = '操作成功！';
        } else {
            $info['status'] = 0;
            $info['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info);
    }

    //订单关闭
    public function orderClose()
    {
        $map['id'] = I('id');
        $cache = model('Shop_order')->where($map)->find();
        $this->assign('cache', $cache);
        $mb = $this->fetch();
        $this->ajaxReturn($mb);
    }

    public function orderCloseSave()
    {
        $data = I('post.');
        if (!$data) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取数据！';
        }
        $m = model('Shop_order');
        $mlog = model('Shop_order_log');
        $mslog = model('Shop_order_syslog');
        $cache = $m->where('id=' . $data['id'])->find();
        switch ($cache['status']) {
            case '1':
                $data['status'] = 6;
                $data['closetime'] = time();
                $re = $m->where('id=' . $data['id'])->save($data);
                if (FALSE !== $re) {
                    //前端LOG
                    $log['oid'] = $cache['id'];
                    $log['msg'] = '未支付订单关闭成功';
                    $log['ctime'] = time();
                    $rlog = $mlog->add($log);
                    //后端LOG
                    $log['type'] = 6;
                    $log['paytype'] = $cache['paytype'];
                    $rslog = $mslog->add($log);

                    $info['status'] = 1;
                    $info['msg'] = '关闭未支付订单成功！';
                } else {
                    //前端LOG
                    $log['oid'] = $cache['id'];
                    $log['msg'] = '未支付订单关闭失败';
                    $log['ctime'] = time();
                    $rlog = $mlog->add($log);
                    //后端LOG
                    $log['type'] = -1;
                    $log['paytype'] = $cache['paytype'];
                    $rslog = $mslog->add($log);
                    $info['status'] = 0;
                    $info['msg'] = '关闭未支付订单失败！';
                }
                $this->ajaxReturn($info);
                break;
            case '2':
                //已支付订单跳转到这里处理
                $this->orderClosePay($cache, $data);
                break;
            default:
                $info['status'] = 0;
                $info['msg'] = '只有未付款和已付款订单可以关闭!';
                $this->ajaxReturn($info);
                break;
        }

    }

    //已支付订单退款
    public function orderClosePay($cache, $data)
    {
        //关闭订单时不再处理库存
        $m = model('Shop_order');
        $mvip = model('Vip');
        $mlog = model('Shop_order_log');
        $mslog = model('Shop_order_syslog');
        if (!$cache['ispay']) {
            $info['status'] = 0;
            $info['msg'] = '订单支付状态异常！请重试或联系技术！';
            $this->ajaxReturn($info);
        }
        //抓取会员数据
        $vip = $mvip->where('id=' . $cache['vipid'])->find();
        if (!$vip) {
            $info['status'] = 0;
            $info['msg'] = '会员数据获取异常！请重试或联系技术！';
            $this->ajaxReturn($info);
        }
        //支付金额
        $payprice = $cache['payprice'];
        //全部退款至余额
        $data['status'] = 6;
        $data['closetime'] = time();
        $re = $m->where('id=' . $cache['id'])->save($data);
        if (FALSE !== $re) {
            $log['oid'] = $cache['id'];
            $log['msg'] = '订单关闭-成功';
            $log['ctime'] = time();
            $rlog = $mlog->add($log);
            $info['status'] = 1;
            $info['msg'] = '关闭订单成功！';
            if ($cache['ispay']) {
                $mm = $vip['money'] + $payprice;
                $rvip = $mvip->where('id=' . $cache['vipid'])->setField('money', $mm);
                if ($rvip) {
                    //前端LOG
                    $log['oid'] = $cache['id'];
                    $log['msg'] = '自动退款' . $payprice . '元至用户余额-成功';
                    $log['ctime'] = time();
                    $rlog = $mlog->add($log);
                    $log['type'] = 6;
                    $log['paytype'] = $cache['paytype'];
                    $rslog = $mslog->add($log);
                    //后端LOG
                    $info['status'] = 1;
                    $info['msg'] = '关闭订单成功！自动退款' . $payprice . '元至用户余额成功!';
                } else {
                    //前端LOG
                    $log['oid'] = $cache['id'];
                    $log['msg'] = '自动退款' . $payprice . '元至用户余额-失败!请联系客服!';
                    $log['ctime'] = time();
                    $rlog = $mlog->add($log);
                    //后端LOG
                    $log['type'] = -1;
                    $log['paytype'] = $cache['paytype'];
                    $rslog = $mslog->add($log);
                    $info['status'] = 1;
                    $info['msg'] = '关闭订单成功！自动退款' . $payprice . '元至用户余额失败!请联系技术！';
                }
            }

        } else {
            $info['status'] = 0;
            $info['msg'] = '关闭订单失败！请重新尝试!';
        }
        $this->ajaxReturn($info);
    }

    //订单发货
    public function orderDeliver()
    {
        $id = I('id');
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取ID数据！';
        }
        $re = model('Shop_order')->where('id=' . $id)->setField('status', 3);
        $mlog = model('Shop_order_log');
        $mslog = model('Shop_order_syslog');
        $dwechat = D('Wechat');
        if (FALSE !== $re) {
            $log['oid'] = $id;
            $log['msg'] = '订单已发货';
            $log['ctime'] = time();
            $rlog = $mlog->add($log);
            //后端LOG
            $log['type'] = 3;
            $log['paytype'] = $cache['paytype'];
            $rslog = $mslog->add($log);

            // 插入订单发货模板消息=====================
            $order = model('Shop_order')->where('id=' . $id)->find();
            $vip = model('vip')->where(array('id' => $order['vipid']))->find();
            $templateidshort = 'OPENTM201541214';
            $templateid = $dwechat->getTemplateId($templateidshort);

            if ($templateid) { // 存在才可以发送模板消息
                $data = array();
                $data['touser'] = $vip['openid'];
                $data['template_id'] = $templateid;
                $data['topcolor'] = "#0000FF";
                $data['data'] = array(
                    'first' => array('value' => '您好，您的订单已发货'),
                    'keyword1' => array('value' => $order['oid']),
                    'keyword2' => array('value' => $order['fahuokd']),
                    'keyword3' => array('value' => $order['fahuokdnum']),
                    'remark' => array('value' => '')
                );
                $options['appid'] = self::$SYS['set']['wxappid'];
                $options['appsecret'] = self::$SYS['set']['wxappsecret'];

                $wx = new \Util\Wx\Wechat($options);
                $rere = $wx->sendTemplateMessage($data);

            }
            // 插入订单发货模板消息结束=================
            $info['status'] = 1;
            $info['msg'] = '操作成功！';
        } else {
            $info['status'] = 0;
            $info['msg'] = '操作失败！';
        }
        $this->ajaxReturn($info);
    }

    //订单批量发货
    public function orderDeliverAll()
    {
        $arr = array_filter(explode(',', $_GET['id'])); //必须使用get方法
        if (!$arr) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取ID数据！';
            $this->ajaxReturn($info);
        }
        $m = model('Shop_order');
        $mlog = model('Shop_order_log');
        $mslog = model('Shop_order_syslog');
        // ==========================================================
        $dwechat = D('Wechat');
        $options['appid'] = self::$SYS['set']['wxappid'];
        $options['appsecret'] = self::$SYS['set']['wxappsecret'];
        $wx = new \Util\Wx\Wechat($options);
        // ==========================================================
        $err = TRUE;
        foreach ($arr as $k => $v) {
            $old = $m->where('id=' . $v)->find();
            if ($old['status'] == 2) {
                $re = $m->where('id=' . $old['id'])->setField('status', 3);
                if (FALSE !== $re) {
                    $log['oid'] = $old['id'];
                    $log['msg'] = '订单已发货';
                    $log['ctime'] = time();
                    $rlog = $mlog->add($log);
                    //后端LOG
                    $log['type'] = 3;
                    $log['paytype'] = $cache['paytype'];
                    $rslog = $mslog->add($log);
                    // 插入订单发货模板消息=====================
                    $vip = model('vip')->where(array('id' => $old['vipid']))->find();
                    $templateidshort = 'OPENTM201541214';
                    $templateid = $dwechat->getTemplateId($templateidshort);
                    if ($templateid) { // 存在才可以发送模板消息
                        $data = array();
                        $data['touser'] = $vip['openid'];
                        $data['template_id'] = $templateid;
                        $data['topcolor'] = "#0000FF";
                        $data['data'] = array(
                            'first' => array('value' => '您好，您的订单已发货'),
                            'keyword1' => array('value' => $old['oid']),
                            'keyword2' => array('value' => $old['fahuokd']),
                            'keyword3' => array('value' => $old['fahuokdnum']),
                            'remark' => array('value' => '')
                        );
                        $re = $wx->sendTemplateMessage($data);
                    }
                    // 插入订单发货模板消息结束=================
                } else {
                    $err = FALSE;
                }
            }
        }
        if ($err) {
            $info['status'] = 1;
            $info['msg'] = '批量发货成功！';
        } else {
            $info['status'] = 0;
            $info['msg'] = '批量发货可能有部分失败，请刷新后重新尝试！';
        }

        $this->ajaxReturn($info);
    }

    //完成订单
    public function orderSuccess()
    {
        $id = I('id');
        if (!$id) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取ID数据！';
            $this->ajaxReturn($info);
        }
        //判断商城配置
        if (!self::$CMS['shopset']) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取商城配置信息！';
            $this->ajaxReturn($info);
        }
        //判断会员配置
        if (!self::$CMS['vipset']) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取会员配置信息！';
            $this->ajaxReturn($info);
        }
        //分销流程介入
        $m = model('Shop_order');
        $map['id'] = $id;
        $cache = $m->where($map)->find();
        if (!$cache) {
            $info['status'] = 0;
            $info['msg'] = '操作失败！';
            $this->ajaxReturn($info);
        }
        if ($cache['status'] != 3) {
            $info['status'] = 0;
            $info['msg'] = '操作失败！';
            $this->ajaxReturn($info);
        }
        //追入会员信息
        $vip = model('Vip')->where('id=' . $cache['vipid'])->find();
        if (!$vip) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取此订单的会员信息！';
            $this->ajaxReturn($info);
        }
        $cache['etime'] = time(); //交易完成时间
        $cache['status'] = 5;
        $rod = $m->save($cache);
        if (FALSE !== $rod) {
            //修改会员账户金额、经验、积分、等级
            $data_vip['id'] = $cache['vipid'];
            $data_vip['score'] = array('exp', 'score+' . round($cache['payprice'] * self::$CMS['vipset']['cz_score'] / 100));
            if (self::$CMS['vipset']['cz_exp'] > 0) {
                $data_vip['exp'] = array('exp', 'exp+' . round($cache['payprice'] * self::$CMS['vipset']['cz_exp'] / 100));
                $data_vip['cur_exp'] = array('exp', 'cur_exp+' . round($cache['payprice'] * self::$CMS['vipset']['cz_exp'] / 100));
                $level = $this->getLevel($vip['cur_exp'] + round($cache['payprice'] * self::$CMS['vipset']['cz_exp'] / 100));
                $data_vip['levelid'] = $level['levelid'];
                //会员分销统计字段
                //会员购买一次变成分销商
                $data_vip['isfx'] = 1;
                //会员合计支付
                $data_vip['total_buy'] = $data_vip['total_buy'] + $cache['payprice'];
            }
            $re = model('vip')->save($data_vip);
            if (FALSE === $re) {
                $info['status'] = 0;
                $info['msg'] = '更新订单关联会员信息失败！';
                $this->ajaxReturn($info);
            }

            //分销佣金计算(多分销)
            $commission = D('Commission');
            $orderids = array();
            $orderids[] = $id;
            $pid = $vip['pid'];
            $mvip = model('vip');
            $mfxlog = model('fx_syslog');
            $fxlog['oid'] = $cache['id'];
            $fxlog['fxprice'] = $fxprice = $cache['payprice'] - $cache['yf'];
            $fxlog['ctime'] = time();
            $fxtmp = array(); //缓存3级数组
            if ($pid) {
                //第一层分销
                $fx1 = $mvip->where('id=' . $pid)->find();
                if ($fx1['isfx']) {
                    $fxlog['fxyj'] = $commission->ordersCommission('fx1rate', $orderids);
                    $fx1['money'] = $fx1['money'] + $fxlog['fxyj'];
                    $fx1['total_xxbuy'] = $fx1['total_xxbuy'] + 1; //下线中购买产品总次数
                    $fx1['total_xxyj'] = $fx1['total_xxyj'] + $fxlog['fxyj']; //下线贡献佣金
                    $rfx = $mvip->save($fx1);
                    $fxlog['from'] = $vip['id'];
                    $fxlog['fromname'] = $vip['nickname'];
                    $fxlog['to'] = $fx1['id'];
                    $fxlog['toname'] = $fx1['nickname'];
                    if (FALSE !== $rfx) {
                        //佣金发放成功
                        $fxlog['status'] = 1;
                    } else {
                        //佣金发放失败
                        $fxlog['status'] = 0;
                    }
                    //单层逻辑
                    //$rfxlog=$mfxlog->add($fxlog);
                    //file_put_contents('./Data/app_debug.txt','日志时间:'.date('Y-m-d H:i:s').PHP_EOL.'纪录信息:'.$rfxlog.PHP_EOL.PHP_EOL.$mfxlog->getLastSql().PHP_EOL.PHP_EOL,FILE_APPEND);
                    array_push($fxtmp, $fxlog);
                }
                //第二层分销
                if ($fx1['pid']) {
                    $fx2 = $mvip->where('id=' . $fx1['pid'])->find();
                    if ($fx2['isfx']) {
                        $fxlog['fxyj'] = $commission->ordersCommission('fx2rate', $orderids);
                        $fx2['money'] = $fx2['money'] + $fxlog['fxyj'];
                        $fx2['total_xxbuy'] = $fx2['total_xxbuy'] + 1; //下线中购买产品人数计数
                        $fx2['total_xxyj'] = $fx2['total_xxyj'] + $fxlog['fxyj']; //下线贡献佣金
                        $rfx = $mvip->save($fx2);
                        $fxlog['from'] = $vip['id'];
                        $fxlog['fromname'] = $vip['nickname'];
                        $fxlog['to'] = $fx2['id'];
                        $fxlog['toname'] = $fx2['nickname'];
                        if (FALSE !== $rfx) {
                            //佣金发放成功
                            $fxlog['status'] = 1;
                        } else {
                            //佣金发放失败
                            $fxlog['status'] = 0;
                        }
                        //单层逻辑
                        //$rfxlog=$mfxlog->add($fxlog);
                        //file_put_contents('./Data/app_debug.txt','日志时间:'.date('Y-m-d H:i:s').PHP_EOL.'纪录信息:'.$rfxlog.PHP_EOL.PHP_EOL.$mfxlog->getLastSql().PHP_EOL.PHP_EOL,FILE_APPEND);
                        array_push($fxtmp, $fxlog);
                    }
                }
                //第三层分销
                if ($fx2['pid']) {
                    $fx3 = $mvip->where('id=' . $fx2['pid'])->find();
                    if ($fx3['isfx']) {
                        $fxlog['fxyj'] = $commission->ordersCommission('fx3rate', $orderids);
                        $fx3['money'] = $fx3['money'] + $fxlog['fxyj'];
                        $fx3['total_xxbuy'] = $fx3['total_xxbuy'] + 1; //下线中购买产品人数计数
                        $fx3['total_xxyj'] = $fx3['total_xxyj'] + $fxlog['fxyj']; //下线贡献佣金
                        $rfx = $mvip->save($fx3);
                        $fxlog['from'] = $vip['id'];
                        $fxlog['fromname'] = $vip['nickname'];
                        $fxlog['to'] = $fx3['id'];
                        $fxlog['toname'] = $fx3['nickname'];
                        if (FALSE !== $rfx) {
                            //佣金发放成功
                            $fxlog['status'] = 1;
                        } else {
                            //佣金发放失败
                            $fxlog['status'] = 0;
                        }
                        //单层逻辑
                        //$rfxlog=$mfxlog->add($fxlog);
                        //file_put_contents('./Data/app_debug.txt','日志时间:'.date('Y-m-d H:i:s').PHP_EOL.'纪录信息:'.$rfxlog.PHP_EOL.PHP_EOL.$mfxlog->getLastSql().PHP_EOL.PHP_EOL,FILE_APPEND);
                        array_push($fxtmp, $fxlog);
                    }
                }
                //多层分销
                if (count($fxtmp) >= 1) {
                    $refxlog = $mfxlog->addAll($fxtmp);
                    if (!$refxlog) {
                        file_put_contents('./Data/app_fx_error.txt', '错误日志时间:' . date('Y-m-d H:i:s') . PHP_EOL . '错误纪录信息:' . $rfxlog . PHP_EOL . PHP_EOL . $mfxlog->getLastSql() . PHP_EOL . PHP_EOL, FILE_APPEND);
                    }
                }

                //花鼓分销方案
                $allhg = $mvip->field('id')->where('isfxgd=1')->select();
                if ($allhg) {
                    $tmppath = array_slice(explode('-', $vip['path']), -20);
                    $tmphg = array();
                    foreach ($allhg as $v) {
                        array_push($tmphg, $v['id']);
                    }
                    //需要计算的花鼓
                    $needhg = array_intersect($tmphg, $tmppath);
                    if (count($needhg)) {
                        $fxlog['oid'] = $cache['id'];
                        $fxlog['fxprice'] = $fxprice;
                        $fxlog['ctime'] = time();
                        $fxlog['fxyj'] = $fxprice * 0.05;
                        $fxlog['from'] = $vip['id'];
                        $fxlog['fromname'] = $vip['nickname'];
                        foreach ($needhg as $k => $v) {
                            $hg = $mvip->where('id=' . $v)->find();
                            if ($hg) {
                                $rhg = $mvip->where('id=' . $v)->setInc('money', $fxlog['fxyj']);
                                if ($rhg) {
                                    $fxlog['to'] = $hg['id'];
                                    $fxlog['toname'] = $hg['nickname'] . '[花股收益]';
                                    $rehgfxlog = $mfxlog->add($fxlog);
                                }
                            }
                        }
                    }
                }
            }
            //分销佣金计算(单分销)
            /*
            $pid = $vip['pid'];
            $mvip = model('vip');
            $mfxlog = model('fx_syslog');
            $fxlog['oid'] = $cache['id'];
            $fxlog['fxprice'] = $fxprice = $cache['payprice'] - $cache['yf'];
            $fxlog['ctime'] = time();
            $fx1rate = self::$CMS['shopset']['fx1rate'] / 100;
            $fx2rate = self::$CMS['shopset']['fx2rate'] / 100;
            $fx3rate = self::$CMS['shopset']['fx3rate'] / 100;
            $fxtmp = array(); //缓存3级数组
            if ($pid) {
            //第一层分销
            $fx1 = $mvip->where('id=' . $pid)->find();
            if ($fx1['isfx'] && $fx1rate) {
            $fxlog['fxyj'] = $fxprice * $fx1rate;
            $fx1['money'] = $fx1['money'] + $fxlog['fxyj'];
            $fx1['total_xxbuy'] = $fx1['total_xxbuy'] + 1; //下线中购买产品总次数
            $fx1['total_xxyj'] = $fx1['total_xxyj'] + $fxlog['fxyj']; //下线贡献佣金
            $rfx = $mvip->save($fx1);
            $fxlog['from'] = $vip['id'];
            $fxlog['fromname'] = $vip['nickname'];
            $fxlog['to'] = $fx1['id'];
            $fxlog['toname'] = $fx1['nickname'];
            if (FALSE !== $rfx) {
            //佣金发放成功
            $fxlog['status'] = 1;
            } else {
            //佣金发放失败
            $fxlog['status'] = 0;
            }
            //单层逻辑
            //$rfxlog=$mfxlog->add($fxlog);
            //file_put_contents('./Data/app_debug.txt','日志时间:'.date('Y-m-d H:i:s').PHP_EOL.'纪录信息:'.$rfxlog.PHP_EOL.PHP_EOL.$mfxlog->getLastSql().PHP_EOL.PHP_EOL,FILE_APPEND);
            array_push($fxtmp, $fxlog);
            }
            //第二层分销
            if ($fx1['pid']) {
            $fx2 = $mvip->where('id=' . $fx1['pid'])->find();
            if ($fx2['isfx'] && $fx2rate) {
            $fxlog['fxyj'] = $fxprice * $fx2rate;
            $fx2['money'] = $fx2['money'] + $fxlog['fxyj'];
            $fx2['total_xxbuy'] = $fx2['total_xxbuy'] + 1; //下线中购买产品人数计数
            $fx2['total_xxyj'] = $fx2['total_xxyj'] + $fxlog['fxyj']; //下线贡献佣金
            $rfx = $mvip->save($fx2);
            $fxlog['from'] = $vip['id'];
            $fxlog['fromname'] = $vip['nickname'];
            $fxlog['to'] = $fx2['id'];
            $fxlog['toname'] = $fx2['nickname'];
            if (FALSE !== $rfx) {
            //佣金发放成功
            $fxlog['status'] = 1;
            } else {
            //佣金发放失败
            $fxlog['status'] = 0;
            }
            //单层逻辑
            //$rfxlog=$mfxlog->add($fxlog);
            //file_put_contents('./Data/app_debug.txt','日志时间:'.date('Y-m-d H:i:s').PHP_EOL.'纪录信息:'.$rfxlog.PHP_EOL.PHP_EOL.$mfxlog->getLastSql().PHP_EOL.PHP_EOL,FILE_APPEND);
            array_push($fxtmp, $fxlog);
            }
            }
            //第三层分销
            if ($fx2['pid']) {
            $fx3 = $mvip->where('id=' . $fx2['pid'])->find();
            if ($fx3['isfx'] && $fx3rate) {
            $fxlog['fxyj'] = $fxprice * $fx3rate;
            $fx3['money'] = $fx3['money'] + $fxlog['fxyj'];
            $fx3['total_xxbuy'] = $fx3['total_xxbuy'] + 1; //下线中购买产品人数计数
            $fx3['total_xxyj'] = $fx3['total_xxyj'] + $fxlog['fxyj']; //下线贡献佣金
            $rfx = $mvip->save($fx3);
            $fxlog['from'] = $vip['id'];
            $fxlog['fromname'] = $vip['nickname'];
            $fxlog['to'] = $fx3['id'];
            $fxlog['toname'] = $fx3['nickname'];
            if (FALSE !== $rfx) {
            //佣金发放成功
            $fxlog['status'] = 1;
            } else {
            //佣金发放失败
            $fxlog['status'] = 0;
            }
            //单层逻辑
            //$rfxlog=$mfxlog->add($fxlog);
            //file_put_contents('./Data/app_debug.txt','日志时间:'.date('Y-m-d H:i:s').PHP_EOL.'纪录信息:'.$rfxlog.PHP_EOL.PHP_EOL.$mfxlog->getLastSql().PHP_EOL.PHP_EOL,FILE_APPEND);
            array_push($fxtmp, $fxlog);
            }
            }
            //多层分销
            if (count($fxtmp) >= 1) {
            $refxlog = $mfxlog->addAll($fxtmp);
            if (!$refxlog) {
            file_put_contents('./Data/app_fx_error.txt', '错误日志时间:' . date('Y-m-d H:i:s') . PHP_EOL . '错误纪录信息:' . $rfxlog . PHP_EOL . PHP_EOL . $mfxlog->getLastSql() . PHP_EOL . PHP_EOL, FILE_APPEND);
            }
            }

            //花鼓分销方案
            $allhg = $mvip->field('id')->where('isfxgd=1')->select();
            if ($allhg) {
            $tmppath = array_slice(explode('-', $vip['path']), -20);
            $tmphg = array();
            foreach ($allhg as $v) {
            array_push($tmphg, $v['id']);
            }
            //需要计算的花鼓
            $needhg = array_intersect($tmphg, $tmppath);
            if (count($needhg)) {
            $fxlog['oid'] = $cache['id'];
            $fxlog['fxprice'] = $fxprice;
            $fxlog['ctime'] = time();
            $fxlog['fxyj'] = $fxprice * 0.05;
            $fxlog['from'] = $vip['id'];
            $fxlog['fromname'] = $vip['nickname'];
            foreach ($needhg as $k => $v) {
            $hg = $mvip->where('id=' . $v)->find();
            if ($hg) {
            $rhg = $mvip->where('id=' . $v)->setInc('money', $fxlog['fxyj']);
            if ($rhg) {
            $fxlog['to'] = $hg['id'];
            $fxlog['toname'] = $hg['nickname'] . '[花股收益]';
            $rehgfxlog = $mfxlog->add($fxlog);
            }
            }
            }
            }
            }
            }*/

            $mlog = model('Shop_order_log');
            $dlog['oid'] = $cache['id'];
            $dlog['msg'] = '确认收货,交易完成。';
            $dlog['ctime'] = time();
            $rlog = $mlog->add($dlog);

            //后端日志
            $mlog = model('Shop_order_syslog');
            $dlog['oid'] = $cache['id'];
            $dlog['msg'] = '交易完成-后台点击';
            $dlog['type'] = 5;
            $dlog['paytype'] = $cache['paytype'];
            $dlog['ctime'] = time();
            $rlog = $mlog->add($dlog);
            //$this->success('交易已完成，感谢您的支持！');
            $info['status'] = 1;
            $info['msg'] = '后台确认收货操作完成！';
        } else {
            //后端日志
            $mlog = model('Shop_order_syslog');
            $dlog['oid'] = $cache['id'];
            $dlog['msg'] = '确认收货失败';
            $dlog['type'] = -1;
            $dlog['paytype'] = $cache['paytype'];
            $dlog['ctime'] = time();
            $rlog = $mlog->add($dlog);
            //$this->error('确认收货失败，请重新尝试！');
            $info['status'] = 0;
            $info['msg'] = '后台确认收货操作失败，请重新尝试！';
        }
        $this->ajaxReturn($info);
    }

    //订单退货
    public function orderTuihuo()
    {
        $map['id'] = I('id');
        $cache = model('Shop_order')->where($map)->find();
        $this->assign('cache', $cache);
        $mb = $this->fetch();
        $this->ajaxReturn($mb);
    }

    public function orderTuihuoSave()
    {
        $data = I('post.');
        if (!$data) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取数据！';
            $this->ajaxReturn($info);
        }
        $m = model('Shop_order');
        $mlog = model('Shop_order_log');
        $mslog = model('Shop_order_syslog');
        $mvip = model('Vip');
        $cache = $m->where('id=' . $data['id'])->find();
        if (!$cache) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取订单数据！';
            $this->ajaxReturn($info);
        }
        if (!$cache) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取此订单数据！';
            $this->ajaxReturn($info);
        }
        //追入会员信息
        $vip = $mvip->where('id=' . $cache['vipid'])->find();
        if (!$vip) {
            $info['status'] = 0;
            $info['msg'] = '未正常获取此订单的会员信息！';
            $this->ajaxReturn($info);
        }
        switch ($cache['status']) {
            case '4':
                $data['status'] = 7;
                $data['tuihuotime'] = time();
                if (!$data['tuihuoprice']) {
                    $info['status'] = 0;
                    $info['msg'] = '退货金额不能为空！';
                    $this->ajaxReturn($info);
                }
                $re = $m->where('id=' . $data['id'])->save($data);
                if (FALSE !== $re) {
                    $vip['money'] = $vip['money'] + $data['tuihuoprice'];
                    $rvip = $mvip->save($vip);
                    if ($rvip !== FALSE) {
                        //前端LOG
                        $log['oid'] = $cache['id'];
                        $log['msg'] = '成功退货，自动退款' . $data['tuihuoprice'] . '元至用户余额-成功';
                        $log['ctime'] = time();
                        $rlog = $mlog->add($log);
                        $log['type'] = 6;
                        $log['paytype'] = $cache['paytype'];
                        $rslog = $mslog->add($log);
                        //后端LOG
                        $info['status'] = 1;
                        $info['msg'] = '关闭订单成功！自动退款' . $data['tuihuoprice'] . '元至用户余额成功!';
                    } else {
                        //前端LOG
                        $log['oid'] = $cache['id'];
                        $log['msg'] = '成功退货，自动退款' . $data['tuihuoprice'] . '元至用户余额-失败!请联系客服!';
                        $log['ctime'] = time();
                        $rlog = $mlog->add($log);
                        //后端LOG
                        $log['type'] = -1;
                        $log['paytype'] = $cache['paytype'];
                        $rslog = $mslog->add($log);
                        $info['status'] = 1;
                        $info['msg'] = '成功退货，自动退款' . $data['tuihuoprice'] . '元至用户余额失败!请联系技术！';
                    }

                } else {
                    //前端LOG
                    $log['oid'] = $cache['id'];
                    $log['msg'] = '订单退货失败';
                    $log['ctime'] = time();
                    $rlog = $mlog->add($log);
                    //后端LOG
                    $log['type'] = -1;
                    $log['paytype'] = $cache['paytype'];
                    $rslog = $mslog->add($log);
                    $info['status'] = 0;
                    $info['msg'] = '订单退货失败！';
                }
                $this->ajaxReturn($info);
                break;
            default:
                $info['status'] = 0;
                $info['msg'] = '只有未付款和已付款订单可以关闭!';
                $this->ajaxReturn($info);
                break;
        }
        //$info['status']=0;
        //$info['msg']='通讯失败，请重新尝试!';
        //$this->ajaxReturn($info);

    }

    public function orderExport()
    {
        $id = I('id');
        $status = I('status');
        if ($id) {
            $map['id'] = array('in', in_parse_str($id));
        } else {
            $map['status'] = $status;
        }
        if($status==-1){
            unset($map['status']);
        }
        switch ($status) {
            case 0:
                $tt = "交易取消";
                break;
            case 1:
                $tt = "未付款";
                break;
            case 2:
                $tt = "已付款";
                break;
            case 3:
                $tt = "已发货";
                break;
            case 4:
                $tt = "退货中";
                break;
            case 7:
                $tt = "退货完成";
                break;
            case 5:
                $tt = "交易成功";
                break;
            case 6:
                $tt = "交易关闭";
                break;
        }
        $data = model('Shop_order')->where($map)->select();
        //dump($data);
        //die();
        foreach ($data as $k => $v) {
            //过滤字段
            unset($data[$k]['sid']);
            unset($data[$k]['ispay']);
            unset($data[$k]['kfmsg']);
            unset($data[$k]['vipxqname']);
            unset($data[$k]['vipxqid']);
            unset($data[$k]['ntime']);
            unset($data[$k]['dtime']);
            unset($data[$k]['etime']);
            switch ($v['status']) {
                case 0:
                    $data[$k]['status'] = "交易取消";
                    break;
                case 1:
                    $data[$k]['status'] = "未付款";
                    break;
                case 2:
                    $data[$k]['status'] = "已付款";
                    break;
                case 3:
                    $data[$k]['status'] = "已发货";
                    break;
                case 4:
                    $data[$k]['status'] = "退货中";
                    break;
                case 7:
                    $data[$k]['status'] = "退货完成";
                    break;
                case 5:
                    $data[$k]['status'] = "交易成功";
                    break;
                case 6:
                    $data[$k]['status'] = "交易关闭";
                    break;
            }
            $data[$k]['ctime'] = date('Y-m-d H:i:s', $v['ctime']);
            $data[$k]['paytime'] = $v['paytime'] ? date('Y-m-d H:i:s', $v['paytime']) : '无';
            $data[$k]['changetime'] = $v['changetime'] ? date('Y-m-d H:i:s', $v['changetime']) : '无';
            $data[$k]['closetime'] = $v['closetime'] ? date('Y-m-d H:i:s', $v['closetime']) : '无';
            $data[$k]['tuihuosqtime'] = $v['tuihuosqtime'] ? date('Y-m-d H:i:s', $v['tuihuosqtime']) : '无';
            $data[$k]['tuihuotime'] = $v['tuihuotime'] ? date('Y-m-d H:i:s', $v['tuihuotime']) : '无';
            $tmpitems = unserialize($v['items']);
            $str = "";
            foreach ($tmpitems as $vv) {
                $vt = '品名：' . $vv['name'] . ' 属性：' . $vv['skuattr'] . '数量：' . $vv['num'] . '单价：' . $vv['price'];
                $str = $str . $vt . '/***/';
            }
            $data[$k]['items'] = $str;
        }
        //dump($data);
        //die();
        $title = array('ID', '订单编号', '代金卷ID', '订单总价', '商品总数', '支付价格', '支付类型', '支付时间', '支付宝支付帐号', '邮费', '会员ID', '会员微信ID', '收货姓名', '收货电话', '收货地址', '购买留言', '订单创建时间', '改价时间', '改价原因', '改价操作员', '关闭时间', '关闭原因', '关闭操作员', '退货退款金额', '退货退款申请时间', '退货退款完成时间', '退货快递公司', '退货快递单号', '退货原因', '退货操作员', '订单状态', '发货快递', '发货快递号', '订单商品详情');
        Vendor("PHPExcel.Excel#class");
        \Excel::export($data, $title, $tt . '订单' . date('Y-m-d H:i:s', time()));
        // $this->exportexcel($data, $title, $tt . '订单' . date('Y-m-d H:i:s', time()));
    }

    //CMS后台标签列表
    public function label()
    {
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '标签列表',
                'url' => U('Admin/Shop/label'),
            ),
        );
        $this->assign('breadhtml', $this->getBread($bread));
        //绑定搜索条件与分页
        $m = model('Shop_label');
        $p = $_GET['p'] ? $_GET['p'] : 1;
        $name = I('name') ? I('name') : '';
        if ($name) {
            $map['name'] = array('like', "%$name%");
            $this->assign("name", $name);
        }
        $psize = self::$CMS['set']['pagesize'] ? self::$CMS['set']['pagesize'] : 20;
        $cache = $m->where($map)->page($p, $psize)->select();
        $count = $m->where($map)->count();
        $this->getPage($count, $psize, 'App-loader', '标签列表', 'App-search');
        $this->assign('cache', $cache);
        $this->display();
    }

    //CMS后台标签设置
    public function labelSet()
    {
        $id = I('id');
        $m = model('Shop_label');
        //设置面包导航，主加载器请配置
        $bread = array(
            '0' => array(
                'name' => '商城首页',
                'url' => U('Admin/Shop/index'),
            ),
            '1' => array(
                'name' => '标签列表',
                'url' => U('Admin/Shop/label'),
            ),
            '2' => array(
                'name' => '标签设置',
                'url' => $id ? U('Admin/Shop/lebelSet', array('id' => $id)) : U('Admin/Shop/lebelSet'),
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

    public function labelDel()
    {
        $id = $_GET['id']; //必须使用get方法
        $m = model('Shop_label');
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