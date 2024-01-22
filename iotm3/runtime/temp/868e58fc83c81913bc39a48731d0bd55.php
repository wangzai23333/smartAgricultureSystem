<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:54:"/www/wwwroot/iotm3.yafrm.com/addons/apilog/config.html";i:1649987434;s:71:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/layout/default.html";i:1649987434;s:68:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/meta.html";i:1649987434;s:70:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/script.html";i:1649987434;}*/ ?>
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
    .row .input-group{
    float: none;
    padding-left: 15px;
    padding-right: 15px;
    margin-right: 15px;
}
</style>
<div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <div class="panel-lead"><em>API预警配置</em>可在此对API的运行情况进行监控并发送通知</div>
        <ul class="nav nav-tabs">
            <li class="active"><a href="#tab-base" data-toggle="tab">基础配置</a></li>
            <li><a href="#tab-time" data-toggle="tab">响应超时监控</a></li>
            <li><a href="#tab-error" data-toggle="tab">请求错误监控</a></li>
            <li><a href="#tab-ip" data-toggle="tab">IP异常监控</a></li>
            <li><a href="#tab-count" data-toggle="tab">请求量监控</a></li>
        </ul>
    </div>
    <div class="panel-body">
        <form id="config-form" class="edit-form form-horizontal" role="form" data-toggle="validator" method="POST"
            action="">
            <div id="myTabContent" class="tab-content">
                <!--基础配置-->
                <div class="tab-pane fade active in" id="tab-base">
                    <div class="widget-body no-padding">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th width="15%">配置项</th>
                                    <th width="85%">配置值</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>预警邮箱</td>
                                    <td>
                                        <div class="row">
                                            <div class="col-sm-8 col-xs-12">
                                                <input type="text" name="row[base][email]" placeholder="请输入接收预警的邮箱,多个使用英文逗号分隔"
                                                    value='<?php echo $addon['config'][0]["value"]["email"]; ?>' class="form-control"
                                                    data-tip="">
                                            </div>
                                            <div class="col-sm-4"></div>
                                        </div>
                                    </td>
                                </tr>
                               
                               
                            </tbody>
                        </table>

                    </div>
                    <div class="alert alert-info-light" style="margin-bottom:10px;">
                        <b>基础配置:</b><br>
                        1、预警邮箱:接收预警监控通知的邮箱；<br>
                        请务必先在 [常规管理-系统配置-邮件配置] 中配置并测试邮件发送是否正常<br>
                        2、相同预警通知在30分钟内仅会发送一次，请及时关注API运行情况<br>
                        3、建议使用Redis作为系统缓存
                    </div>                   
                </div>
                <!--响应超时-->
                <div class="tab-pane fade" id="tab-time">
                    <div class="widget-body no-padding">
                        <div class="widget-body no-padding">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="15%">配置项</th>
                                        <th width="85%">配置值</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>监控频率</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-12 col-xs-12">
                                                    <select name="row[time][pl]" class="selectpicker">
                                                        <option value ="60" <?php if($addon['config'][1]["value"]["pl"]==60): ?> selected <?php endif; ?>>1分钟</option>
                                                        <option value ="180" <?php if($addon['config'][1]["value"]["pl"]==180): ?> selected <?php endif; ?>>3分钟</option>
                                                        <option value ="300" <?php if($addon['config'][1]["value"]["pl"]==300): ?> selected <?php endif; ?>>5分钟</option>
                                                        <option value ="600" <?php if($addon['config'][1]["value"]["pl"]==600): ?> selected <?php endif; ?>>10分钟</option>
                                                        <option value ="1800" <?php if($addon['config'][1]["value"]["pl"]==1800): ?> selected <?php endif; ?>>30分钟</option>
                                                        <option value="3600" <?php if($addon['config'][1]["value"]["pl"]==3600): ?> selected <?php endif; ?>>1小时</option>
                                                        <option value="7200" <?php if($addon['config'][1]["value"]["pl"]==7200): ?> selected <?php endif; ?>>2小时</option>
                                                        <option value="14400" <?php if($addon['config'][1]["value"]["pl"]==14400): ?> selected <?php endif; ?>>4小时</option>
                                                        <option value="21600" <?php if($addon['config'][1]["value"]["pl"]==21600): ?> selected <?php endif; ?>>6小时</option>
                                                        <option value="28800" <?php if($addon['config'][1]["value"]["pl"]==28800): ?> selected <?php endif; ?>>8小时</option>
                                                        <option value="43200" <?php if($addon['config'][1]["value"]["pl"]==43200): ?> selected <?php endif; ?>>12小时</option>
                                                        <option value="86400" <?php if($addon['config'][1]["value"]["pl"]==86400): ?> selected <?php endif; ?>>1天</option>
                                                      </select>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>超时时间</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12 input-group">
                                                    <input type="text" name="row[time][sj]"
                                                        value='<?php echo $addon['config'][1]["value"]["sj"]; ?>'
                                                        class="form-control" data-tip="" aria-describedby="time_t">
                                                    <span class="input-group-addon" id="time_t">毫秒</span>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>百分比</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12 input-group">
                                                    <input type="text" name="row[time][zb]"
                                                        value='<?php echo $addon['config'][1]["value"]["zb"]; ?>'
                                                        class="form-control" data-tip="" aria-describedby="time_zb">
                                                        <span class="input-group-addon" id="time_zb">%</span>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>是否开启预警</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="radio" name="row[time][open]" value="0" <?php if($addon['config'][1]["value"]["open"]==0): ?> checked <?php endif; ?>> 关闭
                                                    <input type="radio" name="row[time][open]" value="1" <?php if($addon['config'][1]["value"]["open"]==1): ?> checked <?php endif; ?>> 开启
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="alert alert-info-light" style="margin-bottom:10px;">
                            <b>响应超时监控:</b><br>主要针对一段时间内接口调用超时率过高<br>
                            1、设定接口超时时间(毫秒)<br>
                            2、设置接口超时百分比。支持两位小数，超时率达到或者大于设定值时会触发预警。
                        </div>            
                    </div>
                </div>
                <!--请求错误-->
                <div class="tab-pane fade" id="tab-error">
                    <div class="widget-body no-padding">
                        <div class="widget-body no-padding">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="15%">配置项</th>
                                        <th width="85%">配置值</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>监控频率</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                        <select name="row[error][pl]" class="selectpicker">
                                                            <option value ="60" <?php if($addon['config'][2]["value"]["pl"]==60): ?> selected <?php endif; ?>>1分钟</option>
                                                            <option value ="180" <?php if($addon['config'][2]["value"]["pl"]==180): ?> selected <?php endif; ?>>3分钟</option>
                                                            <option value ="300" <?php if($addon['config'][2]["value"]["pl"]==300): ?> selected <?php endif; ?>>5分钟</option>
                                                            <option value ="600" <?php if($addon['config'][2]["value"]["pl"]==600): ?> selected <?php endif; ?>>10分钟</option>
                                                            <option value ="1800" <?php if($addon['config'][2]["value"]["pl"]==1800): ?> selected <?php endif; ?>>30分钟</option>
                                                            <option value="3600" <?php if($addon['config'][2]["value"]["pl"]==3600): ?> selected <?php endif; ?>>1小时</option>
                                                            <option value="7200" <?php if($addon['config'][2]["value"]["pl"]==7200): ?> selected <?php endif; ?>>2小时</option>
                                                            <option value="14400" <?php if($addon['config'][2]["value"]["pl"]==14400): ?> selected <?php endif; ?>>4小时</option>
                                                            <option value="21600" <?php if($addon['config'][2]["value"]["pl"]==21600): ?> selected <?php endif; ?>>6小时</option>
                                                            <option value="28800" <?php if($addon['config'][2]["value"]["pl"]==28800): ?> selected <?php endif; ?>>8小时</option>
                                                            <option value="43200" <?php if($addon['config'][2]["value"]["pl"]==43200): ?> selected <?php endif; ?>>12小时</option>
                                                            <option value="86400" <?php if($addon['config'][2]["value"]["pl"]==86400): ?> selected <?php endif; ?>>1天</option>
                                                          </select>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>HTTP状态码</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="text" name="row[error][sj]"
                                                        value='<?php echo $addon['config'][2]["value"]["sj"]; ?>'
                                                        class="form-control" placeholder="请输入需要监控的状态码，多个使用英文逗号分隔" data-tip="多个状态码用英文逗号隔开">
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>百分比</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12 input-group">
                                                    <input type="text" name="row[error][zb]"
                                                        value='<?php echo $addon['config'][2]["value"]["zb"]; ?>'
                                                        class="form-control" data-tip="" aria-describedby="erro_zb">
                                                        <span class="input-group-addon" id="erro_zb">%</span>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>是否开启预警</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="radio" name="row[error][open]" value="0" <?php if($addon['config'][2]["value"]["open"]==0): ?> checked <?php endif; ?>> 关闭
                                                    <input type="radio" name="row[error][open]" value="1" <?php if($addon['config'][2]["value"]["open"]==1): ?> checked <?php endif; ?>> 开启
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="alert alert-info-light" style="margin-bottom:10px;">
                            <b>请求错误监控：</b><br>主要针对一段时间内接口调用错误率过高<br>
                            1、设定Http状态码,500,503,404,多个状态码用英文逗号隔开<br>
                            2、设置命中率,支持 两位小数点，命中率达到或大于设定值时会触发预警。
                        </div>            
                    </div>
                </div>
                <!--IP异常-->
                <div class="tab-pane fade" id="tab-ip">
                    <div class="widget-body no-padding">
                        <div class="widget-body no-padding">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="15%">配置项</th>
                                        <th width="85%">配置值</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>监控频率</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                        <select name="row[ip][pl]" class="selectpicker">
                                                            <option value ="60" <?php if($addon['config'][3]["value"]["pl"]==60): ?> selected <?php endif; ?>>1分钟</option>
                                                            <option value ="180" <?php if($addon['config'][3]["value"]["pl"]==180): ?> selected <?php endif; ?>>3分钟</option>
                                                            <option value ="300" <?php if($addon['config'][3]["value"]["pl"]==300): ?> selected <?php endif; ?>>5分钟</option>
                                                            <option value ="600" <?php if($addon['config'][3]["value"]["pl"]==600): ?> selected <?php endif; ?>>10分钟</option>
                                                            <option value ="1800" <?php if($addon['config'][3]["value"]["pl"]==1800): ?> selected <?php endif; ?>>30分钟</option>
                                                            <option value="3600" <?php if($addon['config'][3]["value"]["pl"]==3600): ?> selected <?php endif; ?>>1小时</option>
                                                            <option value="7200" <?php if($addon['config'][3]["value"]["pl"]==7200): ?> selected <?php endif; ?>>2小时</option>
                                                            <option value="14400" <?php if($addon['config'][3]["value"]["pl"]==14400): ?> selected <?php endif; ?>>4小时</option>
                                                            <option value="21600" <?php if($addon['config'][3]["value"]["pl"]==21600): ?> selected <?php endif; ?>>6小时</option>
                                                            <option value="28800" <?php if($addon['config'][3]["value"]["pl"]==28800): ?> selected <?php endif; ?>>8小时</option>
                                                            <option value="43200" <?php if($addon['config'][3]["value"]["pl"]==43200): ?> selected <?php endif; ?>>12小时</option>
                                                            <option value="86400" <?php if($addon['config'][3]["value"]["pl"]==86400): ?> selected <?php endif; ?>>1天</option>
                                                          </select>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>IP白名单</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="text" name="row[ip][white]"
                                                        value='<?php echo $addon['config'][3]["value"]["white"]; ?>' placeholder="请输入IP白名单"
                                                        class="form-control" data-tip="多个IP地址中间用英文逗号分开">
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>百分比</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12 input-group">
                                                    <input type="text" name="row[ip][zb]"
                                                        value='<?php echo $addon['config'][3]["value"]["zb"]; ?>'
                                                        class="form-control" data-tip="" aria-describedby="ip_zb">
                                                        <span class="input-group-addon" id="ip_zb">%</span>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>是否开启预警</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="radio" name="row[ip][open]" value="0" <?php if($addon['config'][3]["value"]["open"]==0): ?> checked <?php endif; ?>> 关闭
                                                    <input type="radio" name="row[ip][open]" value="1" <?php if($addon['config'][3]["value"]["open"]==1): ?> checked <?php endif; ?>> 开启
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="alert alert-info-light" style="margin-bottom:10px;">
                            <b>IP异常监控：</b><br>主要针对一段时间内大量固定IP请求，类似机器人请求;<br>
                            1、设置IP白名单(可不填),多个IP地址中间用英文逗号分开<br>
                            2、设置重复率，支持两位小数点,当IP重复率达到或大于设定值时,触发预警。
                        </div>  
                    </div>
                </div>
                <!--请求量-->
                <div class="tab-pane fade" id="tab-count">
                    <div class="widget-body no-padding">
                        <div class="widget-body no-padding">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th width="15%">配置项</th>
                                        <th width="85%">配置值</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>监控频率</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                        <select name="row[count][pl]" class="selectpicker">
                                                            <option value ="60" <?php if($addon['config'][4]["value"]["pl"]==60): ?> selected <?php endif; ?>>1分钟</option>
                                                            <option value ="180" <?php if($addon['config'][4]["value"]["pl"]==180): ?> selected <?php endif; ?>>3分钟</option>
                                                            <option value ="300" <?php if($addon['config'][4]["value"]["pl"]==300): ?> selected <?php endif; ?>>5分钟</option>
                                                            <option value ="600" <?php if($addon['config'][4]["value"]["pl"]==600): ?> selected <?php endif; ?>>10分钟</option>
                                                            <option value ="1800" <?php if($addon['config'][4]["value"]["pl"]==1800): ?> selected <?php endif; ?>>30分钟</option>
                                                            <option value="3600" <?php if($addon['config'][4]["value"]["pl"]==3600): ?> selected <?php endif; ?>>1小时</option>
                                                            <option value="7200" <?php if($addon['config'][4]["value"]["pl"]==7200): ?> selected <?php endif; ?>>2小时</option>
                                                            <option value="14400" <?php if($addon['config'][4]["value"]["pl"]==14400): ?> selected <?php endif; ?>>4小时</option>
                                                            <option value="21600" <?php if($addon['config'][4]["value"]["pl"]==21600): ?> selected <?php endif; ?>>6小时</option>
                                                            <option value="28800" <?php if($addon['config'][4]["value"]["pl"]==28800): ?> selected <?php endif; ?>>8小时</option>
                                                            <option value="43200" <?php if($addon['config'][4]["value"]["pl"]==43200): ?> selected <?php endif; ?>>12小时</option>
                                                            <option value="86400" <?php if($addon['config'][4]["value"]["pl"]==86400): ?> selected <?php endif; ?>>1天</option>
                                                          </select>
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>最大请求数量</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="text" name="row[count][max]"
                                                        value='<?php echo $addon['config'][4]["value"]["max"]; ?>'
                                                        class="form-control" data-tip="">
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                    
                                    <tr>
                                        <td>是否开启预警</td>
                                        <td>
                                            <div class="row">
                                                <div class="col-sm-8 col-xs-12">
                                                    <input type="radio" name="row[count][open]" value="0" <?php if($addon['config'][4]["value"]["open"]==0): ?> checked <?php endif; ?>> 关闭
                                                    <input type="radio" name="row[count][open]" value="1" <?php if($addon['config'][4]["value"]["open"]==1): ?> checked <?php endif; ?>> 开启
                                                </div>
                                                <div class="col-sm-4"></div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>

                        </div>
                        <div class="alert alert-info-light" style="margin-bottom:10px;">
                            请求量监控：</b><br>主要针对一段时间内接口大量请求<br>
                            1、设置单位时间内接口最大请求量,当请求量达到或者大于设定时触发预警
                        </div>  
                    </div>
                </div>
                <!--footer-->
                <div class="form-group layer-footer">
                    <label class="control-label col-xs-12 col-sm-2"></label>
                    <div class="col-xs-12 col-sm-8">
                        <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
                        <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
                    </div>
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
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
