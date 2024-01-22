<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:92:"I:\iotm3.yafrm.com_20240122_151308\public/../application/admin\view\sensor_log\to_index.html";i:1652249314;s:77:"I:\iotm3.yafrm.com_20240122_151308\application\admin\view\layout\default.html";i:1649987434;s:74:"I:\iotm3.yafrm.com_20240122_151308\application\admin\view\common\meta.html";i:1649987434;s:76:"I:\iotm3.yafrm.com_20240122_151308\application\admin\view\common\script.html";i:1649987434;}*/ ?>
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
   
</style>

<!-- Main row -->
<div class="row" style="margin-bottom:5px;">
<div style="width: 68%;
    margin: auto;    margin-top: 30px;">
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-12" style="text-align: center;
    font-size: 25px;
    margin-bottom: 16px;
    color: #18bc9c;">组建单元选择</label>
      
    </div>
 <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8">
            <select style="    font-size: 19px;
    /* height: 88px; */
    line-height: 37px;
    max-height: 88px;
    height: 47px;
    border: 2px solid #17987f;
    border-radius: 8px;
    /* background: #c6dae8; */
    color:#17987f;" id="did" data-rule="required" class="form-control selectpicker" name="row[did]">
                <?php if(is_array($unitlist) || $unitlist instanceof \think\Collection || $unitlist instanceof \think\Paginator): if( count($unitlist)==0 ) : echo "" ;else: foreach($unitlist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-0"></label>
        <div class="col-xs-12 col-sm-12" style="text-align: center;">
         <button style="    width: 170px;
    font-size: 19px;
    margin-top: 45px;";  id="search"class="btn btn-success btn-embossed">数据查询</button>
        </div>
    </div>
   </div>
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
