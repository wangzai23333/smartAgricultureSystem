<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:88:"/www/wwwroot/iotm3.yafrm.com/public/../application/admin/view/component_unit/switch.html";i:1649987434;s:71:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/layout/default.html";i:1649987434;s:68:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/meta.html";i:1649987434;s:70:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/script.html";i:1649987434;}*/ ?>
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
                                <form id="edit-form" class="form-horizontal" role="form" data-toggle="validator" method="POST" action="">
<input type="hidden" value="<?php echo $id; ?>" id="sid">
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关1:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status1_0" name="row[onoff1]" type="radio" value="0" <?php if($info['onoff1']==0||empty($list[1])): ?> checked <?php endif; ?> /> 关闭</label> 
           <label ><input id="status1_1" name="row[onoff1]" type="radio" value="1"  <?php if($info['onoff1'] == 1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关2:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status2_0" name="row[onoff2]" type="radio" value="0" <?php if($info['onoff2']==0||empty($info['onoff2'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status2_1" name="row[onoff2]" type="radio" value="1"  <?php if($info['onoff2']==1): ?> checked <?php endif; ?> />打开</label> 
            </div>

        </div>
    </div>

<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关3:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status3_0" name="row[onoff3]" type="radio" value="0" <?php if($info['onoff3']==0||empty($info['onoff3'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status3_1" name="row[onoff3]" type="radio" value="1"  <?php if($info['onoff3']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关4:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status4_0" name="row[onoff4]" type="radio" value="0" <?php if($info['onoff4']==0||empty($info['onoff4'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status4_1" name="row[onoff4]" type="radio" value="1" <?php if($info['onoff4']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关5:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status5_0" name="row[onoff5]" type="radio" value="0" <?php if($info['onoff5']==0||empty($info['onoff4'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status5_1" name="row[onoff5]" type="radio" value="1" <?php if($info['onoff5']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>
 
<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关6:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status6_0" name="row[onoff6]" type="radio" value="0" <?php if($info['onoff6']==0||empty($info['onoff6'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status6_1" name="row[onoff6]" type="radio" value="1" <?php if($info['onoff6']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关7:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status7_0" name="row[onoff7]" type="radio" value="0" <?php if($info['onoff7']==0||empty($info['onoff7'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status7_1" name="row[onoff7]" type="radio" value="1" <?php if($info['onoff7']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

<div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关8:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status8_0" name="row[onoff8]" type="radio" value="0" <?php if($info['onoff8']==0||empty($info['onoff8'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status8_1" name="row[onoff8]" type="radio" value="1" <?php if($info['onoff8']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关9:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status9_0" name="row[onoff9]" type="radio" value="0" <?php if($info['onoff9']==0||empty($info['onoff9'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status9_1" name="row[onoff9]" type="radio" value="1" <?php if($info['onoff9']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

 <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关10:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status10_0" name="row[onoff10]" type="radio" value="0" <?php if($info['onoff10']==0||empty($info['onoff10'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status10_1" name="row[onoff10]" type="radio" value="1" <?php if($info['onoff10']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

 <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关11:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status11_0" name="row[onoff11]" type="radio" value="0" <?php if($info['onoff11']==0||empty($info['onoff11'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status11_1" name="row[onoff11]" type="radio" value="1" <?php if($info['onoff11']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关12:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status12_0" name="row[onoff12]" type="radio" value="0" <?php if($info['onoff12']==0||empty($info['onoff12'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status12_1" name="row[onoff12]" type="radio" value="1" <?php if($info['onoff12']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>


     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关13:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status13_0" name="row[onoff13]" type="radio" value="0" <?php if($info['onoff13']==0||empty($info['onoff13'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status13_1" name="row[onoff13]" type="radio" value="1" <?php if($info['onoff13']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

 <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关14:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status14_0" name="row[onoff14]" type="radio" value="0" <?php if($info['onoff14']==0||empty($info['onoff14'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status14_1" name="row[onoff14]" type="radio" value="1" <?php if($info['onoff14']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关15:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status15_0" name="row[onoff15]" type="radio" value="0" <?php if($info['onoff15']==0||empty($info['onoff15'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status15_1" name="row[onoff15]" type="radio" value="1" <?php if($info['onoff15']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

        </div>
    </div>

     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">开关16:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label ><input id="status16_0" name="row[onoff16]" type="radio" value="0" <?php if($info['onoff16']==0||empty($info['onoff16'])): ?> checked <?php endif; ?>   /> 关闭</label> 
           <label ><input id="status16_1" name="row[onoff16]" type="radio" value="1" <?php if($info['onoff16']==1): ?> checked <?php endif; ?>  />打开</label> 
            </div>

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
