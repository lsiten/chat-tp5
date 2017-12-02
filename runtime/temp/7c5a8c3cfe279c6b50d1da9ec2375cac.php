<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/rulelist/ruleedit.html";i:1511242424;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑采集规则</title>
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
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>编辑采集规则</h5>
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
                    <form class="form-horizontal m-t" id="commentForm" method="post" onsubmit="return toVaild()">
                        <input type="hidden" name="id" value="<?php echo $rule['id']; ?>"/>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">采集规则名称：</label>
                            <div class="input-group col-sm-4">
                                <input id="rulename" type="text" class="form-control" name="rulename" required="" aria-required="true" value="<?php echo $rule['rulename']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">采集站点地址：</label>
                            <div class="input-group col-sm-4">
                                <input id="baseurl" type="url" class="form-control" name="baseurl" required="" aria-required="true" value="<?php echo $rule['baseurl']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">采集列表地址：</label>
                            <div class="input-group col-sm-4">
                                <input id="listurl" type="url" class="form-control" name="listurl" required="" aria-required="true" value="<?php echo $rule['listurl']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">是否批量采集：</label>
                            <div class="input-group col-sm-4">
                                <?php if(is_array($cjstatus) || $cjstatus instanceof \think\Collection): if( count($cjstatus)==0 ) : echo "" ;else: foreach($cjstatus as $key=>$vo): ?>
                                <div class="radio i-checks col-sm-4">
                                    <label>
                                        <input type="radio" value="<?php echo $key; ?>" <?php if($key == $rule['ismore']): ?>checked<?php endif; ?> name="ismore"> <i></i> <?php echo $vo; ?></label>
                                </div>
                                <?php endforeach; endif; else: echo "" ;endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">列表页开始页数：</label>
                            <div class="input-group col-sm-4">
                                <input id="start" type="text" class="form-control" name="start" placeholder="不批量采集可以不填" value="<?php echo $rule['start']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">列表页结束页数：</label>
                            <div class="input-group col-sm-4">
                                <input id="end" type="text" class="form-control" name="end" placeholder="不批量采集可以不填" value="<?php echo $rule['end']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">标题父层规则：</label>
                            <div class="input-group col-sm-4">
                                <input id="titlediv" type="text" class="form-control" name="titlediv" required="" aria-required="true" value="<?php echo $rule['titlediv']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">文章标题规则：</label>
                            <div class="input-group col-sm-4">
                                <input id="title" type="text" class="form-control" name="title" required="" aria-required="true" value="<?php echo $rule['title']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">标题地址规则：</label>
                            <div class="input-group col-sm-4">
                                <input id="titleurl" type="text" class="form-control" name="titleurl" required="" aria-required="true" value="<?php echo $rule['titleurl']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">文章内容规则：</label>
                            <div class="input-group col-sm-4">
                                <input id="body" type="text" class="form-control" name="body" required="" aria-required="true" value="<?php echo $rule['body']; ?>">
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
        var url = "./ruleEdit";
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