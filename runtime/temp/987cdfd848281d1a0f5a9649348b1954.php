<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:94:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/upload/getmoreimg.html";i:1513084566;}*/ ?>
<?php if(is_array($cache) || $cache instanceof \think\Collection): $i = 0; $__LIST__ = $cache;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
	<div class="imgwp" data-id = "<?php echo $vo['id']; ?>" data-check = "0" onclick="checkupload(this);">
		<img src="__UPLOAD__/<?php echo $vo['savepath']; ?><?php echo $vo['savename']; ?>" />
		<div class="cover"></div>
	</div>	
<?php endforeach; endif; else: echo "" ;endif; ?>
