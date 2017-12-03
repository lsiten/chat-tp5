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



