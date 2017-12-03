<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:89:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/product/edit.html";i:1512281949;s:90:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/public/editor.html";i:1512216083;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>产品修改</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
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
                <div class="ibox-title">
                        <h5>产品修改</h5>
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
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('product/edit'); ?>" enctype="multipart/form-data" >
                        <div class="form-group">
                            <label class="col-sm-3 control-label">产品标题：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" value="<?php echo $item['title']; ?>" type="text" name="title" id="title" required="" aria-required="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">所属栏目：</label>
                            <div class="col-sm-4 input-group">
                                <select name="cid" class="form-control" required="" aria-required="true">
                                        <option value="0">请选择</option>
                                            <?php if(is_array($category) || $category instanceof \think\Collection): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fcat): $mod = ($i % 2 );++$i;?>
                                                <option <?php if($item["cid"] == $fcat["id"]): ?>selected<?php endif; ?> value="<?php echo $fcat['id']; ?>"> <?php echo $fcat['name']; ?></option>
                                            <!--二级栏目-->
                                            <?php if(isset($fcat['children'])): if(is_array($fcat['children']) || $fcat['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $fcat['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$scat): $mod = ($i % 2 );++$i;?>
                                                    <option <?php if($item["cid"] == $scat["id"]): ?>selected<?php endif; ?> value="<?php echo $scat['id']; ?>">- <?php echo $scat['name']; ?></option>
                                                <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">产品品牌：</label>
                                <div class="input-group col-sm-6">
                                    <input class="form-control" value="<?php echo $item['brand']; ?>" type="text" name="brand" id="brand" required="" aria-required="true"/>
                                </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">产品单位：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" value="<?php echo $item['units']; ?>" placeholder="例：kg，个" type="text" name="units" id="units" required="" aria-required="true"/>
                            </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">简单描述：</label>
                                <div class="input-group col-sm-6">
                                     <textarea class="form-control"  name="description" id="description" cols="30" rows="5"><?php echo $item['description']; ?></textarea>
                                </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">产品价格：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" placeholder="0.00" value="<?php echo $item['price']; ?>" type="text" name="price" id="price" required="" aria-required="true"/>
                                <span style="color:red;">*单位：元</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">市场价格：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" placeholder="0.00" value="<?php echo $item['marketprice']; ?>" type="text" name="marketprice" id="marketprice" required="" aria-required="true"/>
                                <span style="color:red;">*单位：元</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">产品描述：</label>
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
                                <label class="col-sm-3 control-label">产品图片：</label>
                                <div class="input-group col-sm-6">
                                        <a class="inputButton <?php if($item["litpic"]): ?>hidebuton<?php endif; ?>" id="imgInput">
                                            <input type="file" class="upload-input" name="litpic" id="litpic" onchange="previewFile()"/>
                                        </a>
                                        <?php if($item["litpic"]): ?>
                                            <img src="<?php echo $item['litpic']; ?>" id="previewEdit" alt="图片预览" onclick="changeImg()">
                                        <?php endif; ?>
                                        <img src="" id="preview" alt="图片预览" onclick="changeImg()">
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">关键字：</label>
                                <div class="input-group col-sm-6">
                                    <input class="form-control" type="text" value="<?php echo $item['keywords']; ?>" name="keywords" id="keywords" required="" aria-required="true"/>
                                </div>
                        </div>
                        <div class="form-group">
                                <label class="col-sm-3 control-label">发布时间：</label>
                                <div class="input-group col-sm-6">
                                    <input class="form-control date-picker" value="<?php echo date('Y-m-d',$item['publishtime']); ?>" type="text" name="publishtime" id="publishtime" required="" aria-required="true"/>
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
        var previewEdit = $("#previewEdit");
        var file  = $("#litpic").prop('files')[0];
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