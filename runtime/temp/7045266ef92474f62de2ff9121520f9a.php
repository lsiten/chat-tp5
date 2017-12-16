<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:70:"/var/www/api/chat-tp5/public/../application/admin/view/shop/goods.html";i:1513175448;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品管理</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet">     
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
            <h5>商品管理</h5>
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
                                <a href="<?php echo url('shop/goodsadd'); ?>" style="color:#fff;" class="btn btn-outline btn-primary addLabel" type="button">新增商品</a>
                            </div>
                            <!--搜索框开始-->
                            <form id='commentForm' role="form" method="post" class="form-inline">
                                <div class="content clearfix m-b">
                                    <div class="form-group">
                                        <label>关键字：</label>
                                        <input type="text" class="form-control" id="title" name="title">
                                    </div>
                                    <div class="form-group">
                                        <button class="btn btn-primary" type="button" style="margin-top:5px" id="search"><strong>搜 索</strong>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <!--搜索框结束-->
                            <div class="hr-line-dashed"></div>
                            <div class="example-wrap">
                                <div class="example">
                                    <table id="cusTable" data-height="850">
                                        <thead>
                                            <th data-field="id">ID</th>
                                            <th data-field="spu">SPU</th>
                                            <th data-field="cid">分类名称</th>
                                            <th data-field="name">商品名称</th>
                                            <th data-field="unit">商品单位</th>
                                            <th data-field="num">商品库存</th>
                                            <th data-field="price">商品单价</th>
                                            <th data-field="oprice">商品原价</th>
                                            <th data-field="clicks">商品点击</th>
                                            <th data-field="sells">商品销量</th>
                                            <th data-field="sorts">商品排序</th>
                                            <th data-field="status_html">上下架</th>
                                            <th data-field="issku_html">SKU管理</th> 
                                            <th data-field="operate">操作</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>

            </div>
        </div>
    </div>
</div>


<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/toastr/toastr.js"></script>
<script src="/static/admin/js/beyond.min.js"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/admin/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="/static/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
var RootPath = "__COMMON__";
   function initTable() {
        //先销毁表格
        $('#cusTable').bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable").bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "./goods", //获取数据的地址
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
                    type: 1,
                    searchText:$('#title').val()
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

    function setGoodsStatus(id,status){
         $.getJSON('./goodsStatus', {'id' : id,'status':status}, function(res){
                if(res.code == 1){
                    Notify(res.msg, 'top-right', '5000', "success", 'fa-bolt', true);
                    initTable();
                }else{
                    layer.alert('删除失败');
                }
            });
    }

    function goodsdel(id){


        layer.confirm('确认删除此商品吗?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON('./goodsdel', {'id' : id}, function(res){
                if(res.code == 1){
                    Notify(res.msg, 'top-right', '5000', "success", 'fa-bolt', true);
                    initTable();
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