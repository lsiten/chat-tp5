<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:95:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/index/appimgviewer.html";i:1513084215;}*/ ?>
<div style="text-align: center; width: 570px; overflow: hidden;">
<?php if(is_array($cache) || $cache instanceof \think\Collection): $i = 0; $__LIST__ = $cache;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
	<img src="__UPLOAD__/<?php echo $vo['savepath']; ?><?php echo $vo['savename']; ?>" width="100%" /><br><br>	
<?php endforeach; endif; else: echo "" ;endif; ?>
</div>