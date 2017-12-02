<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:89:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/article/copy.html";i:1512229152;}*/ ?>
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
    <style>

    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加文章</h5>
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
                    <div class="form-group pull-right">
                         <a href="<?php echo url('article/index'); ?>" class="btn btn-outline btn-primary addBanner" type="button">文章列表</a>
                     </div>
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('article/copy'); ?>" enctype="multipart/form-data">

                        <div class="form-group">
                            <label class="col-sm-3 control-label">文章地址：</label>
                            <div class="col-sm-4 input-group">
                                <input class="form-control" type="text" placeholder="请填写带协议头(http://)的完整url地址 "  name="url" id="url" />                                    
                                <p class="help-block" style="color:red">*请输入完整url地址(暂只支持csdn网站文章转载)</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">文章分类：</label>
                            <div class="input-group col-sm-6">
                                <select class="form-control" name="cid">
                                    <option value="0">未分类</option>
                                    <?php if(is_array($category) || $category instanceof \think\Collection): $i = 0; $__LIST__ = $category;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$fcat): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo $fcat['id']; ?>"> <?php echo $fcat['name']; ?></option>
                                    <!--二级分类-->
                                    <?php if(isset($fcat['children'])): if(is_array($fcat['children']) || $fcat['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $fcat['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$scat): $mod = ($i % 2 );++$i;?>
                                    <option value="<?php echo $scat['id']; ?>">&nbsp;&nbsp;├&nbsp;&nbsp;<?php echo $scat['name']; ?></option>
                                        <!--三级分类-->
                                        <?php if(isset($scat['children'])): if(is_array($scat['children']) || $scat['children'] instanceof \think\Collection): $i = 0; $__LIST__ = $scat['children'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$tcat): $mod = ($i % 2 );++$i;?>
                                        <option value="<?php echo $tcat['id']; ?>">&nbsp;&nbsp;&nbsp;&nbsp;├&nbsp;&nbsp;<?php echo $tcat['name']; ?></option>
                                        <?php endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                                </select>
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
    function showSuccess(data){
        layer.close( index );
        if( 1 == data.code ){

            layer.alert( data.msg, {'icon' : 1}, function(){
            });
        }else{
            layer.alert( data.msg, {'icon' : 2} );
        }
    }
   
</script>
</body>
</html>