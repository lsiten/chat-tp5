<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------
use app\admin\model\Category;
// 应用公共文件
/**
 * 生成操作按钮
 * @param array $operate 操作按钮数组
 */
function showOperate($operate = [])
{
    if(empty($operate)){
        return '';
    }
    $option = <<<EOT
<div class="btn-group">
    <button class="btn btn-primary btn-sm dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
        操作 <span class="caret"></span>
    </button>
    <ul class="dropdown-menu">
EOT;

    foreach($operate as $key=>$vo){

        $option .= '<li><a href="'.$vo.'">'.$key.'</a></li>';
    }
    $option .= '</ul></div>';

    return $option;
}

/**
 * 将字符解析成数组
 * @param $str
 */
function parseParams($str)
{
    $arrParams = [];
    parse_str(html_entity_decode(urldecode(trim($str))), $arrParams);
    return $arrParams;
}

/**
 * 子孙树 用于菜单整理
 * @param $param
 * @param int $pid
 */
function subTree($param, $pid = 0)
{
    static $res = [];
    foreach($param as $key=>$vo){

        if( $pid == $vo['pid'] ){
            $res[] = $vo;
            subTree($param, $vo['id']);
        }
    }

    return $res;
}

/**
 * 整理菜单住方法
 * @param $param
 * @return array
 */
function prepareMenu($param)
{
    $parent = []; //父类
    $child = [];  //子类

    foreach($param as $key=>$vo){

        if($vo['typeid'] == 0 ){
            $vo['href'] = !$vo["style"]? url($vo['control_name'] .'/'. $vo['action_name']):'#';
            $parent[] = $vo;
        }else{
            $vo['href'] = url($vo['control_name'] .'/'. $vo['action_name']); //跳转地址
            $child[] = $vo;
        }
        if(60==$vo['id']){
            getAllCategoryMenu($child,60);
        }
    }

    foreach($parent as $key=>$vo){
        foreach($child as $k=>$v){

            if($v['typeid'] == $vo['id']){
                $parent[$key]['child'][] = $v;
            }
        }
    }
    unset($child);

    return $parent;
}

/**
 * 解析备份sql文件
 * @param $file
 */
function analysisSql($file)
{
    // sql文件包含的sql语句数组
    $sqls = array ();
    $f = fopen ( $file, "rb" );
    // 创建表缓冲变量
    $create = '';
    while ( ! feof ( $f ) ) {
        // 读取每一行sql
        $line = fgets ( $f );
        // 如果包含空白行，则跳过
        if (trim ( $line ) == '') {
            continue;
        }
        // 如果结尾包含';'(即为一个完整的sql语句，这里是插入语句)，并且不包含'ENGINE='(即创建表的最后一句)，
        if (! preg_match ( '/;/', $line, $match ) || preg_match ( '/ENGINE=/', $line, $match )) {
            // 将本次sql语句与创建表sql连接存起来
            $create .= $line;
            // 如果包含了创建表的最后一句
            if (preg_match ( '/ENGINE=/', $create, $match )) {
                // 则将其合并到sql数组
                $sqls [] = $create;
                // 清空当前，准备下一个表的创建
                $create = '';
            }
            // 跳过本次
            continue;
        }

        $sqls [] = $line;
    }
    fclose ( $f );

    return $sqls;
}

/*
 * 获取左侧栏目menu
 * @param $child 子栏目
 * @param $typeid 父栏目id
 */
function getAllCategoryMenu(&$child,$typeid=0){
    $nav = Category::with('modelm')
                    ->where(["category.status"=>0])
                    ->order("category.sort DESC")
                    ->select();
   $module = request()->module();
   $data = [];
    foreach ($nav as $val) {
        $row = [];
        $row['typeid'] = $typeid;
        if ($val['type'] == 1){
            $row['href'] = $val['outurl']; //栏目外链模型
        } else {
            $row['href'] = url($module.'/'.$val['modelm']->tablename.'/index', ['id' => $val['id']]);//留言板模型
        }
        if("single"==$val['modelm']->type)
        {
            $row['href'] = url($module.'/page/edit', ['id' => $val['id']]);//留言板模型            
        }
        $row["id"] = $val['id'];
        $row["pid"] = $val['pid'];
        $row["node_name"] = $val['name'];
        $data[] = $row;
    }
    $tree = create_tree($data);
    foreach($tree as $row){
        $child[] = $row; 
        //第一层子栏目
        if(isset($row["children"]))
        { 
            foreach($row["children"] as $child1){
                //第二层子栏目  
                if(isset($child1["children"]))
                {              
                   foreach($child1["children"] as $child2){
                       $child2["node_name"] = "&nbsp;&nbsp;&nbsp;&nbsp;|-".$child2["node_name"];
                       $child[] = $child2;
                   }
                }
               $child1["node_name"] = "&nbsp;&nbsp;|-".$child1["node_name"];
               $child[] = $child1;
           }
        }
    }
}

