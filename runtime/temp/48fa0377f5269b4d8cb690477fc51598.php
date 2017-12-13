<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:91:"/Users/lsiten/project/workman/chat-tp5/public/../application/admin/view/shop/skuloader.html";i:1513152445;}*/ ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>sku添加</title>
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="/static/admin/css/animate.min.css" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet">         
</head>
<body>
    <div style="text-align: center; padding-top: 20px; width: 570px; overflow: hidden;" id="App-sku-loader-wrap">
        <?php if(empty($items) || ($items instanceof \think\Collection && $items->isEmpty())): ?><p>没有未选择的SKU属性了</p><?php endif; if(is_array($items) || $items instanceof \think\Collection): $i = 0; $__LIST__ = $items;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
            <p><button class="btn btn-default" data-id = "<?php echo $vo['id']; ?>"><?php echo $vo['name']; ?>:<?php echo $vo['items']; ?></button></p>
        <?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
    <script src="/static/admin/js/toastr/toastr.js"></script>
    <script src="/static/admin/js/beyond.min.js"></script>
    <script src="/static/admin/js/appapi.js"></script>
    <script src="/static/admin/js/bootbox/bootbox.js"></script> 
    <script>
        var RootPath = "__COMMON__";
        var bts=$('#App-sku-loader-wrap button');
        $(bts).on('click',function(){
            var id=$(this).data('id');
            var bt=$(this);
            $.ajax({
                        type:"post",
                        url:"<?php echo url('/admin/Shop/skuFindback'); ?>",
                        data:{'id':id},
                        dataType: "json",
                        success:function(mb){
                            if(mb.code==1)
                            {
                                window.parent.appendAttr(mb.attr,mb.attr_item);
                                $.App.alert('success','添加属性成功！');
                                $(bt).remove();
                            }
                            else
                            {
                                $.App.alert('error','添加失败！');                                
                            }
                            
                        },
                        error:function(xhr){
                            $.App.alert('danger','通讯失败！请重试！');
                        }
            });
        });
    </script>
</body>
</html>