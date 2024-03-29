<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:87:"C:\eclipse-workspace\iot\public/../application/admin\view\example\customform\index.html";i:1622101102;s:67:"C:\eclipse-workspace\iot\application\admin\view\layout\default.html";i:1605513288;s:64:"C:\eclipse-workspace\iot\application\admin\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\admin\view\common\script.html";i:1605513288;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <div class="row">
    <div class="col-md-6">
        <div class="box box-success">
            <div class="panel-heading">
                <?php echo __('自定义图片描述'); ?>
            </div>
            <div class="panel-body">
                <div class="alert alert-success-light">
                    <b>温馨提示</b><br>
                    默认我们的多图是没有图片描述的，如果我们需要自定义描述，可以使用以下的自定义功能<br>
                    特别注意的是图片的url和描述是分开储存的，也就是说图片一个字段，描述一个字段，你在前台使用时需要自己匹配映射关系<br>
                    <b>下面的演示textarea为了便于调试，设置为可见的，实际使用中应该添加个hidden的class进行隐藏</b>
                </div>
                <form id="first-form" role="form" data-toggle="validator" method="POST" action="">
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('一维数组示例'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <div class="input-group">
                                <input id="c-files" data-rule="required" class="form-control" size="50" name="row[files]" type="text" value="https://cdn.fastadmin.net/uploads/addons/blog.png,https://cdn.fastadmin.net/uploads/addons/cms.png,https://cdn.fastadmin.net/uploads/addons/vote.png">
                                <div class="input-group-addon no-border no-padding">
                                    <span><button type="button" id="plupload-files" class="btn btn-danger plupload" data-input-id="c-files" data-mimetype="*" data-multiple="true" data-preview-id="p-files"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                    <span><button type="button" id="fachoose-files" class="btn btn-primary fachoose" data-input-id="c-files" data-mimetype="*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                </div>
                                <span class="msg-box n-right" for="c-files"></span>
                            </div>

                            <!--ul需要添加 data-template和data-name属性，并一一对应且唯一 -->
                            <ul class="row list-inline plupload-preview" id="p-files" data-template="introtpl" data-name="row[intro]"></ul>

                            <!--请注意 ul和textarea间不能存在其它任何元素，实际开发中textarea应该添加个hidden进行隐藏-->
                            <textarea name="row[intro]" class="form-control" style="margin-top:5px;">["简洁响应式博客","CMS内容管理系统","在线投票系统"]</textarea>

                            <!--这里自定义图片预览的模板 开始-->
                            <script type="text/html" id="introtpl">
                                <li class="col-xs-3">
                                    <a href="<%=fullurl%>" data-url="<%=url%>" target="_blank" class="thumbnail">
                                        <img src="<%=fullurl%>" class="img-responsive">
                                    </a>
                                    <input type="text" name="row[intro][<%=index%>]" class="form-control" placeholder="请输入文件描述" value="<%=value?value:''%>"/>
                                    <a href="javascript:;" class="btn btn-danger btn-xs btn-trash"><i class="fa fa-trash"></i></a>
                                </li>
                            </script>
                            <!--这里自定义图片预览的模板 结束-->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('二维数组示例'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <div class="input-group">
                                <input id="c-images" data-rule="required" class="form-control" size="50" name="row[images]" type="text" value="https://cdn.fastadmin.net/uploads/addons/example.png,https://cdn.fastadmin.net/uploads/addons/upyun.png,https://cdn.fastadmin.net/uploads/addons/alioss.png">
                                <div class="input-group-addon no-border no-padding">
                                    <span><button type="button" id="plupload-images" class="btn btn-danger plupload" data-input-id="c-images" data-mimetype="image/gif,image/jpeg,image/png,image/jpg,image/bmp" data-multiple="true" data-preview-id="p-images"><i class="fa fa-upload"></i> <?php echo __('Upload'); ?></button></span>
                                    <span><button type="button" id="fachoose-images" class="btn btn-primary fachoose" data-input-id="c-images" data-mimetype="image/*" data-multiple="true"><i class="fa fa-list"></i> <?php echo __('Choose'); ?></button></span>
                                </div>
                                <span class="msg-box n-right" for="c-images"></span>
                            </div>

                            <!--ul需要添加 data-template和data-name属性，并一一对应且唯一 -->
                            <ul class="row list-inline plupload-preview" id="p-images" data-template="desctpl" data-name="row[desc]"></ul>

                            <!--请注意 ul和textarea间不能存在其它任何元素，实际开发中textarea应该添加个hidden进行隐藏-->
                            <textarea name="row[desc]" class="form-control" style="margin-top:5px;">[{"info":"开发者示例插件","size":"1M"},{"info":"又拍云储存整合","size":"2M"},{"info":"阿里OSS云储存","size":"1M"}]</textarea>

                            <!--这里自定义图片预览的模板 开始-->
                            <script type="text/html" id="desctpl">
                                <li class="col-xs-3">
                                    <a href="<%=fullurl%>" data-url="<%=url%>" target="_blank" class="thumbnail">
                                        <img src="<%=fullurl%>" class="img-responsive">
                                    </a>
                                    <input type="text" name="row[desc][<%=index%>][info]" class="form-control" placeholder="请输入插件描述" value="<%=value?value['info']:''%>"/>
                                    <input type="text" name="row[desc][<%=index%>][size]" class="form-control" placeholder="请输入插件大小" value="<%=value?value['size']:''%>"/>
                                    <a href="javascript:;" class="btn btn-danger btn-xs btn-trash"><i class="fa fa-trash"></i></a>
                                </li>
                            </script>
                            <!--这里自定义图片预览的模板 结束-->
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-2"></label>
                        <div class="col-xs-12 col-sm-8">
                            <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
                            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                        </div>
                    </div>

                </form>
            </div>
        </div>

    </div>
    <div class="col-md-6">
        <div class="box box-info">
            <div class="panel-heading">
                <?php echo __('自定义Fieldlist示例'); ?>
            </div>
            <div class="panel-body">
                <div class="alert alert-danger-light">
                    <b>温馨提示</b><br>
                    默认我们的fieldlist只有一维数组，为键值形式，如果需要二维数组，可使用下面的自定义模板来实现<br>
                    默认追加的元素是没有进行事件绑定的，我们需要监听btn-append这个按钮的fa.event.appendfieldlist事件<br>
                    <b>下面的演示textarea为了便于调试，设置为可见的，实际使用中应该添加个hidden的class进行隐藏</b>
                </div>
                <form id="second-form" role="form" data-toggle="validator" method="POST" action="">

                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Fieldlist示例'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <dl class="fieldlist" data-template="basictpl" data-name="row[basic]">
                                <dd>
                                    <ins><?php echo __('标题'); ?></ins>
                                    <ins><?php echo __('介绍'); ?></ins>
                                    <ins><?php echo __('大小'); ?></ins>
                                </dd>
                                <dd><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></dd>
                                <!--请注意 dd和textarea间不能存在其它任何元素，实际开发中textarea应该添加个hidden进行隐藏-->
                                <textarea name="row[basic]" class="form-control" cols="30" rows="5">[{"title":"开发者示例插件","intro":"开发者必备","size":"1M"},{"title":"又拍云储存整合","intro":"一款云储存","size":"2M"},{"title":"阿里OSS云储存","intro":"一款云储存","size":"1M"}]</textarea>
                            </dl>
                            <script id="basictpl" type="text/html">
                                <dd class="form-inline">
                                    <ins><input type="text" name="<%=name%>[<%=index%>][title]" class="form-control" value="<%=row.title%>" placeholder="标题" size="10"/></ins>
                                    <ins><input type="text" name="<%=name%>[<%=index%>][intro]" class="form-control" value="<%=row.intro%>" placeholder="描述"/></ins>
                                    <ins><input type="text" name="<%=name%>[<%=index%>][size]" class="form-control" value="<%=row.size%>" placeholder="大小"/></ins>
                                    <!--下面的两个按钮务必保留-->
                                    <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                                    <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>
                                </dd>
                            </script>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-2"><?php echo __('元素事件'); ?>:</label>
                        <div class="col-xs-12 col-sm-8">
                            <dl class="fieldlist" data-template="eventtpl" data-name="row[event]">
                                <dd>
                                    <ins><?php echo __('管理员'); ?></ins>
                                    <ins><?php echo __('名称'); ?></ins>
                                    <ins><?php echo __('登录时间'); ?></ins>
                                </dd>
                                <dd><a href="javascript:;" class="btn btn-sm btn-success btn-append"><i class="fa fa-plus"></i> <?php echo __('Append'); ?></a></dd>
                                <!--请注意 dd和textarea间不能存在其它任何元素，实际开发中textarea应该添加个hidden进行隐藏-->
                                <textarea name="row[event]" class="form-control" cols="30" rows="5">[{"id":"1","name":"admin","time":"2019-06-28 12:05:03"}]</textarea>
                            </dl>
                            <script id="eventtpl" type="text/html">
                                <dd class="form-inline">
                                    <ins><input type="text" name="<%=name%>[<%=index%>][id]" class="form-control selectpage" data-source="auth/admin/selectpage" data-field="username" value="<%=row.id%>" placeholder="管理员" size="10"/></ins>
                                    <ins><input type="text" name="<%=name%>[<%=index%>][name]" class="form-control" value="<%=row.name%>" placeholder="名称"/></ins>
                                    <ins><input type="text" name="<%=name%>[<%=index%>][time]" class="form-control datetimepicker" value="<%=row.time%>" placeholder="时间"/></ins>
                                    <!--下面的两个按钮务必保留-->
                                    <span class="btn btn-sm btn-danger btn-remove"><i class="fa fa-times"></i></span>
                                    <span class="btn btn-sm btn-primary btn-dragsort"><i class="fa fa-arrows"></i></span>
                                </dd>
                            </script>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label class="control-label col-xs-12 col-sm-2"></label>
                        <div class="col-xs-12 col-sm-8">
                            <button type="submit" class="btn btn-success btn-embossed"><?php echo __('OK'); ?></button>
                            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                        </div>
                    </div>

                </form>
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
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
