<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:78:"C:\eclipse-workspace\iot\public/../application/admin\view\sensor_list\add.html";i:1606976923;s:67:"C:\eclipse-workspace\iot\application\admin\view\layout\default.html";i:1605513288;s:64:"C:\eclipse-workspace\iot\application\admin\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\admin\view\common\script.html";i:1605513288;}*/ ?>
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
                                <form id="add-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Title'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-title" data-rule="required" class="form-control" name="row[title]" type="text">
        </div>
    </div>
    
      <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Did'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-did" data-rule="required" class="form-control selectpicker" name="row[did]">
                <?php if(is_array($unitlist) || $unitlist instanceof \think\Collection || $unitlist instanceof \think\Paginator): if( count($unitlist)==0 ) : echo "" ;else: foreach($unitlist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>

      <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Kind'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-kind" data-rule="required" class="form-control selectpicker" name="row[kind]">
                <?php if(is_array($sensorlist) || $sensorlist instanceof \think\Collection || $sensorlist instanceof \think\Paginator): if( count($sensorlist)==0 ) : echo "" ;else: foreach($sensorlist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Port'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-port" data-rule="required" class="form-control selectpicker" name="row[port]">
                    <option value="A1" >A1</option>
                     <option value="A2" >A2</option>
                      <option value="A3" >A3</option>
                       <option value="A4" >A4</option>
                        <option value="A5" >A5</option>
                         <option value="D1" >D1</option>
                          <option value="D2" >D2</option>
                           <option value="D3" >D3</option>
            </select>
       
        </div>
    </div>
    <div style="display:none;" class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Number'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
              <select  id="c-number" data-rule="required" class="form-control selectpicker" name="row[number]">
                    <option value="1" >1</option>
                     <option value="2" >2</option>
                      <option value="3" >3</option>
                       <option value="4" >4</option>
      
            </select>
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">分组:</label>
        <div class="col-xs-12 col-sm-8">
           <?php echo Form::selectpickers('row[group][]', $grouplist, $ids); ?>
        </div>
    </div>
    
    <div class="form-group layer-footer">
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
