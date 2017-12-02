<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:88:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/comment/add.html";i:1512227758;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加/回复留言</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <style>

    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加/回复留言</h5>
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
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('comment/add'); ?>" enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">标题：</label>
                            <div class="col-sm-4 input-group">
                              <input class="form-control" type="text" value="回复：<?php echo $item['title']; ?>@<?php echo $item['username']; ?>"  disabled/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">内容：</label>
                            <div class="input-group col-sm-6">
                                <textarea class="form-control"  name="content" id="content" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-3">
                                    <input type="hidden" name="rid" value="<?php echo $item['id']; ?>" />
                                    <input type="hidden" name="aid" value="<?php echo \think\Session::get('id'); ?>" />
                                    <input type="hidden" name="username" value="<?php echo \think\Session::get('username'); ?>" />
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
<script src="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.js"></script>
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
            });
        }else{
            layer.alert( data.msg, {'icon' : 2} );
        }
    }
   $.datetimepicker.setLocale('ch');//设置中文
   $(".date-picker").datetimepicker({
     format:"Y-m-d",      //格式化日期
     timepicker:false,    //关闭时间选项
     value:new Date()
   });
   $("select[name='type']").change(function(){
        var typeid = $(this).val();
        if(typeid == 2){
            $('.adver').show();
        }else{
            $('.adver').hide();
        }
    });
</script>
</body>
</html>