/*
 * 获取所有导航不进行树形结构
 * @param $status mixed 是否显示||全部
 * @param $limit mixed
 * @param $pid int 父级id 0则为顶级栏目
 * @param $where array|string 查询条件
 */
function getAllCategoryNoTree($status,  $pid = '', $limit = '', $where='')
{
    //导航
    $nav = think\Db::name('category');
    if ($status !== 'all') {
        $nav = $nav->where(['status' => $status]);
    }
    $total = $nav->where($where)->count("id");
    $module = request()->module();
    if ($status !== 'all') {
        $nav = $nav->where(['status' => $status]);
    }
   
    //pid为空则会获取所有导航并且拼装二级，其他值则只能获取该父id下的导航
    if ($pid !== '') {
        $nav = $nav->where('pid',$pid);
    }

    if ($limit != '') {
        $nav = $nav->limit($limit);
    }
    if ($where != '') {
        $nav = $nav->where($where);
    }
   
    $nav = $nav->order("sort DESC")->select();
    $all_nav = [];
    $tree = [];
    //拼接栏目树形结构
    foreach ($nav as $val) {
        //生成url，前端调用
        if ($val['type'] == 1){
            $val['url'] = $val['outurl']; //栏目外链模型
            $val['name'] .= "<button type='button' class='btn btn-default btn-sm btn-red-border'>外链</button>";
        } elseif ($val['modelid'] == 6){
            $val['url'] = url($module.'/guestbook/index', ['cid' => $val['id']]);//留言板模型
        } else {
          $val['url'] = '/home/#/Category/'.$val['id'];  
        }

        //导航的status文本对应
        if ($val['status'] == 0){
            $val['status_text'] = "显示";
        }
        else
        {
            $val['status_text'] = "不显示";
        }
        $val["operate"] = showOperate([
            '编辑' => url('nav/edit', ['id' => $val['id']]),
            '删除' => "javascript:categoryDel('".$val['id']."')"
            ]);
        $all_nav[$val['id']] = $val;
    }

    //返回全部栏目，非树形结构
    if ($pid !== '') {
        return $all_nav;
    }

    return ["total"=>$total,"data"=>$all_nav];
}
/*
 * 获取所有导航
 * @param $status mixed 是否显示||全部
 * @param $limit mixed
 * @param $pid int 父级id 0则为顶级栏目
 * @param $where array|string 查询条件
 */
function getAllCategory($status,  $pid = '', $limit = '', $where='')
{
   
   $data = getAllCategoryNoTree($status,$pid,$limit,$where);
    //树形结构,无限级分类
    $tree = create_tree($data["data"]);

    return ["total"=> $data["total"],"data"=>$tree];
}

/**
 * 根据数据生成树形结构数据
 * @param array $data 要转换的数据集
 * @param int $pk 主键（栏目id）
 * @param string $pid parent标记字段
 * @return array
 */
function create_tree($data,$pk='id',$pid='pid'){
    $tree = $list = [];

    foreach ($data as $val) {
        $list[$val[$pk]] = $val;
    }

    foreach ($list as $key =>$val){     
        if($val[$pid] == 0){      
            $tree[] = &$list[$key];
        }else{
            //找到其父类
            $list[$val[$pid]]['children'][] = &$list[$key];
        }
    }
    return $tree;
}

/**
 * 根据树形结构数据生成树形图,最多三层
 * @param array $data 要转换的数据集
 * @return array
 */
function create_tree_dom($category){
    $data = [];
    foreach($category as $row){
        $data[] = $row;
        //第一层子栏目
        if(isset($row["children"]))
        {
            foreach($row["children"] as $child){
                 //第二层子栏目  
                 if(isset($child["children"]))
                 {              
                    foreach($child["children"] as $child2){
                        $child2["name"] = "&nbsp;&nbsp;&nbsp;&nbsp;|-".$child2["name"];
                        $data[] = $child2;
                    }
                 }
                $child["name"] = "&nbsp;&nbsp;|-".$child["name"];
                $data[] = $child;
            }
        }
    }
    return $data;
}

/**
 * 获取system配置参数
 */
function get_system_value($name=''){
    $value = think\Db::name('system')->where('name',$name)->value('value');
    return $value;
}


/**
 * UTF-8错误信息输出
 * @param  string $msg 错误名称
 * @return null
 * @author App <2094157689@qq.com>
 */
function utf8error($msg)
{
    header("Content-type: text/html; charset=utf-8");
    die($msg);
}


/**
 * 分类路径配置
 * @param  string $m ,$pid 数据表,父id
 * @return null
 * @author App <2094157689@qq.com>
 */
function setPath($m, $pid)
{
    $cate = model($m);
    $map['id'] = $pid;
    $list = $cate->field('id,path')->where($map)->limit(1)->find();
    $path = $list['path'] . '-' . $list['id'];
    $lv = count(explode('-', $path));
    return array('path' => $path, 'lv' => $lv);
}


