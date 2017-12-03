<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:88:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/article/add.html";i:1512282562;s:90:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/public/editor.html";i:1512216083;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加文章</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
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
                        <h5>文章添加</h5>
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
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('article/add'); ?>" enctype="multipart/form-data" >
                        <div class="form-group">
                            <label class="col-sm-3 control-label">文章标题：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" type="text" name="title" id="title" required="" aria-required="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">所属栏目：</label>
                            <div class="col-sm-4 input-group">
                                <select name="cid" class="form-control" required="" aria-required="true">
                                        <option value="0">请选择</option>
                                            <?php if(is_array($category) || $category instanceof \think\Collection): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fcat): $mod = ($i % 2 );++$i;?>
                                                <option <?php if($cid == $fcat["id"]): ?>selected<?php endif; ?> value="<?php echo $fcat['id']; ?>"> <?php echo $fcat['name']; ?></option>
                                            <!--二级栏目-->
                                            <?php if(isset($fcat['children'])): if(is_array($fcat['children']) || $fcat['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $fcat['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$scat): $mod = ($i % 2 );++$i;?>
                                                    <option <?php if($cid == $scat["id"]): ?>selected<?php endif; ?> value="<?php echo $scat['id']; ?>">- <?php echo $scat['name']; ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">简单描述：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" type="text" name="description" id="description" required="" aria-required="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">文章内容：</label>
                            <div class="col-sm-6">
                                 <!-- 富文本框 -->
                                    <?php if(get_system_value('site_editor') == 'markdown'): ?>
	<link rel="stylesheet" type="text/css" href="__COMMON__/editor.md/css/editormd.css" />
	<script type="text/javascript" src="__COMMON__/editor.md/editormd.min.js"></script>

	<!--markdown-->
	<div id="markdown">
		<?php if(isset($item['content'])): ?>
			<textarea  style="display:none;" name="content"><?php echo $item['content']; ?></textarea>
		<?php else: ?>
			<textarea  style="display:none;" name="content"></textarea>
		<?php endif; ?>
	</div>
	<script type="text/javascript">
	    $(function() {
	      var Editor = editormd("markdown", {
	            width   : "100%",
	            height  : 540,
                codeFold : true,
                htmlDecode : "style,script,iframe|on*", 
	            syncScrolling : "single",
	            path    : "__COMMON__/editor.md/lib/",
	            emoji : false,
	            imageUpload : true,
                imageFormats : ["jpg", "jpeg", "gif", "png", "bmp"],
                imageUploadURL : "<?php echo url('main/uploadEditor'); ?>",
	        });
	    });   
	</script>
<?php else: ?>
	<script type="text/javascript" src="__JS__/plugins/ueditor/ueditor.config.js"></script><script type="text/javascript" src="__JS__/plugins/ueditor/ueditor.all.min.js"></script><script type="text/javascript" src="__JS__/plugins/ueditor/lang/zh-cn/zh-cn.js"></script>

	<!--ueditor-->
	<textarea id="editor" style="width:780px;height:500px;" name="content">
	 <?php if(isset($item['content'])): ?>
		<?php echo $item['content']; endif; ?>
	</textarea>
	<script type="text/javascript">
	    var ue = UE.getEditor('editor',{
	    	"imageUrl": "<?php echo url('main/uploadEditor'); ?>",
		    "imagePath": "/",
		    "imageFieldName": "editormd-image-file",
		    "imageMaxSize": 2048,
		    "imageAllowFiles": [".png", ".jpg", ".jpeg", ".gif", ".bmp"]
	    });   
	</script>
<?php endif; ?>
                            </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">缩略图：</label>
                                <div class="input-group col-sm-6">
                                   <a class="inputButton" id="imgInput">
                                        <input type="file" class="upload-input" name="litpic" id="litpic" required="" aria-required="true" onchange="previewFile()"/>
                                    </a>
                                    <img src="" id="preview" alt="图片预览" onclick="changeImg()">
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">关键字：</label>
                                <div class="input-group col-sm-6">
                                    <input class="form-control" type="text" name="keywords" id="keywords" required="" aria-required="true"/>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">发布时间：</label>
                                <div class="input-group col-sm-6">
                                    <input class="form-control date-picker" type="text" name="publishtime" id="publishtime" required="" aria-required="true"/>
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
            layer.alert( data.msg, {'icon' : 1});
        }else{
            layer.alert( data.msg, {'icon' : 2} );
        }
    }
    function previewFile() {
            var preview = $("#preview");
            var imgInput = $("#imgInput");
            var file  = $("#litpic").prop('files')[0];
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
        $("#litpic").click();
      }

   $.datetimepicker.setLocale('ch');//设置中文
   $(".date-picker").datetimepicker({
     format:"Y-m-d",      //格式化日期
     timepicker:false,    //关闭时间选项
     value:new Date()
   });
</script>
</body>
</html>