<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"/var/www/api/chat-tp5/public/../application/admin/view/wx/customer.html";i:1513016737;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>客服消息配置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet"> 
    <style>
        .upload-input{
            opacity: 0;
            width: 88px;
            height: 75px;
        }
        #preview{
            display: none;
            background: #D0EEFF;
            border: 1px solid #d4e3ec;
            border-radius: 4px;
            overflow: hidden;
            line-height: 20px;
            width: 110px;
            height: 110px;
        }
        .inputButton{
            display: inline-block;
            background: #D0EEFF;
            border: 1px solid #d4e3ec;
            border-radius: 4px;
            overflow: hidden;
            line-height: 20px;
            background: url(/static/admin/images/image.png) center no-repeat;
            width: 110px;
            height: 110px;
        }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>
                        客服消息配置
                    </h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post">
                        <?php if(is_array($cache) || $cache instanceof \think\Collection): $i = 0; $__LIST__ = $cache;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><?php echo $vo['position']; ?>：</label>
                                <?php if($vo['type'] == 'up'): ?>
                                    <label class="col-sm-3 control-label"><strong>[昵称]通过您的推广，成为了您的[层级]，</strong></label>
                                    <div class="col-sm-4">
                                        <input id="data-<?php echo $vo['id']; ?>" type="text" class="form-control" placeholder="客服接口消息" value="<?php echo $vo['value']; ?>">
                                    </div>
                                <?php elseif($vo['type'] == 'emp'): ?>
                                    <label class="col-sm-3 control-label"><strong>[昵称]通过您推广，成为了您的[分销商]，</strong></label>
                                    <div class="col-sm-4">
                                        <input id="data-<?php echo $vo['id']; ?>" type="text" class="form-control" placeholder="客服接口消息" value="<?php echo $vo['value']; ?>">
                                    </div>
                                <?php else: ?>
                                    <div class="col-sm-7">
                                        <input id="data-<?php echo $vo['id']; ?>" type="text" class="form-control" placeholder="客服接口消息" value="<?php echo $vo['value']; ?>">
                                    </div>
                                <?php endif; ?>
                                <div class="col-sm-1">
                                    <div class="btn btn-default" data-id="<?php echo $vo['id']; ?>" onclick="save(this)">保存</div>
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
function save(o) {
    var object = $(o);
    var id = object.data('id');
    var value = $('#data-' + id).val();
    if (!id) {
        $.App.alert('danger','通信失败！');
        return false;
    } else {
        $.ajax({
            type: 'post',
            data: {
                'id': id,
                'value': value,
            },
            url: "<?php echo url('Admin/Wx/customerSet'); ?>",
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