/**
 * SonCate配置
 * @param  string $m ,$pid 数据表,父id
 * @return null
 * @author App <2094157689@qq.com>
 */
function setSoncate($m, $pid)
{
    $model = model($m);
    $cate = $model;
    $map['pid'] = $pid;
    $father = $cate->where('id=' . $pid)->limit(1)->find();
    $son = $cate->field('id')->where($map)->select();
    if ($son && $father) {
        //存在子栏目
        $arr = '';
        foreach ($son as $k => $v) {
            $arr = $arr . $v['id'] . ',';
        }
        $father['soncate'] = $arr;
        $rf = $father->save();
        if ($rf === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    } elseif (!$son && $father) {
        //子栏目为空
        $father['soncate'] = '';
        $rf = $father->save();
        if ($rf === FALSE) {
            return FALSE;
        } else {
            return TRUE;
        }
    } else {
        //未知错误
        return FALSE;
    }
}



    /**
     * UE编辑器处理
     * @param  string $data 编辑器内容
     * @return null
     * @author App <2094157689@qq.com>
     */
    function trimUE($data)
    {
        $data = stripslashes(htmlspecialchars_decode($data));
        $find = array("<p><br/></p>", "<p>		</p>", "<p>			</p>");
        $data = htmlspecialchars(str_replace($find, "", $data));
        return $data;
    }

    /**
     * AppTree快速无限分类树
     * @param  string $m ,$pid 数据表,父id
     * @return null
     * @author App <2094157689@qq.com>
     */
    function appTree($m, $pid, $field, $map=[], $order = 'sorts desc', $keyid = 'id', $keypid = 'pid', $keychild = '_child')
    {
        $model = model($m);
        $list = $model->where($map)->field($field)->order('sorts desc')->select();
        $data = [];
        if(is_array($list))
        {
           foreach($list as $item){
            $data[] = $item->toArray();
           }
           $data = list_to_tree($data, $keyid, $keypid, $keychild, $root = $pid);
        }
        
        return $data;
    }

    /**
     * 把返回的数据集转换成Tree
     * @param array $list 要转换的数据集
     * @param string $pid parent标记字段
     * @param string $level level标记字段
     * @return array
     * @author 麦当苗儿 <zuojiazi@vip.qq.com>
     */
    function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = '_child', $root = 0)
    {
        // 创建Tree
        $tree = array();
        if (is_array($list)) {
            // 创建基于主键的数组引用
            $refer = array();
            foreach ($list as $key => $data) {
                $refer[$data[$pk]] = &$list[$key];
            }
            foreach ($list as $key => $data) {
                // 判断是否存在parent
                $parentId = $data[$pid];
                if ($root == $parentId) {
                    $tree[] = &$list[$key];
                } else {
                    if (isset($refer[$parentId])) {
                        $parent = &$refer[$parentId];
                        $parent[$child][] = &$list[$key];
                    }
                }
            }
        }
        return $tree;
    }

    /**
     * 将list_to_tree的树还原成列表
     * @param  array $tree 原来的树
     * @param  string $child 孩子节点的键
     * @param  string $order 排序显示的键，一般是主键 升序排列
     * @param  array $list 过渡用的中间数组，
     * @return array        返回排过序的列表数组
     * @author yangweijie <yangweijiester@gmail.com>
     */
    function tree_to_list($tree, $child = '_child', $order = 'id', &$list = array())
    {
        if (is_array($tree)) {
            $refer = array();
            foreach ($tree as $key => $value) {
                $reffer = $value;
                if (isset($reffer[$child])) {
                    unset($reffer[$child]);
                    tree_to_list($value[$child], $child, $order, $list);
                }
                $list[] = $reffer;
            }
            $list = list_sort_by($list, $order, $sortby = 'asc');
        }
        return $list;
    }
/**
 * where in 数组为空时返回不存在的字符例如(-10000000000)
 * @param $value
 * @return string
 */
function in_parse_str($value)
{
    if (!$value) {
        $value = '-10000000000';
    }
    return $value;
}



/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc':    // 正向排序
                asort($refer);
                break;
            case 'desc':    // 逆向排序
                arsort($refer);
                break;
            case 'nat':    // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }
    return false;
}



/**
 * 所有数组的笛卡尔积
 *
 * @param unknown_type $data
 */
function Descartes()
{
    $t = func_get_args();
    if (func_num_args() == 1) {
        return call_user_func_array(__FUNCTION__, $t[0]);
    }

    $a = array_shift($t);
    if (!is_array($a)) {
        $a = array($a);
    }

    $a = array_chunk($a, 1);
    do {
        $r = array();
        $b = array_shift($t);
        if (!is_array($b)) {
            $b = array($b);
        }

        foreach ($a as $p) {
            foreach (array_chunk($b, 1) as $q) {
                $r[] = array_merge($p, $q);
            }
        }

        $a = $r;
    } while ($t);
    return $r;
}
