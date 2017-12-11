<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:90:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/wx/keywordadd.html";i:1512972659;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加关键字</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
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

                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('wx/keywordadd'); ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">触发关键词：</label>
                            <div class="col-sm-4 input-group">
                                <input class="form-control" placeholder="必填，全局唯一" type="text" name="keyword" id="keyword" required="" aria-required="true"/>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">选择类型：</label>
                            <div class="col-sm-4 input-group">
                                <select name="type" class="form-control" required="" aria-required="true">
                                    <option value="1" title="纯文本">纯文本</option>
                                    <option value="2" title="单图文">单图文</option>
                                    <!-- <option value="3" title="多图文">多图文</option> -->
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">名称[图文有效]：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" placeholder="选填" type="text" name="name" id="name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">封面[图文有效]：</label>
                            <div class="input-group col-sm-6">
                                <div class="col-sm-6">
                                    <a class="inputButton" id="imgInput">
                                        <input type="file" class="upload-input" name="pic" id="pic" required="" aria-required="true" onchange="previewFile()"/>
                                    </a>
                                    <img src="" id="preview" alt="图片预览" onclick="changeImg()">
                                </div>
                            </div>
                        </div>
                        <div class="form-group adver">
                            <label class="col-sm-3 control-label">关键词描述：</label>
                            <div class="input-group col-sm-6">
                                <textarea class="form-control"  name="summary" id="summary" cols="30" rows="5"></textarea>
                            </div>
                        </div>
                        <div class="form-group adver">
                            <label class="col-sm-3 control-label">超链接：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" placeholder="选填" type="text" name="url" id="url"/>
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
            var file  = $("#pic").prop('files')[0];
            var reader = new FileReader();
            reader.onloadend = function () {
                preview.attr("src",reader.result);
                 imgInput.hide();
                 preview.show();
            }
            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.attr("src","");
            }
      }
      function changeImg(){
        $("#pic").click();
      }
</script>
</body>
</html>