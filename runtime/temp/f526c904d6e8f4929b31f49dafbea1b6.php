<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:90:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/product/index.html";i:1512282304;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品列表</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <style>
        .padding-style{
            padding: 0px 20px;
        }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <div class="row padding-style">
                <h5>产品列表</h5>
                <div class="form-group pull-right">
                    <a href="<?php echo url('product/add',['cid'=>$id]); ?>" class="btn btn-outline btn-primary addBanner" type="button">添加产品</a>
                </div>
            </div>
        </div>
        <div class="ibox-content">
             <!--搜索框开始-->
             <form id='commentForm' role="form" method="post" class="form-inline">
                    <div class="content clearfix m-b">
                        <div class="form-group">
                            <label>关键字：</label>
                            <input type="text" placeholder="产品名称" class="form-control" id="title" name="title">
                            <input type="text" placeholder="开始时间" class="form-control date-picker" id="start_time" name="start_time">
                            <input type="text" placeholder="结束时间" class="form-control date-picker" id="end_time" name="end_time">
                            <select class="form-control" name="cat_id" id="cat_id">
                                <option value="0">未分类</option>
                                <?php if(is_array($category) || $category instanceof \think\Collection): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fcat): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $fcat['id']; ?>" <?php if($id == $fcat['id']): ?>selected<?php endif; ?>> <?php echo $fcat['name']; ?></option>
                                <!--二级分类-->
                                <?php if(isset($fcat['children'])): if(is_array($fcat['children']) || $fcat['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $fcat['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$scat): $mod = ($i % 2 );++$i;?>
                                <option value="<?php echo $scat['id']; ?>" <?php if($id == $scat['id']): ?>selected<?php endif; ?>>&nbsp;&nbsp;├&nbsp;&nbsp;<?php echo $scat['name']; ?></option>
                                    <!--三级分类-->
                                    <?php if(isset($scat['children'])): if(is_array($scat['children']) || $scat['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $scat['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tcat): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo $tcat['id']; ?>" <?php if($id == $tcat['id']): ?>selected<?php endif; ?>>&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;<?php echo $tcat['name']; ?></option>
                                    <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-primary" type="button" style="margin-top:5px" id="search"><strong>搜 索</strong>
                            </button>
                        </div>
                    </div>
            </form>
           
            <div class="hr-line-dashed"></div>
            <div class="example-wrap">
                <div class="example">
                    <table id="cusTable" data-height="850">
                        <thead>
                        <th data-field="id">编号</th>
                        <th data-field="title">产品名称</th>
                        <th data-field="click">点击量</th>
                        <th data-field="name">产品分类</th>
                        <th data-field="publishtime">添加日期</th>
                        <th data-field="operate">操作</th>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- End Example Pagination -->
        </div>
    </div>
</div>
<!-- End Panel Other -->
</div>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.js"></script>
<script src="/static/admin/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="/static/admin/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
   
    function initTable() {
        //先销毁表格
        $('#cusTable').bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable").bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "./index", //获取数据的地址
            striped: true,  //表格显示条纹
            pagination: true, //启动分页
            pageSize: 10,  //每页显示的记录数
            pageNumber:1, //当前第几页
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表
            sidePagination: "server", //表示服务端请求
            //设置为undefined可以获取pageNumber，pageSize，searchText，sortName，sortOrder
            //设置为limit可以获取limit, offset, search, sort, order
            queryParamsType : "undefined",
            queryParams: function queryParams(params) {   //设置查询参数
                var param = {
                    pageNumber: params.pageNumber,
                    pageSize: params.pageSize,
                    searchText:$('#title').val(),
                    startTime:$('#start_time').val(),
                    endTime:$('#end_time').val(),
                    cat_id:$('#cat_id').val()
                };
                return param;
            },
            onLoadSuccess: function(){  //加载成功时执行
                layer.msg("加载成功", {time : 1000});
            },
            onLoadError: function(){  //加载失败时执行
                layer.msg("加载数据失败");
            }
        });
    }

    $(document).ready(function () {
        //调用函数，初始化表格
        initTable();
        //当点击查询按钮的时候执行
        $("#search").bind("click", initTable);
    });

    // function edit(id){
    //     //iframe层
    //     layer.open({
    //         type: 2,
    //         title: '编辑友情链接',
    //         shadeClose: true,
    //         shade: false,
    //         maxmin: false, //开启最大化最小化按钮
    //         area: ['850px', '520px'],
    //         content: '/index.php/admin/comment/edit/id/'+id

    //     });
    // }
    
//置顶
    function topit(id,flag){
        var index = '';
        prex=flag?"置顶":"取消置顶";
        index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
        $.getJSON('/index.php/admin/product/topit', {'id' : id,'flag':flag}, function(res){
                layer.close( index );
                if(res.code == 1){
                    layer.msg(prex+'成功',{
                        icon: 1,
                        time: 1000 //2秒关闭（如果不配置，默认是3秒）
                    }, function(){
                        initTable();
                    });
                }else{
                    layer.msg(prex+'失败');
                }
            });
    }
    function del(id){


        layer.confirm('确认删除此产品?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON('/index.php/admin/product/dele', {'id' : id}, function(res){
                if(res.code == 1){

                    layer.alert('删除成功', function(){
                        initTable();
                    });
                }else{
                    layer.alert('删除失败');
                }
            });

            layer.close(index);
        })

    }

    $.datetimepicker.setLocale('ch');//设置中文
    $(".date-picker").datetimepicker({
      format:"Y-m-d H:i:s",      //格式化日期
      timepicker:false,    //关闭时间选项
     // value:new Date()
    });
</script>
</body>
</html>