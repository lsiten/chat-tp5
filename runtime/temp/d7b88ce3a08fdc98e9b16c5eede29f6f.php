<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:92:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/upload/indeximg.html";i:1513089622;}*/ ?>
<style>
	#App-uploader-body { overflow: auto}
	#App-uploader-body .imgwp{ float: left; width: 130px; height: 100px; overflow: hidden; text-align: center; margin-left: 10.5px; margin-bottom: 10.5px; border: 1px solid #F5F5F5; position: relative; cursor: pointer;}
	#App-uploader-body .cover{ width: 130px; height: 100px; position: absolute; left: 0px; top:0px; background:url('__IMG__/choosed.png'); display: none;}
	#App-uploader-body .imgwp:hover{border: 1px solid #ED4E2A;}
	#App-uploader-body .imgwp img{width: 100%; height: 100%; vertical-align: middle;}
	#App-uploader-body .group{ position: absolute; right: 0px; top: 0px;}
</style>
<div id="App-uploader">
	<div id="App-uploader-header">
		<div class="hide">
			<iframe name='App-uploader-frame' id="App-uploader-frame"></iframe>
			<form enctype="multipart/form-data" action="<?php echo url('Admin/Upload/doupimg'); ?>" method="post" id="App-uploader-form" target="App-uploader-frame" >
			 	<input type="file" id="App-uploader-file" name="appfile[]" multiple accept="image/*">
		 	</form>
	 	</div>
		<div class="alert alert-success">
			  <button id="App-uploader-getmore" class="btn btn-blue"><i class="glyphicon glyphicon-refresh"></i>加载更多</button>
              <button id="App-uploader-start" class="btn btn-blue"><i class="glyphicon glyphicon-search"></i>选择图片</button>
              <i class="fa-fw fa fa-info"></i>
              <strong>提示：</strong> <span id="App-uploader-result">您还未选择任何图片.</span>
        </div>
        <div class="input-group input-group-sm" style="margin-bottom: 20px;">
        		<input type="text" class="form-control" disabled value="" id="App-uploader-findback" placeholder="点击下方图片自动添加">
           		<span class="input-group-btn">
                	<button class="btn btn-danger" type="button" id="App-uploader-delall"><i class="glyphicon glyphicon-trash"></i>删除图片</button>
                </span>
        </div>
	</div>
	<div id="App-uploader-body" data-page = "2">
		<?php if(is_array($cache) || $cache instanceof \think\Collection): $i = 0; $__LIST__ = $cache;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
			<div class="imgwp" data-id = "<?php echo $vo['id']; ?>" data-check = "0" onclick="checkupload(this);">
				<img src="__UPLOAD__/<?php echo $vo['savepath']; ?><?php echo $vo['savename']; ?>" />
				<div class="cover"></div>
			</div>
		<?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
	<div class="clear"></div>
</div>
<script>
	var fbid="<?php echo $fbid; ?>";
	var isall="<?php echo $isall; ?>"=="true"?true:false;
	var Jupfile=$("#App-uploader-file");
	var Jupresult=$("#App-uploader-result");
	var Jupstart=$("#App-uploader-start");
	var Jupform=$("#App-uploader-form");
	var Jupgetmore=$("#App-uploader-getmore");
	var Jupbody=$('#App-uploader-body');
	var Jupfindback=$('#App-uploader-findback');
	var Jupdelall=$('#App-uploader-delall');
	
	//上传后回调
	function doupimgcallback(info,upval) {
		if(upval){$.App.alert('success',info);$(Jupbody).empty().data('page',1);$(Jupgetmore).trigger('click');$(Jupfindback).val('');}else{$.App.alert('danger',info)};
		if(true){var cfile=$(Jupfile).clone().val("");$(Jupfile).remove();cfile.appendTo(Jupform);$(Jupresult).html('您还未选择任何图片.');}
    }
	//图片选择函数
	function checkupload(obj){
		var id=$(obj).data('id');
		var ischeck=$(obj).data('check');
		var cover=$(obj).find('.cover');
		if(!isall){
			var objs=$(Jupbody).find('.imgwp');
		}
		va=$(Jupfindback).val();
		if(!isall){
			//单图模式
			$(objs).each(function(){
				if($(this).data('check')==1){
					$(this).data('check',0);
					$(this).find('.cover').hide();
				}
			});
			$(Jupfindback).val(id);
			$(obj).data('check',1);
			$(cover).show();
		}else{
			//图集模式
			if(ischeck=='1'){
				$(obj).data('check',0);
				$(Jupfindback).val(va.replace(id+',',''));
				$(cover).hide();
			}else{
				$(obj).data('check',1);			
				$(Jupfindback).val(va+id+',');
				$(cover).show();
			}
		}
		
	}
	//上传变化
	$(Jupfile).on('change',function(){
		$(Jupstart).html('<i class="glyphicon glyphicon-upload"></i>上传图片');
		$(Jupresult).html('您有'+document.getElementById("App-uploader-file").files.length+'等待上传！');
	});
	//上传按钮
	$(Jupstart).on('click',function(){
		var len=document.getElementById("App-uploader-file").files.length;
		if(len){
			$("#App-uploader-form").submit();
		}else{ $(Jupfile).trigger("click");}
		return false;
	});
	//加载更多
	$(Jupgetmore).on('click',function(){
		var p=$(Jupbody).data('page');
		var moreurl="<?php echo url('Admin/Upload/getmoreimg'); ?>";
		var more;
		$.ajax({
			type:"post",			
			data:{'p':p},
			dataType: "json", 
			url:moreurl,
			success:function(info){
				if(info){$(info).appendTo($(Jupbody));$(Jupbody).data('page',(p+1));}else{$.App.alert('success','没有图片了!请上传！');}
			},
			error:function(x){
				$.App.alert('dange','通讯失败！请重试！');
			}
		});
	});
	//删除图片
	$(Jupdelall).on('click',function(){
		var ids = $(Jupfindback).val();
		var delimgurl="<?php echo url('Admin/Upload/delimgs'); ?>";

		$.ajax({
			type:"post",			
			data:{'ids':ids},
			dataType: "json", 
			url:delimgurl,
			success:function(info){

				if (info.status == 1) {
					$.App.alert('success',info.msg);
					$(Jupbody).empty().data('page',1);
					$(Jupgetmore).trigger('click');
					$(Jupfindback).val('');
				} else {
					$.App.alert('dange',info.msg);
				}
			},
			error:function(x){
				$.App.alert('dange','通讯失败！请重试！');
			}
		});		

	});	
</script>