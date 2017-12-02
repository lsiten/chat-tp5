<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:84:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/nav/add.html";i:1512190181;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加导航</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <!-- Sweet Alert -->
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加导航</h5>
                    <div class="ibox-tools">
                        <a class="collapse-link">
                            <i class="fa fa-chevron-up"></i>
                        </a>
                        <a class="dropdown-toggle" data-toggle="dropdown" href="form_basic.html#">
                            <i class="fa fa-wrench"></i>
                        </a>
                        <a class="close-link">
                            <i class="fa fa-times"></i>
                        </a>
                    </div>
                </div>
                <div class="ibox-content">
                    <div class="row">
                        <form class="form-horizontal m-t" id="commentForm" method="post" onsubmit="return toVaild()">
                            <div class="panel panel-default col-sm-7" style="padding-right: 0 ;padding-left: 0; margin:10px">
                                <div class="panel-heading">
                                       站内导航
                                 </div>
                                 <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">栏目模型：</label>
                                        <div class="input-group col-sm-4">
                                            <select class="form-control" name="modelid" required="" aria-required="true">
                                                <option value="">请选择栏目模型</option>
                                                <?php if(is_array($model) || $model instanceof \think\Collection): $i = 0; $__LIST__ = $model;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$m): $mod = ($i % 2 );++$i;?>
                                                    <option value="<?php echo $m['id']; ?>" title="<?php echo $m['name']; ?>"><?php echo $m['name']; ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">导航名称：</label>
                                        <div class="input-group col-sm-4">
                                            <input id="name" type="text" class="form-control" name="name" required="required" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">上级分类：</label>
                                        <div class="input-group col-sm-4">
                                            <select class="form-control" name="pid" required="" aria-required="true">
                                                <option value="0">无</option>
                                                <?php if(is_array($category) || $category instanceof \think\Collection): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$c): $mod = ($i % 2 );++$i;?>
                                                    <option value="<?php echo $c['id']; ?>" title="<?php echo $c['name']; ?>"><?php echo $c['name']; ?></option>
                                                    <?php if(isset($c['children'])): if(is_array($c['children']) || $c['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $c['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$n): $mod = ($i % 2 );++$i;?>
                                                            <option value="<?php echo $n['id']; ?>" title="<?php echo $n['name']; ?>"> &nbsp;&nbsp;|- <?php echo $n['name']; ?></option>
                                                        <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">别名（英文）：</label>
                                        <div class="input-group col-sm-4">
                                            <input id="ename" type="text" class="form-control" name="ename" required="required" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <label class="col-sm-3 control-label">是否是外链：</label>
                                            <div class="input-group col-sm-4">
                                                <div class="radio i-checks col-sm-4">
                                                    <label for="type_0">
                                                        <input type="radio" id="type_0"  value="1" name="type"> <i></i> 是</label>
                                                </div>
                                                <div class="radio i-checks col-sm-4">
                                                    <label for="type_1">
                                                        <input type="radio" id="type_1" value="0" checked name="type"> <i></i> 否</label>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">位置：</label>
                                        <div class="input-group col-sm-4">
                                            <div class="radio i-checks col-sm-4">
                                                <label for="position_0">
                                                    <input type="radio" id="position_0" checked value="1" name="position"> <i></i> 主导航</label>
                                            </div>
                                            <div class="radio i-checks col-sm-4">
                                                <label for="position_1">
                                                    <input type="radio" id="position_1" value="2" name="position"> <i></i> 底部</label>
                                            </div>
                                            <div class="radio i-checks col-sm-4">
                                                <label for="position_2">
                                                    <input type="radio" id="position_2" value="3" name="position"> <i></i> 侧边</label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">关键词：</label>
                                        <div class="input-group col-sm-4">
                                            <input id="keywords" type="text" class="form-control" name="keywords"  aria-required="true">                                
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">描述：</label>
                                        <div class="input-group col-sm-4">
                                            <input id="description" type="text" class="form-control" name="description"  aria-required="true">                                
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">排序：</label>
                                        <div class="input-group col-sm-4">
                                            <input id="sort" type="text" class="form-control" name="sort" placeholder="0" size="5" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">是否显示：</label>
                                        <div class="input-group col-sm-4">
                                            <div class="radio i-checks col-sm-4">
                                                <label for="status_0">
                                                    <input type="radio" id="status_0" checked value="0" name="status"> <i></i> 显示</label>
                                            </div>
                                            <div class="radio i-checks col-sm-4">
                                                <label for="status_1">
                                                    <input type="radio" id="status_1" value="1" name="status"> <i></i> 不显示</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- 添加自定义链接 -->
                            <div class="panel panel-default col-sm-4" style="padding-right: 0 ;padding-left: 0; margin:10px">
                                <div class="panel-heading">
                                        添加自定义链接【如果不是外链导航可不填写】
                                 </div>
                                 <div class="panel-body">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">链接地址：</label>
                                        <div class="input-group col-sm-4">
                                            <input id="outurl" type="text" class="form-control" name="outurl" aria-required="true">                                
                                        </div>
                                    </div>
                                    
                                  
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-3">
                                    <!--<input type="button" value="提交" class="btn btn-primary" id="postform"/>-->
                                    <button class="btn btn-primary" type="submit">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/validate/jquery.validate.min.js"></script>
<script src="/static/admin/js/plugins/validate/messages_zh.min.js"></script>
<script src="/static/admin/js/plugins/iCheck/icheck.min.js"></script>
<script src="/static/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/static/admin/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/admin/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">

    //表单提交
    function toVaild(){
        var jz;
        var url = "./add";
        $.ajax({
            type:"POST",
            url:url,
            data:{'data' : $('#commentForm').serialize()},// 你的formid
            async: false,
            beforeSend:function(){
                jz = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
            },
            error: function(request) {
                layer.close(jz);
                swal("网络错误!", "", "error");
            },
            success: function(data) {
                //关闭加载层
                layer.close(jz);
                if(data.code == 1){
                    swal(data.msg, "", "success");
                }else{
                    swal(data.msg, "", "error");
                }

            }
        });

        return false;
    }

    //表单验证
    $(document).ready(function(){
        $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});
    });
    $.validator.setDefaults({
        highlight: function(e) {
            $(e).closest(".form-group").removeClass("has-success").addClass("has-error")
        },
        success: function(e) {
            e.closest(".form-group").removeClass("has-error").addClass("has-success")
        },
        errorElement: "span",
        errorPlacement: function(e, r) {
            e.appendTo(r.is(":radio") || r.is(":checkbox") ? r.parent().parent().parent() : r.parent())
        },
        errorClass: "help-block m-b-none",
        validClass: "help-block m-b-none"
    });

</script>
</body>
</html>