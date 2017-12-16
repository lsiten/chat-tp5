<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"/var/www/api/chat-tp5/public/../application/admin/view/wx/template.html";i:1513016737;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>模版消息配置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet"> 
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>
                        模版消息配置
                    </h5>
                </div>
                <div class="ibox-content">
                    <div>
                        <p style="color:red">
                                * 模板消息类型有：订单支付成功通知（OPENTM200444326）；订单发货通知（OPENTM201541214）；成为会员通知（OPENTM203264949）
                        </p>
                        <p style="color:red">
                                * 更新保存会自动在公众号上添加相对应的模板消息ID，手动保存可以通过手动编辑相应的模板消息ID
                        </p>
                        <p style="color:red">
                                * 由于微信只提供了添加模板消息的接口，所以在此处添加的模板消息若多于15，则会添加失败，需要到微信公众号后台进行删除
                        </p>
                        <p style="color:red">
                                * 微信模板消息内行业设置必须为 消费品-消费品
                        </p>
                    </div>
                    <div class="hr-line-dashed"></div>
                    <form class="form-horizontal m-t" id="commentForm" method="post">
                        <?php if(is_array($cache) || $cache instanceof \think\Collection): $i = 0; $__LIST__ = $cache;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <div class="form-group">
                                <label class="col-sm-4 control-label"><?php echo $vo['position']; ?>（<?php echo $vo['templateidshort']; ?>）：</label>
                                <div class="col-sm-5">
                                    <input id="<?php echo $vo['templateidshort']; ?>" type="text" class="form-control" placeholder="Template_ID" value="<?php echo $vo['templateid']; ?>">
                                </div>
                                <div class="col-sm-1">
                                    <div class="btn btn-default" data-short="<?php echo $vo['templateidshort']; ?>" onclick="refreshtemplateid(this)">更新保存</div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="btn btn-default" data-short="<?php echo $vo['templateidshort']; ?>" onclick="savetemplateid(this)">手动保存</div>
                                </div>
                            </div>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script src="/static/admin/js/jquery.form.js"></script>
<script src="/static/admin/js/toastr/toastr.js"></script>
<script src="/static/admin/js/beyond.min.js"></script>
<script src="/static/admin/js/appapi.js"></script>
<script type="text/javascript">
function refreshtemplateid(o){
    var object = $(o);
    var shortid = object.data('short');
    if(!shortid){
        $.App.alert('danger','通信失败！');
        return false;
    }else{
        $.ajax({
            type: 'post',
            data: {
                'shortid': shortid,
            },
            url: "<?php echo url('Admin/Wx/templateRemoteSet'); ?>",
            async: false,
            dataType: 'json',
            success: function(e) {
                $('#'+e.shortid).val(e.templateid);
                $.App.alert('ok', e.msg);
                return false;
            },
            error: function() {
                $.App.alert('danger', '通讯失败！');
            }
        });
        return false;
    }

}
function savetemplateid(o){
    var object = $(o);
    var shortid = object.data('short');
    var templateid = $('#'+shortid).val();
    if(!shortid){
        $.App.alert('danger','通信失败！');
        return false;
    }else{
        $.ajax({
            type: 'post',
            data: {
                'shortid': shortid,
                'templateid': templateid,
            },
            url: "<?php echo url('Admin/Wx/templateSet'); ?>",
            async: false,
            dataType: 'json',
            success: function(e) {
                $.App.alert('ok', e.msg);
                return false;
            },
            error: function() {
                $.App.alert('danger', '通讯失败！');
            }
        });
        return false;
    }
}
</script>
</body>
</html>