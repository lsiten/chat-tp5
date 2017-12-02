<?php 
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
   
    $nav = $nav->select();
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
 * 根据树形结构数据生成树形图,最低三层
 * @param array $data 要转换的数据集
 * @return array
 */
function create_tree_dom($category){
    $data = [];
    foreach($category as $row){
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
        $data[] = $row;
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
