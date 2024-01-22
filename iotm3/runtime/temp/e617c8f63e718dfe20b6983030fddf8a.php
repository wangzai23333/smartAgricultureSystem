<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"/www/wwwroot/iotm3.yafrm.com/public/../application/admin/view/sensor_log/index.html";i:1652682749;s:71:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/layout/default.html";i:1649987434;s:68:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/meta.html";i:1649987434;s:70:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/script.html";i:1649987434;}*/ ?>
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
                                <style>
    .tab-content > .chart {
        padding: 10px;
    }
    body{
    background: #8ea8b4;
    }
</style>

<!-- Main row -->
<div class="row" style="margin-bottom:5px;">
<input type="hidden" id="did" value="<?php echo $did; ?>" />
<input type="hidden" id="sensorText" value="<?php echo $sensorText; ?>" />
    <!-- Left col -->
    <section class="col-lg-12 connectedSortable">
    <div>
          <button style="width: 99px;
    font-size: 15px;
    margin-bottom: 10px;
    background-color: #2c3e50;";  id="tosearch"class="btn btn-success btn-embossed">返回选择</button>
    </div>
        <!-- Custom tabs (Charts with tabs)-->
         <?php if(is_array($sensorList) || $sensorList instanceof \think\Collection || $sensorList instanceof \think\Paginator): if( count($sensorList)==0 ) : echo "" ;else: foreach($sensorList as $key=>$vo): ?>
        <div class="nav-tabs-custom charts-custom" id="do_<?php echo $vo['label']; ?>" style="width: 48%;
    float: left;
    margin: 0.5%;border: 2px solid #3c8dbc;    border-top: 0;">
   <div class="box-header box-solid bg-teal-gradient" style="    background: -webkit-gradient(linear, left bottom, left top, color-stop(0, #3c8dbc), color-stop(1, #3498db)) !important;
    margin-bottom: 10px;" >
                <i class="fa fa-th"></i>

                <h3 class="box-title"><?php echo $vo['title']; ?>(<?php echo $vo['label']; ?>)记录</h3>

                <div class="box-tools pull-right">
                </div>
            </div>
            <input type="hidden" id="data_<?php echo $vo['id']; ?>" value="<?php echo $todayText[$vo['id']]; ?>" />
            <!-- Tabs within a box -->
            <ul class="nav nav-tabs pull-right">
               <li ><a href="#chart_day_<?php echo $vo['id']; ?>" data-toggle="tab">7天数据</a></li>
             <li  class=" active" ><a id="to_<?php echo $vo['id']; ?>" href="#chart_today_<?php echo $vo['id']; ?>" data-toggle="tab">今天数据</a></li>
             
               
            </ul>
            <div class="tab-content no-padding">
                <!-- Morris chart - Sales -->
                   <div class="chart tab-pane" id="chart_day_<?php echo $vo['id']; ?>" style="position: relative; height: 300px;"></div>
                <div  class="chart tab-pane active" id="chart_today_<?php echo $vo['id']; ?>" style="position: relative; height: 300px;"></div>
             
            </div>
        </div>
       <?php endforeach; endif; else: echo "" ;endif; ?>

    </section>
   
</div>
<!-- /.row (main row) -->

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
