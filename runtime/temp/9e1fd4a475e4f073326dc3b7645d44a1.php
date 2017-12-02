<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:87:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/page/index.html";i:1512218363;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>lsiten后台管理系统</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <style>
        .ibox-title span{cursor: pointer}
        .tab-content{ padding-top:20px;}
    </style>
</head>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>单页面管理</h5>
             <div class="ibox-tools">
                <a class="collapse-link">
                    <i class="fa fa-chevron-up"></i>
                </a>
                <a class="dropdown-toggle" data-toggle="dropdown" href="table_basic.html#">
                    <i class="fa fa-wrench"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="table_basic.html#">选项1</a>
                    </li>
                    <li><a href="table_basic.html#">选项2</a>
                    </li>
                </ul>
                <a class="close-link">
                    <i class="fa fa-times"></i>
                </a>
            </div>
        </div>
        <div class="ibox-content">
            <div class="row">
                <div class="form-group pull-right">
                    <a href="<?php echo url('page/add'); ?>" class="btn btn-outline btn-primary addBanner" type="button">添加单页面</a>
                </div>
            </div>
            <div class="hr-line-dashed"></div>
            <div class="row">
                <?php if(is_array($pages) || $pages instanceof \think\Collection): $i = 0; $__LIST__ = $pages;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$page): $mod = ($i % 2 );++$i;?>
                    <div class="col-sm-3 page-box">
                        <div class="page-content">
                            <h3><?php echo $page['name']; ?></h3>
                            <p><?php echo $page['ename']; ?></p>
                            <div>
                            <a href="<?php echo url('page/edit',['id' => $page['id']]); ?>">编 辑</a> | <a class="del" onclick="pageDel(<?php echo $page['id']; ?>)">删 除</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </div>
        </div>
    </div>
</div>

<style>
    .page-box{
        padding: 5px;
        text-align: center;
       
    }
    .page-content{
        padding: 30px 5px;
        background: #f5f5f5;
        border-radius: 3px;
        border: 1px dashed #ccc;
    }
    .page-box h3{
        padding: 5px 0px;
    }
    .page-box p{
        color:#999;
    }
    .page-box a{
        color:#999;
        padding: 5px 10px;
    }
</style>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/admin/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="/static/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
 

    function pageDel(id){

        layer.confirm('确认删除此单页面吗?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON('./dele', {'id' : id}, function(res){
                if(res.code == 1){
                    layer.alert('删除成功', function(){
                    });
                }else{
                    layer.alert('删除失败');
                }
            });

            layer.close(index);
        })

    }
</script>

</body>

</html>