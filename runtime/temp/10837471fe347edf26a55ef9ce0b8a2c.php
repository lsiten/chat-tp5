<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:66:"/var/www/api/chat-tp5/public/../application/admin/view/wx/set.html";i:1513016737;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信设置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <!-- Sweet Alert -->
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
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
        <div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>微信设置</h5>
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
                        <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('wx/set'); ?>" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信名称[推送]</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxname']; ?>" id="wxname" type="text" class="form-control" name="wxname" required="required" aria-required="true">                                             
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信首次关注描述[推送]</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxsummary']; ?>" id="wxsummary" type="text" class="form-control" name="wxsummary" required="required" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <label class="col-sm-3 control-label">开启首次关注图片[推送]</label>
                                            <div id="wxswitch-group" class="input-group col-sm-4">
                                                <div class="radio i-checks col-sm-4">
                                                    <label for="wxswitch_0">
                                                        <input type="radio" id="wxswitch_0"  value="1" <?php if($cache['wxswitch'] == 1): ?>checked<?php endif; ?> name="wxswitch"> <i></i> 是</label>
                                                </div>
                                                <div class="radio i-checks col-sm-4">
                                                    <label for="wxswitch_1">
                                                        <input type="radio" id="wxswitch_1" <?php if($cache['wxswitch'] == 0): ?>checked<?php endif; ?> value="0" name="wxswitch"> <i></i> 否</label>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="form-group" id="wxpicture-group" <?php if($cache['wxswitch'] == 0): ?>style="display:none"<?php endif; ?>>
                                        <label class="col-sm-3 control-label">微信首次关注图片[推送]</label>
                                        <div class="input-group col-sm-6">
                                            <div class="col-sm-6">
                                                <a class="inputButton <?php if($cache["wxpicture"]): ?>hidebuton<?php endif; ?>" id="imgInput">
                                                    <input type="file" class="upload-input" name="wxpicture" id="wxpicture" onchange="previewFile()"/>
                                                </a>
                                                <?php if($cache["wxpicture"]): ?>
                                                    <img src="<?php echo $cache['wxpicture']; ?>" id="previewEdit" alt="图片预览" onclick="changeImg()">
                                                <?php endif; ?>
                                                <img src="" id="preview" alt="图片预览" onclick="changeImg()">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                            <label class="col-sm-3 control-label">开启微信oAuth调试模式</label>
                                            <div class="input-group col-sm-4">
                                                <div class="radio i-checks col-sm-4">
                                                    <label for="wxdebug_0">
                                                        <input type="radio" id="wxdebug_0" <?php if($cache['wxdebug'] == 1): ?>checked<?php endif; ?> value="1" name="wxdebug"> <i></i> 是</label>
                                                </div>
                                                <div class="radio i-checks col-sm-4">
                                                    <label for="wxdebug_1">
                                                        <input type="radio" id="wxdebug_1" value="0" <?php if($cache['wxdebug'] == 0): ?>checked<?php endif; ?> name="wxdebug"> <i></i> 否</label>
                                                </div>
                                            </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信URL前缀[推送]</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxurl']; ?>" id="wxurl" type="text" class="form-control" name="wxurl" required="required" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信TOKEN</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxtoken']; ?>" id="wxtoken" type="text" class="form-control" name="wxtoken" required="required" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-sm-offset-3 input-group col-sm-9">
                                            <p id="callback-url" class="text-primary"><?php echo $cache['wxurl']; ?>/index/wx/index/token/<?php echo $cache['wxtoken']; ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信APPID</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxappid']; ?>" id="wxappid" type="text" class="form-control" name="wxappid" required="required" aria-required="true">                                
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信APPSECRET</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxappsecret']; ?>" id="wxappsecret" type="text" class="form-control" name="wxappsecret" required="required" aria-required="true">                                
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信支付MchID</label>
                                        <div class="input-group col-sm-4">
                                            <input placeholder="必填" value="<?php echo $cache['wxmchid']; ?>" id="wxmchid" type="text" class="form-control" name="wxmchid"  aria-required="true">                                
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label class="col-sm-3 control-label">微信支付MchKey</label>
                                        <div class="input-group col-sm-4">
                                            <input  placeholder="必填" value="<?php echo $cache['wxmchkey']; ?>" id="wxmchkey" type="text" class="form-control" name="wxmchkey"  aria-required="true">                                
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
<script src="/static/admin/js/jquery.form.js"></script>
<script type="text/javascript">

    //表单验证
    $(document).ready(function(){
        $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});
        var options = {
            beforeSubmit:showStart,
            success:showSuccess
        };
        $('#commentForm').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });
        $("#wxswitch-group label").each(function(){
           $(this).click(function(){
               var wxswitch_v = $(this).find("input[name=wxswitch]").val();
               if(wxswitch_v == 1)
               {
                    $("#wxpicture-group").show();
               }
               else{
                    $("#wxpicture-group").hide();
               }
           })
        });
        $("#wxswitch-group .iCheck-helper").each(function(){
           $(this).click(function(){
              var wxswitch_v = $(this).parent().find("input[name=wxswitch]").val();
              if(wxswitch_v == 1)
               {
                    $("#wxpicture-group").show();
               }
               else{
                    $("#wxpicture-group").hide();
               }
           })
        });
    });
    var index = '';
    function showStart(){
        index = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
        return true;
    }
    function showSuccess(data){
        layer.close( index );
        if( 1 == data.code ){

            layer.alert( data.msg, {'icon' : 1});
        }else{
            layer.alert( data.msg, {'icon' : 2} );
        }
    }
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

    function previewFile() {
            var preview = $("#preview");
            var imgInput = $("#imgInput");
            var previewEdit = $("#previewEdit");
            var file  = $("#wxpicture").prop('files')[0];
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
        $("#wxpicture").click();
      }
</script>
</body>
</html>