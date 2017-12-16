<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:71:"/var/www/api/chat-tp5/public/../application/admin/view/shop/skuset.html";i:1513175448;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>商品SKU设置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/js/plugins/jquerydatetimepicker/jquery.datetimepicker.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-12">
            <div class="ibox float-e-margins">

                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('shop/skuset'); ?>">
                       <div class="form-group">
                            <label class="col-sm-3 control-label">此商品SKU属性：</label>
                            <div class="col-sm-4 input-group">
                                <input disabled class="form-control" value="<?php echo $item['skuattr']; ?>" placeholder="必填" type="text" name="skuattr" id="skuattr" required="" aria-required="true"/>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">此商品SKU价格：</label>
                            <div class="col-sm-4 input-group">
                                <input  class="form-control" value="<?php echo $item['price']; ?>" placeholder="必填" type="text" name="price" id="price" required="" aria-required="true"/>                                
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">此商品SKU库存：</label>
                            <div class="col-sm-4 input-group">
                                <input  class="form-control" value="<?php echo $item['num']; ?>" placeholder="必填" type="text" name="num" id="num" required="" aria-required="true"/>                                
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
                window.parent.initTable(2);
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
</script>
</body>
</html>