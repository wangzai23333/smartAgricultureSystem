<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:84:"C:\eclipse-workspace\iotm3.1\public/../application/admin\view\apilog\data\index.html";i:1649987433;s:71:"C:\eclipse-workspace\iotm3.1\application\admin\view\layout\default.html";i:1649987433;s:68:"C:\eclipse-workspace\iotm3.1\application\admin\view\common\meta.html";i:1649987433;s:70:"C:\eclipse-workspace\iotm3.1\application\admin\view\common\script.html";i:1649987433;}*/ ?>
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
    .smallstat {
        border-radius: 8px;
        box-shadow: 2px 2px 4px #ccc;
        position: relative;
        margin-bottom: 30px;
        height: 120px;
        padding: 15px;
    }

    .teal-bg {
        color: #fff;
        background: #97d3c5;
        background-color: #97d3c5;
    }

    .smallstat .value {
        text-align: center;
        font-size: 26px;
        padding-top: 5px;
    }

    .smallstat .title,
    .smallstat .value {
        display: block;
        width: 100%;
    }

    .smallstat h4 {
        text-align: center;
        font-size: 16px;
        margin-top: 20px;
        letter-spacing: 0.05rem;
    }
</style>
<div class="panel" style="background-color: transparent;">
    <div class="panel-body">
        <div class="btn-group datefilter" role="group" aria-label="..." style="padding-bottom: 10px;">
            <button type="button" data-type="1" class="btn btn-default">15分钟</button>
            <button type="button" data-type="2" class="btn btn-default">30分钟</button>
            <button type="button" data-type="3" class="btn btn-default">1小时</button>
            <button type="button" data-type="4" class="btn btn-default">4小时</button>
            <button type="button" data-type="5" class="btn btn-default">12小时</button>
            <button type="button" data-type="6" class="btn btn-default btn-24hours">24小时</button>

            <div class="input-group " style="padding-left: 10px; width: 340px;">
                <span class="input-group-addon"><i class="fa fa-calendar"></i></span>
                <input type="text" class="form-control input-inline datetimerange" id="createtime" placeholder="指定日期">
            </div>
        </div>
    </div>
</div>

<div class="panel" style="background-color: transparent;">
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="one">

                <div class="row">
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12">
                        <div class="smallstat teal-bg" style="background-color:#dd6b66">
                            <b><span class="value" id="bs_request">0</span></b>
                            <h4>请求次数</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12">
                        <div class="smallstat teal-bg" style="background-color:#759aa0">
                            <b><span class="value" id="bs_time">0</span></b>
                            <h4>平均处理时间(ms)</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12">
                        <div class="smallstat teal-bg" style="background-color:#e69d87">
                            <b><span class="value" id="bs_404">0</span></b>
                            <h4>404</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12">
                        <div class="smallstat teal-bg" style="background-color:#eedd78">
                            <b><span class="value" id="bs_500">0</span></b>
                            <h4>500</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12">
                        <div class="smallstat teal-bg" style="background-color:#73a373">
                            <b><span class="value" id="bs_error">0%</span></b>
                            <h4>错误率占比</h4>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-6 col-xs-6 col-xxs-12">
                        <div class="smallstat teal-bg" style="background-color:#7289ab">
                            <b><span class="value" id="bs_api">0</span></b>
                            <h4>接口总数(已请求)</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel">
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in">
                <div class="row">
                    <div class="col-md-6 nav-tabs-custom charts-custom">
                        <div class="chart tab-pane" id="code-chart" style="position: relative; height: 300px;">
                        </div>
                    </div>
                    <div class="col-md-6 nav-tabs-custom charts-custom">
                        <div class="chart tab-pane" id="time-chart" style="position: relative; height: 300px;">
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 nav-tabs-custom charts-custom">
                        <div class="chart tab-pane" id="request-chart" style="position: relative; height: 350px;">
                        </div>
                    </div>
                    <div class="col-md-6 nav-tabs-custom charts-custom">
                        <div class="chart tab-pane" id="error-chart" style="position: relative; height: 350px;">
                        </div>

                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 nav-tabs-custom charts-custom">
                        <div class="chart tab-pane" id="fast-chart" style="position: relative; height: 350px;">
                        </div>
                    </div>
                    <div class="col-md-6 nav-tabs-custom charts-custom">
                        <div class="chart tab-pane" id="slow-chart" style="position: relative; height: 350px;">
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
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
