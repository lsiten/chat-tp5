<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/shop/sku.html";i:1513161074;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品SKU管理</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet">         
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
            <h5>当前sku属性</h5>
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
                                <button style="color:#fff" class="btn btn-outline btn-primary addSku" type="button">添加SKU属性</button>
                                <button style="color:#fff" data-id='<?php echo $goodsid; ?>' class="btn btn-outline btn-success saveSku" type="button">保存所有SKU属性</button>
                                <button style="color:#fff" data-id='<?php echo $goodsid; ?>' class="btn btn-outline btn-danger makeSku" type="button">更新生成所有SKU</button>
                            </div>
                            <!--搜索框结束-->
                            <div class="example-wrap">
                                <div class="example">
                                    <table id="cusTable_1">
                                        <thead>
                                            <th data-field="attrlabel">属性名称</th>
                                            <th data-field="items_list">属性值</th>
                                            <th data-field="operate">操作</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
            </div>
        </div>
    </div>

<!-- 商品SKU -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>商品SKU-<?php echo $goodsname; ?></h5>
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
                                    <table id="cusTable_2">
                                        <thead>
                                            <th data-field="id">ID</th>
                                            <th data-field="sku">属性名称</th>
                                            <th data-field="skuattr">属性值</th>
                                            <th data-field="price">价格</th>
                                            <th data-field="num">库存</th>
                                            <th data-field="sells">销量</th>
                                            <th data-field="operate">操作</th>
                                        </thead>
                                    </table>
                                </div>
                            </div>
            </div>
        </div>
    </div>
</div>
<input id='app-sku-has' type="hidden" value="0"/>

<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/toastr/toastr.js"></script>
<script src="/static/admin/js/beyond.min.js"></script>
<script src="/static/admin/js/bootbox/bootbox.js"></script>

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
    function appendAttr(attr,items){
        var tabelBody = $("#cusTable_1>tbody");
        var dataLength = tabelBody.find('tr').length;
        var tr = $('<tr data-index="'+dataLength+'">');
        $(".pagination-info").show();
        $(".pagination-info").text("显示第 1 到第 "+(dataLength+1)+" 条记录，总共 "+(dataLength+1)+" 条记录");
        var data = "<td>"+attr.name+"<input type='hidden' class='dataskuid' value='"+attr.id+"'/><input type='hidden' class='dataskulabel' value='"+attr.name+"'/></td>"
        var itemsHtml = "";
        $.each(items,function(i,item){
            itemsHtml += "<label>";
            itemsHtml += '<input type="checkbox" class="colored-blue App-check" checked value="'+item.path+'" data-label = "'+item.name+'"><span class="text">'+item.name+'</span>';
            itemsHtml += "</label>";
        })
        data += '<td><div class="checkbox" style="margin-bottom: 0px; margin-top: 0px;">'+itemsHtml+'</div></td>';
        data +='<td style=""><button class="App-skuattr-del btn btn-xs btn-darkorange" data-id="'+attr.id+'" data-type="remove">移除此属性</button></td>';
        tabelBody.append(tr.html(data));
        var attrids = $("#app-sku-has").val();
        attrids = attrids+attr.id;
        $("#app-sku-has").val(attrids);
    }
   function initTable(type) {
        //先销毁表格
        $('#cusTable_'+type).bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable_"+type).bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "./sku", //获取数据的地址
            striped: true,  //表格显示条纹
            pagination: true, //启动分页
            pageSize: type==1?1000:10,  //每页显示的记录数,type=1不分页
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
                    type: type,
                    id:<?php echo $goodsid; ?>,
                    searchText:$('#title').val()
                };
                return param;
            },
            onLoadSuccess: function(e){  //加载成功时执行
                if(e.type==1)
                {   
                    var attrids = '';
                     $.each(e.rows,function(i,item){
                        attrids = attrids+item.attrid+',';
                    })
                    $("#app-sku-has").val(attrids);
                }
                layer.msg("加载成功", {time : 1000});
            },
            onLoadError: function(){  //加载失败时执行
                layer.msg("加载数据失败");
            }
        });
    }

   $(document).ready(function () {
        //调用函数，初始化表格
        initTable(1);
        initTable(2);
        //当点击查询按钮的时候执行
        $("#search").bind("click", function(){
            initTable(2);
        });

        $(".addSku").click(function(){
            var url = "<?php echo url('shop/skuloader'); ?>";
            var ids= $("#app-sku-has").val();
            url = url + "?ids="+ids;
            //iframe层
            layer.open({
                type: 2,
                title: '新增sku',
                shadeClose: true,
                shade: false,
                maxmin: false, //开启最大化最小化按钮
                area: ['570px', '300px'],
                content: url
            });
        });

        $(".saveSku").click(function(){
            var id = $(this).data('id');
            var trs = $("#cusTable_1>tbody").find("tr");
			var data="";
			$(trs).each(function(){
				var aid=$(this).find('.dataskuid').val();
				var label=$(this).find('.dataskulabel').val();
				var str='';
                var checks=$(this).find('.App-check');            
				$(checks).each(function(){
					if($(this).is(":checked")){
						str=str+$(this).val()+":"+$(this).data('label')+',';
					}
				});
				data=data+aid+":"+label+"-"+str+";";
            });
            $.getJSON("<?php echo url('shop/skuattrsave'); ?>", {'id' : id,'data':data}, function(res){
                if(res.code == 1){
                    Notify(res.msg, 'top-right', '5000', "success", 'fa-bolt', true);
                    initTable(1);
                }else{
                    layer.alert('更新失败');
                }
            });
        })

        $(".makeSku").click(function(){
            var id = $(this).data('id');
            $.getJSON("<?php echo url('shop/skuattrmake'); ?>", {'id' : id}, function(res){
                if(res.code == 1){
                    Notify(res.msg, 'top-right', '5000', "success", 'fa-bolt', true);
                    initTable(2);
                }else{
                    layer.alert('生成失败');
                }
            });
        })
        $("#cusTable_1").on("click",'.App-skuattr-del',function(e){
            var self = $(e.target);
            self.parent().parent("tr").remove();
            id = self.data("id");
            var attrids = $("#app-sku-has").val();
            var attridsA = trim(attrids).split(",");
            removeByValue(attridsA,id);
            attrids =attridsA.join(",")+",";
            $("#app-sku-has").val(attrids);
            if(attridsA.length>0)
            {
                $(".pagination-info").text("显示第 1 到第 "+attridsA.length+" 条记录，总共 "+attridsA.length+" 条记录");
            }
            else{
                $(".pagination-info").hide();
            }
            e.preventDefault();
        })
        

    });

function skuedit(id){
    //iframe层
    layer.open({
       type: 2,
       title: '编辑广告',
       shadeClose: true,
       shade: false,
        maxmin: false, //开启最大化最小化按钮
        area: ['850px', '520px'],
        content: '/index.php/admin/shop/skuset/id/'+id

    });
}
function removeByValue(arr, val) {
  for(var i=0; i<arr.length; i++) {
    if(arr[i] == val) {
      arr.splice(i, 1);
      break;
    }
  }
}

var trim = function (str) {
    return str.replace(/^,*|,*$/g,'');
  };
</script>

</body>

</html>