<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:91:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/shop/groupedit.html";i:1513067830;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>广告设置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <style>
        .hidebuton{
            display: none !important;
        }
        .upload-input{
            opacity: 0;
            width: 88px;
            height: 75px;
        }
        #previewEdit{
           display: inline-block;
           background: #D0EEFF;
            border: 1px solid #d4e3ec;
            border-radius: 4px;
            overflow: hidden;
            line-height: 20px;
            width: 110px;
            height: 110px;
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

                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('shop/groupedit'); ?>" enctype="multipart/form-data">
                        <div class="form-group">
                                <label class="col-sm-3 control-label">分组名称：</label>
                                <div class="col-sm-4 input-group">
                                    <input class="form-control" value="<?php echo $item['name']; ?>" placeholder="必填" type="text" name="name" id="name" required="" aria-required="true"/>                                
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">首页图标：</label>
                                <div class="input-group col-sm-6">
                                    <div class="col-sm-6">
                                        <a class="inputButton <?php if($item["icon"]): ?>hidebuton<?php endif; ?>" id="imgInput">
                                            <input type="file" class="upload-input" name="icon" id="icon" onchange="previewFile()"/>
                                        </a>
                                        <?php if($item["icon"]): ?>
                                            <img src="<?php echo $item['icon']; ?>" id="previewEdit" alt="图片预览" onclick="changeImg()">
                                        <?php endif; ?>
                                        <img src="" id="preview" alt="图片预览" onclick="changeImg()">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">分组商品：</label>
                                <div class="input-group col-sm-6">
                                    <input value="<?php echo $item['goods']; ?>" placeholder="多个商品用','分割" class="form-control" required="" aria-required="true" type="text" name="goods" id="goods"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">分组备注：</label>
                                <div class="input-group col-sm-6">
                                    <textarea class="form-control"   name="summary" id="summary" cols="30" rows="5"><?php echo $item['summary']; ?></textarea>
                                </div>
                            </div>                    
                        
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-3">
                                <input type="hidden" name="id" value="<?php echo $item['id']; ?>" />                                                                        
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

    function previewFile() {
            var preview = $("#preview");
            var imgInput = $("#imgInput");
            var previewEdit = $("#previewEdit");
            var file  = $("#icon").prop('files')[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.attr("src",reader.result);
                 imgInput.hide();
                 previewEdit.hide();
                 preview.show();
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.attr("src","");
            }
      }
      function changeImg(){
        $("#icon").click();
      }
</script>
</body>
</html>