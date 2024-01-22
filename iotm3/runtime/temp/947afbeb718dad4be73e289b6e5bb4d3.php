<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:73:"C:\eclipse-workspace\iot\public/../application/index\view\iotm\login.html";i:1623744669;s:64:"C:\eclipse-workspace\iot\application\index\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\index\view\common\script.html";i:1605513288;}*/ ?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?> – <?php echo $site['name']; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">

<?php if(isset($keywords)): ?>
<meta name="keywords" content="<?php echo $keywords; ?>">
<?php endif; if(isset($description)): ?>
<meta name="description" content="<?php echo $description; ?>">
<?php endif; ?>

<link rel="shortcut icon" href="/assets/img/favicon.ico" />

<link href="/assets/css/frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config: <?php echo json_encode($config); ?>
    };
</script>
        <link href="/assets/css/user.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
    </head>

    <body>
<div id="content-container" class="container" style="    margin-top: -48px;">
    <div class="user-section login-section">
        <div class="logon-tab clearfix"><a style="width: 100%;background: #c7dae8;font-size: 2rem;" class="active"><?php echo __('Sign in'); ?></a> </div>
        <div class="login-main">
            <form name="form" id="login-form" class="form-vertical" method="POST" action="">
                <div class="form-group">
                    <label class="control-label" for="account">账号</label>
                    <div class="controls">
                        <input class="form-control input-lg" id="account" type="text" name="account" value="" data-rule="required" placeholder="请输入账号" autocomplete="off">
                        <div class="help-block"></div>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label" for="password">密码</label>
                    <div class="controls">
                        <input class="form-control input-lg" id="password" type="password" name="password" data-rule="required;password" placeholder="<?php echo __('Password'); ?>" autocomplete="off">
                    </div>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-lg btn-block"><?php echo __('Sign in'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
 <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-frontend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>

    </body>

</html>
