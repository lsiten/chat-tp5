<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:88:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/vip/cardadd.html";i:1513329238;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加卡券</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link rel="stylesheet" href="/static/admin/js/plugins/datetime/daterangepicker-bs3.css">
    <style>
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">

                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('vip/cardadd'); ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">选择卡券类型：</label>
                            <div class="col-sm-4 input-group">
                                <select class="form-control" name="type" id="type">
                                    <option value="">请选择类型</option>
                                    <!-- <option value="1" <eq name="cache.type" value="1">selected</eq>>充值卡</option> -->
                                    <option value="2">代金券</option>
                                </select>
                            </div>
                        </div>
                       
                        <div class="form-group">
                            <label class="col-sm-3 control-label">卡券金额：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="money" id="money"/>
                                <p style="color:red;">*使用卡券时，金额必须小于订单总金额</p>                                
                            </div>
                        </div>
                        <div class="form-group adver">
                            <label class="col-sm-3 control-label">有效期：</label>
                            <div class="input-group col-sm-6">
                                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                                <input class="form-control" type="text" name="usetime" id="usetime"/>
                            </div>
                        </div>   
                         <div class="form-group">
                            <label class="col-sm-3 control-label">使用规则：</label>
                            <div class="input-group col-sm-6">
                                    <span class="input-group-addon">订单金额满</span>
                                    <input type="text" class="form-control" name="usemoney">
                                    <span class="input-group-addon">元可使用</span>                            
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label"></label>
                            <div class="col-sm-6 left15">
                                <span><sup style="font-size:1em">*使用卡券时，金额必须小于订单总金额</sup></span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">生成数量：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="num" id="num"/>
                                <span class="input-group-addon">张</span>
                            </div>
                        </div>                  
                        
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-3">
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
<script src="/static/admin/js/plugins/datetime/moment.js"></script>
<script src="/static/admin/js/plugins/datetime/daterangepicker.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script src="/static/admin/js/jquery.form.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        var options = {
            beforeSubmit:showStart,
            success:showSuccess
        };
        $('#commentForm').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });

    });
    var index = '';
    function showStart(){
        index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
        return true;
    }
    var index2 = '';
    function showSuccess(data){
        layer.close( index );
        if( 1 == data.code ){

            layer.alert( data.msg, {'icon' : 1}, function(){
                window.parent.initTable();
                setTimeout(closeLayer(), 1000);
            });
        }else{
            layer.alert( data.msg, {'icon' : 2} );
        }
    }

    function closeLayer(){
        index2 = parent.layer.getFrameIndex(window.name); //先得到当前iframe层的索引
        parent.layer.close(index2); //再执行关闭
    }
    $('#usetime').daterangepicker();
</script>
</body>
</html>