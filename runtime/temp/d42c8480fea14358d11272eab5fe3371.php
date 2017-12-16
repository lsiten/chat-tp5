<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:72:"/var/www/api/chat-tp5/public/../application/admin/view/index/system.html";i:1512285488;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>常规设置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/sweetalert/sweetalert.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox float-e-margins">
            <div class="ibox-title">
                <h5>常规设置</h5>
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
                    <div class="row">
                            <form class="form-horizontal  m-t" id="commentForm" method="post" onsubmit="return toVaild()">
                        <?php if(is_array($slist) || $slist instanceof \think\Collection): if( count($slist)==0 ) : echo "" ;else: foreach($slist as $k=>$list): ?>
                            <div class="panel panel-default col-sm-5" style="padding-right: 0 ;padding-left: 0; margin:10px">
                                 <div class="panel-heading">
                                        <?php if($k == 'site'): ?>
                                         常规设置
                                        <?php else: ?>
                                         显示设置
                                        <?php endif; ?>
                                 </div>
                                 <div class="panel-body">
                                        <?php if(is_array($slist[$k]) || $slist[$k] instanceof \think\Collection): $i = 0; $__LIST__ = $slist[$k];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                            <!--判断输入框类型-->
                                            <div class="form-group">
                                             <?php switch($vo['tvalue']): case "radio": switch($vo['name']): case "site_editor": ?>
                                                            <label class="col-sm-3 control-label"><?php echo $vo['title']; ?></label>
                                                            <div class="radio i-checks col-sm-4">
                                                                    <label for="<?php echo $vo['name']; ?>_ue">
                                                                            <input type="radio" name="<?php echo $vo['name']; ?>" id="<?php echo $vo['name']; ?>_ue" value="ue" <?php if($vo['value'] == 'ue'): ?>checked="true"<?php endif; ?>>
                                                                            富文本编辑器</label> &nbsp;&nbsp;
                                                                     <label for="<?php echo $vo['name']; ?>_markdown">
                                                                            <input type="radio" name="<?php echo $vo['name']; ?>" id="<?php echo $vo['name']; ?>_markdown" value="markdown" <?php if($vo['value'] == 'markdown'): ?>checked="true"<?php endif; ?>>
                                                                            Markdown编辑器
                                                                        </label>
                                                                   <?php if($vo['remark']): ?><p class="help-block"><?php echo $vo['remark']; ?></p><?php endif; ?>
                                                            </div>
                                                        <?php break; default: ?>
                                                        <label class="col-sm-3 control-label"><?php echo $vo['title']; ?></label>
                                                        <div class="radio i-checks col-sm-4">
                                                                <label for="<?php echo $vo['name']; ?>_0">
                                                                        <input type="radio" name="<?php echo $vo['name']; ?>" id="<?php echo $vo['name']; ?>_0" value="0" <?php if($vo['value'] == '0'): ?>checked="true"<?php endif; ?>>
                                                                        否
                                                                </label>
                                                                <label for="<?php echo $vo['name']; ?>_1">
                                                                        <input type="radio" name="<?php echo $vo['name']; ?>" id="<?php echo $vo['name']; ?>_1" value="1" <?php if($vo['value'] == '1'): ?>checked="true"<?php endif; ?>>
                                                                        是
                                                                </label>
                                                               <?php if($vo['remark']): ?><p class="help-block"><?php echo $vo['remark']; ?></p><?php endif; ?>
                                                        </div>
                                                    <?php endswitch; break; case "select": ?>
                                                    <label class="col-sm-3 control-label"><?php echo $vo['title']; ?></label>
                                                    <div class="input-group col-sm-4">
                                                        <select class="form-control" name="<?php echo $vo['name']; ?>" required="" aria-required="true">
                                                            <option value="0">请选择</option>
                                                            <?php if(is_array($vo['svalue']) || $vo['svalue'] instanceof \think\Collection): $i = 0; $__LIST__ = $vo['svalue'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$s): $mod = ($i % 2 );++$i;?>
                                                                <option value="<?php echo $s; ?>" <?php if($s == $vo['value']): ?>selected<?php endif; ?>><?php echo $s; ?></option>
                                                            <?php endforeach; endif; else: echo "" ;endif; ?>
                                                        </select>
                                                    </div>
                                                 <?php break; default: ?>
                                                    <label class="col-sm-3 control-label"><?php echo $vo['title']; ?></label>
                                                    <div class="input-group col-sm-4">
                                                        <input id="username" type="text" class="form-control" name="<?php echo $vo['name']; ?>" value="<?php echo $vo['value']; ?>">
                                                        <?php if($vo['remark']): ?><p class="help-block"><?php echo $vo['remark']; ?></p><?php endif; ?>
                                                        
                                                    </div>
                                                 <?php endswitch; ?>
                                                </div>
                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                 </div>
                            </div>
                        <?php endforeach; endif; else: echo "" ;endif; ?>
                            <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-3">
                                    <!--<input type="button" value="提交" class="btn btn-primary" id="postform"/>-->
                                    <button class="btn btn-primary" type="submit">提交</button>
                                </div>
                            </div>
                        </form>
                    </div>
            </div>
        </div>
    </div>
</div>
<!-- End Panel Other -->
</div>
<!-- 角色分配 -->
<div class="col-sm-12" style="display: none" id="wait">
    <div class="ibox ">
        <div class="ibox-content">
            <div class="spiner-example">
                <div class="sk-spinner sk-spinner-three-bounce">
                    <div class="sk-bounce1"></div>
                    <div class="sk-bounce2"></div>
                    <div class="sk-bounce3"></div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/content.min.js?v=1.0.0"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="/static/admin/js/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="/static/admin/js/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="/static/admin/js/plugins/layer/laydate/laydate.js"></script>
<script src="/static/admin/js/plugins/sweetalert/sweetalert.min.js"></script>
<script src="/static/admin/js/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
    //表单提交
    function toVaild(){
        var jz;
        var url = "./system";
        $.ajax({
            type:"POST",
            url:url,
            data:$('#commentForm').serialize(),// 你的formid
            async: false,
            beforeSend:function(){
                jz = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2
            },
            error: function(request) {
                layer.close(jz);
                swal("网络错误!", "", "error");
            },
            success: function(data) {
                //关闭加载层
                layer.close(jz);
                if(data.code == 1){
                    swal(data.msg, "", "success");
                }else{
                    swal(data.msg, "", "error");
                }

            }
        });

        return false;
    }
</script>
</body>
</html>