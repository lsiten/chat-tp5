<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:85:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/vip/card.html";i:1513330316;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>卡券列表</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet">             
   <style>
        .ibox-title span{cursor: pointer}
        .tab-content{ padding-top:20px;}
    </style>
</head>

<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>卡券列表</h5>
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
                                <button style="color:#fff" class="btn btn-outline btn-primary addCard" type="button">新增卡券</button>
                                <button style="color:#fff" class="btn btn-outline btn-success sendCard" type="button">发送卡券</button>
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
                                            <th data-field="checkbox">
                                                <div class="checkbox" style="margin-bottom: 0px; margin-top: 0px;">
                                                    <label style="padding-left: 4px;"> 
                                                        <input type="checkbox" class="App-checkall colored-blue">
                                                        <span class="text"></span>
                                                    </label>                                    
                                                </div>
                                            </th>
                                            <th data-field="id">ID</th>
                                            <th data-field="type_text">类型</th>
                                            <th data-field="cardno">卡券编号</th>
                                            <th data-field="cardpwd">卡券密码</th>
                                            <th data-field="money_text">金额</th>
                                            <th data-field="time_text">有效期</th>
                                            <th data-field="usemoney_text">使用规则</th>
                                            <th data-field="ctime_text">创建时间</th>
                                            <th data-field="status_text">状态</th>
                                            <th data-field="vipid">所属会员ID</th>
                                            <th data-field="usetime_text">使用时间</th>
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
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/admin/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="/static/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script src="/static/admin/js/toastr/toastr.js"></script>
<script src="/static/admin/js/beyond.min.js"></script>
<script src="/static/admin/js/appapi.js"></script>
<script src="/static/admin/js/bootbox/bootbox.js"></script> 
<script type="text/javascript">
var RootPath = "__COMMON__";
	$("#cusTable").on('click','.App-check',function(e){
            if($(e).is(":checked")){
                $(e).removeAttr("checked");
            }else{
                $(e).prop("checked","checked");
            }  
    });
    $("#cusTable").on('click','tr',function(e){
            var tr = $(e.target).parent('tr');   
            var c=$(tr).find("input[type=checkbox]");
            if($(c).is(":checked")){
                $(c).removeAttr("checked");
            }else{
                $(c).prop("checked","checked");
            } 
    });
    //全选
	$(".example").on('click','.App-checkall',function(){
        if($(this).is(":checked")){			
			$('.App-check').prop("checked","checked");
		}else{
			$('.App-check').removeAttr("checked");
		}
	});
   function initTable() {    
        //先销毁表格
        $('#cusTable').bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable").bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "./card", //获取数据的地址
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
                    type: <?php echo $type; ?>,
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

        $(".addCard").click(function(){
            //iframe层
            layer.open({
                type: 2,
                title: '新增卡券',
                shadeClose: true,
                shade: false,
                maxmin: false, //开启最大化最小化按钮
                area: ['850px', '460px'],
                content: "<?php echo url('vip/cardadd'); ?>"
            });
        });

        //发送给会员
	$(".sendCard").on('click', function () {
		var checks=$(".App-check:checked");
		if(checks.length==0){
			$.App.alert('danger','请选择要发送的卡券！');
			return false;
		}
		if(checks.length>1){
			$.App.alert('danger','单次仅能发送一张卡券！');
			return false;
		}
		var cardid=checks.val();
		var cardstatus=$('#status'+cardid).text();
		if(cardstatus!='生成'){
			$.App.alert('danger','此卡券不能发放！');
			return false;
		}
		if($('#type'+cardid).text()=='充值卡'){
			$.App.alert('danger','充值卡暂不支持线上发放！');
			return false;
		}
		var carddetail=$('#money'+cardid).text()+"元"+$('#type'+cardid).text()+"　发送给：";
		
		
        bootbox.prompt(carddetail, function (result) {
            if (result != null) {
                var data={'cardid':cardid,'vipid':result};
                var tourl="<?php echo url('/admin/vip/sendcard'); ?>";
            	$.App.ajax('post',tourl,data,function(){
                    initTable();
                });
            }
        });
        
    });

    });

    function cardDel(id){


        layer.confirm('确认删除此卡券吗?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON('./cardDel', {'id' : id}, function(res){
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
</script>

</body>

</html>