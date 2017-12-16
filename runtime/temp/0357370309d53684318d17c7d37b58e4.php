<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:67:"/var/www/api/chat-tp5/public/../application/admin/view/wx/menu.html";i:1513016737;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>微信设置</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="/static/admin/css/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="/static/admin/css/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="/static/admin/css/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="/static/admin/css/beyond.min.css" rel="stylesheet">    
    <style type="text/css">
            .nav-pills>li.active{
                border-left: none;
                background: none;
            }
        .table-striped td {
            padding-top: 10px;
            padding-bottom: 10px
        }
        
        a {
            font-size: 14px;
        }
        
        a:hover,
        a:active {
            text-decoration: none;
            color: red;
        }
        
        .hover td {
            padding-left: 10px;
        }
        
        .designer a {
            border-left: 1px #DDD solid;
            margin-left: 10px;
            padding-left: 10px;
            color: #333;
        }
        
        .modal-dialog .radio-inline {
            width: 32.5%;
            padding: 5px 0 5px 20px;
            margin-left: 0;
        }
        
        .sonmenu {
            margin-top: 20px;
            padding-left: 80px;
            background: url('__IMG__/bg_repno.gif') no-repeat -245px -545px;
        }
        
        .hide {
            display: none;
        }

        .widget-buttons .btn {
            margin-top: 2px;
        }
        </style>
       
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div>
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>微信菜单</h5>
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
                        <div class="row">
                                <div class="col-xs-12 col-md-12">
                                    <div class="widget">
                                        <form id="form" action="<?php echo url('Admin/Wx/menu'); ?>" method="post" target='frame'>
                                            <input id="do" type="hidden" name="do" value="">
                                        </form>
                                        <iframe id='frame' name='frame' style='display:none;'></iframe>
                            
                                        <div class="widget-header bg-blue">
                                            <i class="widget-icon fa fa-arrow-down"></i>
                                            <span class="widget-caption">自定义菜单</span>
                                            <div class="widget-buttons">
                                                <a href="#" data-toggle="maximize">
                                                    <i class="fa fa-expand"></i>
                                                </a>
                                                <a href="#" data-toggle="collapse">
                                                    <i class="fa fa-minus"></i>
                                                </a>
                                                <a href="#" data-toggle="dispose">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="widget-body">
                                            <table class="table table-bordered table-hover">
                                                <tbody class="mlist">
                                                        <?php if(is_array($menu['button']) || $menu['button'] instanceof \think\Collection): $i = 0; $__LIST__ = $menu['button'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                                                            <tr>
                                                                <td>
                                                                    <div class="parentmenu" data-type="<?php echo $vo['type']; ?>" data-url="<?php echo $vo['url']; ?>" data-key="<?php echo $vo['key']; ?>">
                                                                        <input type="text" class="form-control" style="display:inline-block;width:300px;" value="<?php echo $vo['name']; ?>">
                                                                        <a href="javascript:;" onclick="setAction(this);" title="设置此菜单动作"><i class="glyphicon glyphicon-pencil"></i> 设置此菜单动作</a>
                                                                        <a href="javascript:;" onclick="deleteMenu(this)" title="删除此菜单"><i class="glyphicon glyphicon-trash"></i> 删除此菜单</a>
                                                                        <a href="javascript:;" onclick="addSubMenu(this);" title="添加子菜单"><i class=" glyphicon glyphicon-plus"></i> 添加子菜单</a>
                                                                    </div>
                                                                    <div class="smlist">
                                                                        <volist name="vo.sub_button" id="sub">
                                                                            <div class="sonmenu" data-type="<?php echo $sub['type']; ?>" data-url="<?php echo $sub['url']; ?>" data-key="<?php echo $sub['key']; ?>">
                                                                                <input type="text" class="form-control" style="display:inline-block;width:220px;" value="<?php echo $sub['name']; ?>">
                                                                                <a href="javascript:;" onclick="setAction(this);" title="设置此菜单动作"><i class="glyphicon glyphicon-pencil"></i> 设置此菜单动作</a>
                                                                                <a href="javascript:;" onclick="deleteMenu(this);" title="删除此菜单"><i class="glyphicon glyphicon-trash"></i> 删除此菜单</a>
                                                                            </div>
                                                                        </volist>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        <?php endforeach; endif; else: echo "" ;endif; ?>
                                                </tbody>
                                            </table>
                                            <div class="widget-header bg-gold">
                                                <span class="widget-caption">
                                                    <a href="javascript:;" onclick="addMenu();">添加菜单 <i class="fa fa-plus-circle" title="添加菜单"></i></a>
                                                </span>
                                                <div class="widget-buttons">
                                                    <input type="button" value="保存菜单结构" class="btn btn-success" onclick="saveMenu();" />&nbsp;
                                                    <input type="button" value="删除菜单结构" class="btn btn-danger" onclick="removeMenu();" />
                                                </div>
                                            </div>
                                        </div>
                                        <div id="dialog" class="modal fade">
                                            <div class="modal-dialog">
                                                <div class="modal-content widget">
                                                    <div class="widget-header bg-gold modal-header">
                                                        <span class="widget-caption"><h5>选择菜单 <strong id="menu-name" class="red"></strong> 要执行的操作</h5></span>
                                                        <div class="widget-buttons">
                                                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        </div>
                                                    </div>
                                                    <div class="modal-body">
                                                        <input id="type" type="hidden">
                                                        <ul class="nav nav-pills">
                                                            <li role="presentation" class="menutype" data-type="view"><a href="javascript:;">链接</a></li>
                                                            <li role="presentation" class="menutype" data-type="click"><a href="javascript:;">模拟关键字</a></li>
                                                            <li role="presentation" class="menutype" data-type="scancode_push"><a href="javascript:;">扫码</a></li>
                                                            <li role="presentation" class="menutype" data-type="scancode_waitmsg"><a href="javascript:;">扫码（等待信息）</a></li>
                                                            <li role="presentation" class="menutype" data-type="pic_sysphoto"><a href="javascript:;">系统拍照发图</a></li>
                                                            <li role="presentation" class="menutype" data-type="pic_photo_or_album"><a href="javascript:;">拍照或者相册发图</a></li>
                                                            <li role="presentation" class="menutype" data-type="pic_weixin"><a href="javascript:;">微信相册发图</a></li>
                                                            <li role="presentation" class="menutype" data-type="location_select"><a href="javascript:;">地理位置</a></li>
                                                        </ul>
                                                        <!-- /input-group -->
                                                        <div id="url">
                                                            <hr />
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-addon"><i class="glyphicon glyphicon-link"></i></span>
                                                                <input class="form-control" id="target-url" type="text" placeholder="http://" />
                                                            </div>
                                                            <span class="help-block">指定点击此菜单时要跳转的链接（注：链接需加http://）</span>
                                                            <span class="help-block"><strong>注意: 由于接口限制. 如果你没有网页oAuth接口权限, 这里输入链接直接进入微站个人中心时将会有缺陷(有可能获得不到当前访问用户的身份信息. 如果没有oAuth接口权限, 建议你使用图文回复的形式来访问个人中心)</strong></span>
                                                        </div>
                                                        <div id="other" style="position:relative">
                                                            <hr />
                                                            <div class="input-group input-group-sm">
                                                                <span class="input-group-addon"><i class="glyphicon glyphicon-send"></i></span>
                                                                <input class="form-control" id="target-other" type="text" />
                                                            </div>
                                                            <div id="key-result" style="width:100%;position:absolute;top:55px;left:0px;display:none;z-index:10000">
                                                                <ul class="dropdown-menu" style="display:block;width:88%;"></ul>
                                                            </div>
                                                            <span class="help-block">指定点击此菜单时要执行的操作, 你可以在这里输入关键字, 那么点击这个菜单时就就相当于发送这个内容至本系统</span>
                                                            <span class="help-block"><strong>这个过程是程序模拟的, 比如这里添加关键字: 优惠券, 那么点击这个菜单是, 本系统相当于接受了粉丝用户的消息, 内容为"优惠券"</strong></span>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <a href="javascript:;" onclick="saveAction();" class="pull-right btn btn-primary span2">保存</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="/static/admin/js/jquery.min.js?v=2.1.4"></script>
<script src="/static/admin/js/bootstrap.min.js?v=3.3.6"></script>
<script src="/static/admin/js/toastr/toastr.js"></script>
<script src="/static/admin/js/beyond.min.js"></script>
<script src="/static/admin/js/appapi.js"></script>
<script>
        var RootPath = "__COMMON__";
        // 预加载
        $(function() {
            // 选择菜单
            $(".menutype").click(function() {
                $(".menutype").removeClass('active');
                $(this).addClass('active');
                $("#type").val($(this).data('type'));
                if ($(this).data('type') == "view") {
                    $("#url").removeClass('hide');
                    $("#other").addClass('hide');
                } else {
                    $("#other").removeClass('hide');
                    $("#url").addClass('hide');
                }
            });
            $(".menutype:first").click();
        });
        var currentMenu = null;
        
        // 添加一级菜单
        function addMenu() {
            if ($('.parentmenu').length >= 3) {
                $.App.alert('danger', '一级菜单栏不可以多于三个');
            } else {
                var html = '<tr>' +
                    '<td>' +
                    '<div class="parentmenu" data-type="" data-key="" data-url="">' +
                    '<input type="text" class="form-control" style="display:inline-block;width:300px;" value="">' +
                    ' <a href="javascript:;" onclick="setAction(this);" title="设置此菜单动作"><i class="glyphicon glyphicon-pencil"></i> 设置此菜单动作</a>' +
                    ' <a href="javascript:;" onclick="deleteMenu(this)" title="删除此菜单"><i class="glyphicon glyphicon-trash"></i> 删除此菜单</a>' +
                    ' <a href="javascript:;" onclick="addSubMenu(this);" title="添加子菜单"><i class="glyphicon glyphicon-plus"></i> 添加子菜单</a>' +
                    '</div>' +
                    '<div class="smlist">' +
                    '</div>' +
                    '</td>' +
                    '</tr>';
                $('tbody.mlist').append(html);
            }
        }
        
        // 添加二级菜单
        function addSubMenu(o) {
            if ($(o).parent().next().find('.sonmenu').length >= 5) {
                $.App.alert('danger', '二级菜单不可以多于五个');
            } else {
                var html = '<div class="sonmenu" data-type="" data-url="" data-key="">' +
                    '<input type="text" class="form-control" style="display:inline-block;width:220px;" value="">' +
                    '<a href="javascript:;" onclick="setAction(this);" title="设置此菜单动作"><i class="glyphicon glyphicon-pencil"></i> 设置此菜单动作</a>' +
                    '<a href="javascript:;" onclick="deleteMenu(this);" title="删除此菜单"><i class="glyphicon glyphicon-trash"></i> 删除此菜单</a>' +
                    '</div>';
                $(o).parent().next().append(html);
            }
        }
        
        // 删除一级菜单
        function deleteMenu(o) {
            if ($(o).parent().parent().hasClass('smlist')) {
                $(o).parent().slideUp('slow', function() {
                    $(o).parent().remove();
                });
            } else {
                $(o).parent().parent().parent().fadeOut('slow', function() {
                    $(o).parent().parent().parent().remove();
                });
            }
        }
        
        // 设置菜单功能
        function setAction(o) {
            var menu = $(o).parent();
            // 缓存设置中的Menu
            currentMenu = menu;
            // 判断是否存在子菜单
            if (menu.next().find('.sonmenu').length > 0) {
                $.App.alert('danger', '包含子菜单，无法设置动作');
                return false;
            }
            var menutype = menu.data('type');
            var menukey = menu.data('key');
            var menuurl = menu.data('url');
            var menuname = menu.find('input').first().val();
            // 触发菜单修改
            $(".menutype").each(function() {
                if ($(this).data('type') == menutype) {
                    $(this).click();
                }
            });
            // 更新Dialog名
            $('#menu-name').text(menuname);
            // 更新Dialog参数
            $('#target-url').val(menuurl);
            $('#target-other').val(menukey);
            // 修改命名，修改Banner
            $('#dialog').modal('show');
        }
        
        // 保存菜单功能
        function saveAction() {
            var menu = currentMenu;
            if (menu == null) {
                $.App.alert('danger', '操作错误，请关闭后重试！');
                return false;
            }
            var menutype = $("#type").val();
            var menukey = $("#target-other").val();
            var menuurl = $("#target-url").val();
        
            menu.data('type', menutype);
            if (menutype == 'view') {
                menu.data('url', menuurl);
                menu.data('key', '');
            } else {
                menu.data('url', '');
                menu.data('key', menukey);
            }
            $.App.alert('ok', '修改成功！');
            $('#dialog').modal('hide');
        }
        
        // 保存菜单功能（写不出来了）
        function saveMenu() {
            // 菜单一定需要命名
            if ($('.parentmenu input,.sonmenu input').filter(function() {
                    return $.trim($(this).val()) == '';
                }).length > 0) {
                $.App.alert('danger', '存在未输入名称的菜单');
                return false;
            }
            // 构造字符串
            var dat = '[';
            var error = false;
            $('.parentmenu').each(function() {
                var pname = $.trim($(this).find(':text').val()).replace(/"/g, '\"');
                var ptype = $(this).data('type');
                var purl = $(this).data('url');
                if (!purl) purl = '';
                var pkey = $.trim($(this).data('key'));
                if (!pkey) pkey = '';
                dat += '{"name": "' + pname + '"';
                // 判断是否存在子菜单
                if ($(this).next().find('.sonmenu').length > 0) {
                    dat += ',"sub_button": [';
                    $(this).next().find('.sonmenu').each(function() {
                        var sname = $.trim($(this).find(':text').val()).replace(/"/g, '\"');
                        var stype = $(this).data('type');
                        var surl = $(this).data('url');
                        if (!surl)
                            surl = '';
                        var skey = $.trim($(this).data('key'));
                        if (!skey)
                            skey = '';
                        dat += '{"name": "' + sname + '"';
                        if ((stype != 'view' && skey == '') || (stype == 'view' && !surl)) {
                            $.App.alert('danger', '子菜单项 “' + sname + '”未设置对应规则.');
                            error = true;
                            return false;
                        }
                        if (stype == 'click') {
                            dat += ',"type": "click","key": "' + encodeURIComponent(skey) + '"';
                        } else if (stype == 'view') {
                            dat += ',"type": "view","url": "' + surl + '"';
                        } else {
                            dat += ',"type": "' + stype + '","key": "' + encodeURIComponent(skey) + '"';
                        }
                        dat += '},';
                    });
                    if (error) {
                        return false;
                    }
                    dat = dat.slice(0, -1);
                    dat += ']';
                } else {
                    if ((ptype != 'view' && pkey == '') || (ptype == 'view' && !purl)) {
                        $.App.alert('danger', '菜单 “' + pname + '”不存在子菜单项, 且未设置对应规则.');
                        error = true;
                        return false;
                    }
                    if (ptype == 'click') {
                        dat += ',"type": "click","key": "' + encodeURIComponent(pkey) + '"';
                    } else if (ptype == 'view') {
                        dat += ',"type": "view","url": "' + purl + '"';
                    } else {
                        dat += ',"type": "' + ptype + '","key": "' + encodeURIComponent(pkey) + '"';
                    }
                }
                dat += '},';
            });
            if (error) {
                return false;
            }
            dat = dat.slice(0, -1);
            dat += ']';
            $('#do').val(dat);
            $('#form')[0].submit();
        }
        
        // 移除自定义菜单
        function removeMenu() {
            $('#do').val('remove');
            $('#form')[0].submit();
        }
        
        // Iframe 回调方法
        function replaceok(msg) {
            $.App.alert('success', msg);
            $('#do').val('');
        }
        
        // Iframe 回调方法
        function replaceFuck(msg) {
            $.App.alert('danger', msg);
            $('#do').val('');
        }
        
        
        </script>
</body>
</html>