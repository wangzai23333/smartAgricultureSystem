<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:82:"C:\eclipse-workspace\iot\public/../application/admin\view\general\crontab\add.html";i:1617096940;s:67:"C:\eclipse-workspace\iot\application\admin\view\layout\default.html";i:1605513288;s:64:"C:\eclipse-workspace\iot\application\admin\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\admin\view\common\script.html";i:1605513288;}*/ ?>
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
                                <style type="text/css">
    #schedulepicker {
        padding-top:7px;
    }
</style>
<form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
    <div class="form-group">
        <label for="name" class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input type="text" class="form-control" id="title" name="row[title]" value="" data-rule="required" />
        </div>
    </div>
    <div class="form-group">
        <label for="name" class="control-label col-xs-12 col-sm-2"><?php echo __('Type'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_select('row[type]', $typeList, null, ['class'=>'form-control', 'data-rule'=>'required']); ?>
        </div>
    </div>
    <div class="form-group">
        <label for="content" class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea name="row[content]" id="conent" cols="30" rows="5" class="form-control" data-rule="required"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label for="schedule" class="control-label col-xs-12 col-sm-2"><?php echo __('Schedule'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <div class="input-group margin-bottom-sm">
                <input type="text" class="form-control" id="schedule" style="font-size:12px;font-family: Verdana;word-spacing:23px;" name="row[schedule]" value="* * * * *" data-rule="required; remote(general/crontab/check_schedule)"/>
                <span class="input-group-btn">
                    <a href="https://www.fastadmin.net/store/crontab.html" target="_blank" class="btn btn-default"><i class="fa fa-info-circle"></i> <?php echo __('Crontab rules'); ?></a>
                </span>
                <span class="msg-box n-right"></span>
            </div>
            <div id="schedulepicker">
                <pre><code>*    *    *    *    *
-    -    -    -    -
|    |    |    |    +--- day of week (0 - 7) (Sunday=0 or 7)
|    |    |    +-------- month (1 - 12)
|    |    +------------- day of month (1 - 31)
|    +------------------ hour (0 - 23)
+----------------------- min (0 - 59)</code></pre>
                <h5><?php echo __('The next %s times the execution time', '<input type="number" id="pickdays" class="form-control text-center" value="7" style="display: inline-block;width:80px;">'); ?></h5>
                <ol id="scheduleresult" class="list-group">
                </ol>
            </div>
        </div>
    </div>
    <div class="form-group">
        <label for="maximums" class="control-label col-xs-12 col-sm-2"><?php echo __('Maximums'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input type="number" class="form-control" id="maximums" name="row[maximums]" value="0" data-rule="required" size="6" />
        </div>
    </div>
    <div class="form-group">
        <label for="begintime" class="control-label col-xs-12 col-sm-2"><?php echo __('Begin time'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input type="text" class="form-control datetimepicker" id="begintime" name="row[begintime]" value="" data-rule="<?php echo __('Begin time'); ?>:required" size="6" />
        </div>
    </div>
    <div class="form-group">
        <label for="endtime" class="control-label col-xs-12 col-sm-2"><?php echo __('End time'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input type="text" class="form-control datetimepicker" id="endtime" name="row[endtime]" value="" data-rule="<?php echo __('End time'); ?>:required;match(gte, row[begintime], datetime)" size="6" />
        </div>
    </div>
    <div class="form-group">
        <label for="weigh" class="control-label col-xs-12 col-sm-2"><?php echo __('Weigh'); ?>:</label>
        <div class="col-xs-12 col-sm-4">
            <input type="text" class="form-control" id="weigh" name="row[weigh]" value="0" data-rule="required" size="6" />
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Status'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <?php echo build_radios('row[status]', ['normal'=>__('Normal'), 'hidden'=>__('Hidden')]); ?>
        </div>
    </div>
    <div class="form-group hide layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
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
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
