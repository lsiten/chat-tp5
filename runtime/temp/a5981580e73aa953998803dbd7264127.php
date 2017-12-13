<?php if (!defined('THINK_PATH')) exit(); /*a:2:{s:90:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/shop/goodsadd.html";i:1513093995;s:90:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/public/editor.html";i:1512216083;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品添加</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet">   
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
    <script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
    <style>
        .upload-input{
            opacity: 0;
            width: 88px;
            height: 75px;
        }
        #preview,#preview2,#preview3{
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
                    <h2>商品添加</h2>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('shop/goodsadd'); ?>" enctype="multipart/form-data">
                        <div class="form-group">
                            <label class="col-sm-3 control-label">选择分类：</label>
                            <div class="col-sm-4 input-group">
                                <select class="form-control" name="cid">
                                    <option value="0">顶级分类</option>
                                    <?php if(is_array($cate) || $cate instanceof \think\Collection): $i = 0; $__LIST__ = $cate;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?></option>
                                        <?php if(isset($vo['_child'])): if(is_array($vo['_child']) || $vo['_child'] instanceof \think\Collection): $i = 0; $__LIST__ = $vo['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo2): $mod = ($i % 2 );++$i;?>
                                                <option value="<?php echo $vo2['id']; ?>">&nbsp;&nbsp;└<?php echo $vo2['name']; ?></option>
                                                <?php if(isset($vo2['_child'])): if(is_array($vo2['_child']) || $vo2['_child'] instanceof \think\Collection): if( count($vo2['_child'])==0 ) : echo "" ;else: foreach($vo2['_child'] as $key=>$vo3): ?>
                                                        <option value="<?php echo $vo3['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└<?php echo $vo3['name']; ?></option>
                                                        <?php if(isset($vo3['_child'])): if(is_array($vo3['_child']) || $vo3['_child'] instanceof \think\Collection): if( count($vo3['_child'])==0 ) : echo "" ;else: foreach($vo3['_child'] as $key=>$vo4): ?>
                                                                <option value="<?php echo $vo4['id']; ?>" >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└<?php echo $vo4['name']; ?></option>
                                                                <?php if(isset($vo4['_child'])): if(is_array($vo4['_child']) || $vo4['_child'] instanceof \think\Collection): if( count($vo4['_child'])==0 ) : echo "" ;else: foreach($vo4['_child'] as $key=>$vo5): ?>
                                                                        <option value="<?php echo $vo5['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;└<?php echo $vo5['name']; ?></option>
                                                                    <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">商品名称：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="name" id="name"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">首页大图片：</label>
                            <div class="input-group col-sm-6">
                                <div class="col-sm-6">
                                    <a class="inputButton" id="imgInput">
                                        <input required="" aria-required="true" type="file" class="upload-input" name="indexpic" id="indexpic" onchange="previewFile()"/>
                                    </a>
                                    <img src="" id="preview" alt="图片预览" onclick="changeImg()">
                                    <p style="color:#d4e3ec">尺寸：675*320px</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">首页列表图片：</label>
                            <div class="input-group col-sm-6">
                                <div class="col-sm-6">
                                    <a class="inputButton" id="imgInput2">
                                        <input required="" aria-required="true" type="file" class="upload-input" name="listpic" id="listpic" onchange="previewFile2()"/>
                                    </a>
                                    <img src="" id="preview2" alt="图片预览" onclick="changeImg2()">
                                    <p style="color:#d4e3ec">尺寸：335*260px</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">商品图片：</label>
                            <div class="input-group col-sm-6">
                                <div class="col-sm-6">
                                    <a class="inputButton" id="imgInput3">
                                        <input required="" aria-required="true" type="file" class="upload-input" name="pic" id="pic" onchange="previewFile3()"/>
                                    </a>
                                    <img src="" id="preview3" alt="图片预览" onclick="changeImg3()">
                                    <p style="color:#d4e3ec">尺寸：720*400px</p>
                                </div>
                            </div>
                        </div> 
                         <div class="form-group">
                            <label class="col-sm-3 control-label">商品图集：</label>
                            <div class="input-group col-sm-6">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="album" id="App-album">
                                        <span class="input-group-btn">
                                            <button class="btn btn-azure shiny" type="button" onclick="appImgviewer('App-album')"><i class="fa fa-camera-retro"></i>预览</button>
                                            <button class="btn btn-azure shiny" type="button" onclick="appImguploader('App-album',true)"><i class="glyphicon glyphicon-picture"></i>上传</button>
                                        </span>
                                    </div>
                            </div>
                        </div>     
                        <div class="form-group">
                            <label class="col-sm-3 control-label">商品单位：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="unit" id="unit"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">商品价格：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="price" id="price"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">商品原价：</label>
                            <div class="input-group col-sm-6">
                                <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="oprice" id="oprice"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">分销佣金：</label>
                            <div class="input-group col-sm-6"></div>
                                <div class="col-sm-2">
                                    <div class="input-group input-group-xs">
                                        <span class="input-group-btn">
                                            <button class="btn btn-darkorange" type="button">一级(%)：</button>
                                        </span>
                                        <input name="fx1rate" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group input-group-xs">
                                        <span class="input-group-btn">
                                            <button class="btn btn-darkorange" type="button">二级(%)：</button>
                                        </span>
                                        <input name="fx2rate" type="text" class="form-control">
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="input-group input-group-xs">
                                        <span class="input-group-btn">
                                            <button class="btn btn-darkorange" type="button">三级(%)：</button>
                                        </span>
                                        <input name="fx3rate" type="text" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">商品库存：</label>
                                <div class="input-group col-sm-6">
                                    <input class="form-control" required="" aria-required="true" placeholder="必填" type="text" name="num" id="num"/>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">启用SKU：</label>
                                <div class="input-group col-sm-6">
                                    <label>
                                        <input type="hidden" name="issku"  id="issku">
                                        <input class="checkbox-slider slider-icon colored-darkorange" type="checkbox" id="isskubtn">
                                        <span class="text darkorange">&nbsp;&nbsp;&larr;重要：启用后将采用商品SKU模式管理库存，价格与销量。</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">是否免邮费：</label>
                                <div class="input-group col-sm-6">
                                    <label>
                                        <input type="hidden" name="ismy"  id="ismy">
                                        <input class="checkbox-slider slider-icon colored-darkorange" type="checkbox" id="ismybtn">
                                        <span class="text darkorange">&nbsp;&nbsp;&larr;重要：启用后纯免邮商品免邮费。</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">开启自定义销量：</label>
                                <div class="input-group col-sm-6">
                                    <label>
                                        <input type="hidden" name="issells"  id="issells">
                                        <input class="checkbox-slider slider-icon colored-darkorange" type="checkbox" id="issellsbtn">
                                        <span class="text darkorange">&nbsp;&nbsp;&larr;重要：开启后前端显示自定义销量。</span>
                                    </label>
                                </div>
                            </div>
                            <div class="form-group" id="dissells">
                                <label class="col-sm-3 control-label">自定义销量：</label>
                                <div class="input-group col-sm-6">
                                    <input type="text" class="form-control" name="dissells" placeholder="填写自定义销量，此销量也会自动增长">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">开启每天返现功能：</label>
                                <div class="input-group col-sm-6">
                                    <label>
                                        <input type="hidden" name="cashback"  id="cashback">
                                        <input class="checkbox-slider slider-icon colored-darkorange" type="checkbox" id="cashbackbtn">
                                        <span class="text darkorange">&nbsp;&nbsp;&larr;重要：开启后可每天返现。</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-group" id="backratio">
                                <label class="col-sm-3 control-label">自定义返现比例：</label>
                                <div class="input-group col-sm-6">
                                    <input type="text" class="form-control" name="backratio" placeholder="例如0.01">
                                </div>
                            </div> 

                            <div class="form-group">
                                <label class="col-sm-3 control-label">选择标签</label>
                                <div class="input-group col-sm-6">
                                    <?php if(is_array($label) || $label instanceof \think\Collection): $i = 0; $__LIST__ = $label;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo_l): $mod = ($i % 2 );++$i;?>
                                        <label>
                                            <input type="checkbox" class="colored-blue label-check" value="<?php echo $vo_l['id']; ?>" data-label="<?php echo $vo_l['name']; ?>">
                                            <span class="text"><?php echo $vo_l['name']; ?>&nbsp;&nbsp;&nbsp;&nbsp;</span>
                                        </label>
                                    <?php endforeach; endif; else: echo "" ;endif; ?>
                                </div>
                                <input type="hidden" name="lid" id="lid" />
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">商品备注</label>
                                <div class="input-group col-sm-6">
                                    <textarea class="form-control" name="summary" rows="5"></textarea>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">商品排序</label>
                                <div class="input-group col-sm-6">
                                    <input type="text" class="form-control" name="sorts" placeholder="必填" required="" aria-required="true">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">商品详情</label>
                                <div class="input-group col-sm-6">
                                    <!--必须插入空input避免验证冲突-->
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
<script src="/static/admin/js/toastr/toastr.js"></script>
<script src="/static/admin/js/beyond.min.js"></script>
<script src="/static/admin/js/appapi.js"></script>
<script src="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script src="/static/admin/js/jquery.form.js"></script>
<script src="/static/admin/js/bootbox/bootbox.js"></script>
<script type="text/javascript">
    $('.label-check').on("change",function(){
        var lid = '';
        var checks = $('.label-check');
        $(checks).each(function() {
            if ($(this).is(":checked")) {
                lid += $(this).val() + ',';
            }
        });
        $('#lid').val(lid);
    })
if ($('#issellsbtn').prop('checked')) {
    $('#dissells').slideDown();
} else {
    $('#dissells').slideUp();
}
$('#isskubtn').on('click', function() {
    var value = $(this).prop('checked') ? 1 : 0;
    $('#issku').val(value);
});
$('#ismybtn').on('click', function() {
    var value = $(this).prop('checked') ? 1 : 0;
    $('#ismy').val(value);
});
$('#issellsbtn').on('click', function() {
    var value;
    if ($(this).prop('checked')) {
        value = 1;
        $('#dissells').slideDown();
    } else {
        value = 0;
        $('#dissells').slideUp();
    }
    $('#issells').val(value);
});
if ($('#cashbackbtn').prop('checked')) {
    $('#backratio').slideDown();
} else {
    $('#backratio').slideUp();
}
$('#cashbackbtn').on('click', function() {
    var value;
    if ($(this).prop('checked')) {
        value = 1;
        $('#backratio').slideDown();
    } else {
        value = 0;
        $('#backratio').slideUp();
    }
    $('#cashback').val(value);
});


var RootPath = "__COMMON__";
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
            Notify(data.msg, 'top-right', '5000', "success", 'fa-bolt', true);
        }else{
            layer.alert( data.msg, {'icon' : 2} );
        }
    }

    function previewFile() {
            var preview = $("#preview");
            var imgInput = $("#imgInput");
            var file  = $("#indexpic").prop('files')[0];
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
        $("#indexpic").click();
      }


      function previewFile2() {
            var preview = $("#preview2");
            var imgInput = $("#imgInput2");
            var file  = $("#listpic").prop('files')[0];
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
      function changeImg2(){
        $("#listpic").click();
      }


      function previewFile3() {
            var preview = $("#preview3");
            var imgInput = $("#imgInput3");
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
      function changeImg3(){
        $("#pic").click();
      }



      
 //App默认图片预览器
 function appImgviewer(fbid) {
    //fbid 查找带回的文本框ID,全局唯一
    //isall 多图,单图模式
    var ids = $('#' + fbid).val();
    if (!ids) {
        $.App.alert('danger', '您还没有图片可以预览！');
        return false;
    }
    $.ajax({
        type: "post",
        url: "<?php echo url('Admin/Index/appImgviewer'); ?>",
        data: {
            'ids': ids
        },
        dataType: "json",
        success: function(mb) {
            bootbox.dialog({
                message: mb,
                title: "图片预览器",
                className: "modal-darkorange",
                buttons: {
                    success: {
                        label: "确定",
                        className: "btn-blue",
                        callback: function() {}
                    },
                    "取消": {
                        className: "btn-danger",
                        callback: function() {}
                    }
                }
            });
        },
        error: function(xhr) {
            $.App.alert('danger', '通讯失败！请重试！');
        }
    });
    return false;
}




    //App默认图片上传管理器
    function appImguploader(fbid, isall) {
            //fbid 查找带回的文本框ID,全局唯一
            //isall 多图,单图模式
            $.ajax({
                type: "post",
                url: "<?php echo url('Admin/Upload/indeximg'); ?>",
                data: {
                    'fbid': fbid,
                    'isall': isall
                },
                dataType: "json",
                //beforeSend:$.App.loading(),
                success: function(mb) {
                    //$.App.loading();
                    bootbox.dialog({
                        message: mb,
                        title: "图片上传管理器",
                        className: "modal-darkorange",
                        buttons: {
                            "追加": {
                                className: "btn-success",
                                callback: function() {
                                    if (isall == 'false') {
                                        $('#' + fbid).val($('#App-uploader-findback').val());
                                    } else {
                                        $('#' + fbid).val($('#' + fbid).val() + $('#App-uploader-findback').val());
                                    }
                                }
                            },
                            "替换": {
                                className: "btn-blue",
                                callback: function() {
                                    $('#' + fbid).val($('#App-uploader-findback').val());
                                }
                            },
                            "取消": {
                                className: "btn-danger",
                                callback: function() {}
                            }
                        }
                    });
                },
                error: function(xhr) {
                    $.App.alert('danger', '通讯失败！请重试！');
                }
            });
            return false;
        }
</script>
</body>
</html>