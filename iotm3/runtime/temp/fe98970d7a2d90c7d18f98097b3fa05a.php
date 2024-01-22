<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:79:"C:\eclipse-workspace\iot\public/../application/admin\view\fastworker\index.html";i:1617086713;s:67:"C:\eclipse-workspace\iot\application\admin\view\layout\default.html";i:1605513288;s:64:"C:\eclipse-workspace\iot\application\admin\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\admin\view\common\script.html";i:1605513288;}*/ ?>
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
    .relation-item {margin-top:10px;}
    legend {padding-bottom:5px;font-size:14px;font-weight:600;}
    label {font-weight:normal;}
    .form-control{padding:6px 8px;}
    #extend-zone .col-xs-2 {margin-top:10px;padding-right:0;}
    #extend-zone .col-xs-2:nth-child(6n+0) {padding-right:15px;}
</style>
<div class="panel panel-default panel-intro">
    <div class="panel-heading">
        <ul class="nav nav-tabs">
            <li class="active"><a href="#server" data-toggle="tab"><?php echo __('Workerman'); ?></a></li>
            <li><a href="#gateway" data-toggle="tab"><?php echo __('GatewayWorker'); ?></a></li>
            <li><a href="#timer" data-toggle="tab"><?php echo __('Timer'); ?></a></li>
        </ul>
    </div>
    <div class="panel-body">
        <div id="myTabContent" class="tab-content">
            <div class="tab-pane fade active in" id="server">
                <p>
                    * 同时支持TCP、UDP、UNIXSOCKET，支持长连接，支持Websocket、HTTP、WSS、HTTPS等通讯协以及自定义协议。</br>
                    * 拥有定时器、异步socket客户端、异步Mysql、异步Redis、异步Http、异步消息队列等众多高性能组件。</br>
                    * 更为底层，可以在此基础上封装更多功能</br>
                    <a target="_blank" href="http://doc.workerman.net/">* 在线开发手册</a>
                </p>

                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="server" />

                            <div class="form-group">
                                <legend>服务模式<span style="font-size:12px;font-weight: normal;">(windows下不支持)</span></legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label class="control-label" data-toggle="tooltip" title='在守护进程模式下运行Workerman服务'>
                                            <input name="daemon" type="checkbox" value=" ">
                                            守护进程模式
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <legend>服务配置</legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>绑定主机</label>
                                        <input type="text" class="form-control" name="host" value="127.0.0.1" data-toggle="tooltip" title="默认为当前Host" placeholder="Host">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>绑定端口</label>
                                        <input type="number" class="form-control" name="port" value="2345" data-toggle="tooltip" title="默认端口号为2345" placeholder="Port">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>实例名称</label>
                                        <input type="text" class="form-control" name="name" value="fastworker" data-toggle="tooltip" title="默认为fastworker" placeholder="实例名称">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>实例进程数<a target="_blank" href="http://doc.workerman.net/faq/processes-count.html">设置参考</a></label>
                                        <input type="number" class="form-control" name="count" value="1" data-toggle="tooltip" title="默认为1" placeholder="实例进程数">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>选择通讯协议<a target="_blank" href="http://doc.workerman.net/protocols/why-protocols.html">协议的作用</a></label>
                                        <?php echo build_select('protocol',$protocolList,null,['class'=>'form-control selectpicker']);; ?>
                                    </div>
                                </div>

                            </div>

                            <div class="form-group">
                                <legend>服务类配置<span style="font-size:12px;font-weight: normal;">（设置服务类后主机、端口、协议以类中声明为有效）(windows下仅可设置一个服务类)</span></legend>
                                <div class="row" style="margin-top:15px;">
                                    <div class="col-xs-12">
                                        <a href="javascript:;" class="btn btn-primary btn-sm btn-newrelation" data-index="1">添加服务类</a>
                                    </div>
                                </div>


                            </div>

                            <div class="form-group">
                                <legend>执行指令<span style="font-size:12px;font-weight: normal;">(windows下仅有启动指令)</span></legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>选择指令</label>
                                        <?php echo build_select('action',$actionList,null,['class'=>'form-control selectpicker']);; ?>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="form-group">
                                <legend>在项目目录中执行命令行</legend>
                                <textarea class="form-control" data-toggle="tooltip" title="将命令复制到命令行进行执行" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>





            <div class="tab-pane fade " id="gateway">
                <p>
                    * GatewayWorker使用经典的Gateway和Worker进程模型</br>
                    * 基于Workerman开发，支持分布式部署、高并发、全局广播或者向任意客户端推送数据、对象或者资源永久保持</br>
                    * 高性能，方便与其它项目集成，支持HHVM、代码热更新、支持长连接、各种应用层协议</br>
                    <a target="_blank" href="http://doc2.workerman.net/">* 在线开发手册</a>
                </p>

                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="gateway" />

                            <div class="form-group">
                                <legend>服务部署<span style="font-size:12px;font-weight: normal;"><a target="_blank" href="http://doc2.workerman.net/how-distributed.html">（部署参考）</a></span></legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label class="control-label" data-toggle="tooltip" title='部署Register服务'>
                                            <input name="deploy[]" data-deploy="0" type="checkbox" value="register" checked>
                                            Register服务配置
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <label class="control-label" data-toggle="tooltip" title='部署BusinessWorker服务'>
                                            <input name="deploy[]" data-deploy="1" type="checkbox" value="business" checked>
                                            BusinessWorker服务配置
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <label class="control-label" data-toggle="tooltip" title='部署Gateway服务'>
                                            <input name="deploy[]" data-deploy="2" type="checkbox" value="gateway" checked>
                                            Gateway服务配置
                                        </label>
                                    </div>

                                </div>
                            </div>
                            <!-- Register服务配置 START -->
                            <div class="form-group" id="deploy-zone0">
                                <legend>Register服务配置</legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>注册地址</label>
                                        <input type="text" class="form-control" name="rhost" value="127.0.0.1" data-toggle="tooltip" title="默认地址为0.0.0.0" placeholder="Host">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>注册端口</label>
                                        <input type="number" class="form-control" name="rport" value="1236" data-toggle="tooltip" title="默认端口号为1236" placeholder="Port">
                                    </div>
                                </div>
                            </div>
                            <!-- BusinessWorker服务配置 START -->
                            <div class="form-group" id="deploy-zone1">
                                <legend>BusinessWorker服务配置</legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>实例名称</label>
                                        <input type="text" class="form-control" name="bname" value="BusinessWorker" data-toggle="tooltip" title="默认为fastworker" placeholder="实例名称">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>实例进程数<a target="_blank" href="http://doc.workerman.net/faq/processes-count.html">设置参考</a></label>
                                        <input type="number" class="form-control" name="bcount" value="1" data-toggle="tooltip" title="默认为1" placeholder="实例进程数">
                                    </div>
                                    <div class="col-xs-3">
                                        <label>填写业务类</label>
                                        <input type="text" class="form-control" name="event"  data-toggle="tooltip" title="例如：addons\\fastworker\\example\\GatewayWorker" placeholder="回调类">
                                    </div>
                                </div>
                            </div>
                            <!-- Gateway服务配置 START -->
                            <div id="deploy-zone2">
                                <!--part1-->
                                <div class="form-group" >
                                    <legend>Gateway服务配置</legend>
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <label>绑定地址</label>
                                            <input type="text" class="form-control" name="host" value="127.0.0.1" data-toggle="tooltip" title="默认为当前Host" placeholder="Host">
                                        </div>
                                        <div class="col-xs-3">
                                            <label>绑定端口</label>
                                            <input type="number" class="form-control" name="port" value="2345" data-toggle="tooltip" title="默认端口号为2345" placeholder="Port">
                                        </div>
                                        <div class="row">
                                            <div class="col-xs-3">
                                                <label>选择通讯协议<a target="_blank" href="http://doc.workerman.net/protocols/why-protocols.html">协议的作用</a></label>
                                                <?php echo build_select('protocol',$protocolList,null,['class'=>'form-control selectpicker']);; ?>
                                            </div>
                                        </div>
                                        <div class="col-xs-3">
                                            <label>实例名称</label>
                                            <input type="text" class="form-control" name="name" value="Fastworker" data-toggle="tooltip" title="实例名称" placeholder="实例名称">
                                        </div>
                                        <div class="col-xs-3">
                                            <label>实例进程数<a target="_blank" href="http://doc.workerman.net/faq/processes-count.html">设置参考</a></label>
                                            <input type="number" class="form-control" name="count" value="1" data-toggle="tooltip" title="实例进程数" placeholder="实例进程数">
                                        </div>
                                        <div class="col-xs-3">
                                            <label>部署主机内网IP</label>
                                            <input type="text" class="form-control" name="lanip" value="127.0.0.1" data-toggle="tooltip" title="部署主机内网IP" placeholder="Port">
                                        </div>
                                        <div class="col-xs-3">
                                            <label>起始端口</label>
                                            <input type="number" class="form-control" name="gport" value="2000" data-toggle="tooltip" title="起始端口" placeholder="Port">
                                        </div>
                                    </div>
                                </div>
                                <!--part2-->
                                <div class="form-group" >
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <label>心跳间隔</label>
                                            <input type="text" class="form-control" name="pil" value="55" data-toggle="tooltip" title="心跳间隔" placeholder="心跳间隔">
                                        </div>
                                        <div class="col-xs-3">
                                            <label>心跳次数<a target="_blank" href="http://doc2.workerman.net/heartbeat.html">设置参考</a></label>
                                            <input type="number" class="form-control" name="pnrl" value="1" data-toggle="tooltip" title="心跳次数" placeholder="心跳次数">
                                        </div>
                                    </div>
                                </div>
                                <!--part3-->
                                <div class="form-group" >
                                    <div class="row">
                                        <div class="col-xs-3">
                                            <label class="control-label" data-toggle="tooltip" title='在守护进程模式下运行'>
                                                <input name="daemon" type="checkbox" value=" ">
                                                守护进程模式
                                            </label>
                                        </div>
                                        <div class="col-xs-3">
                                            <label class="control-label" data-toggle="tooltip" title='服务端主动发送心跳数据{"type":"ping"}'>
                                                <input name="initiative" type="checkbox" value=" ">
                                                服务端发送心跳
                                            </label>
                                        </div>
                                    </div>
                                </div>

                            </div>


                            <div class="form-group">
                                <legend>执行指令<span style="font-size:12px;font-weight: normal;">(windows下仅有启动指令)</span></legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>选择指令</label>
                                        <?php echo build_select('action',$actionList,['start'],['class'=>'form-control selectpicker']);; ?>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group">
                                <legend>在项目目录中执行命令行</legend>
                                <textarea class="form-control" data-toggle="tooltip" title="将命令复制到命令行进行执行" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!--计时器部分-->

            <div class="tab-pane fade " id="timer">
                <p>
                    * 基于Workman Timer 执行定时任务</br>
                    * 默认执行回调类中的fire方法,如需指定方法则用'.'连接,如'addons\\fastworker\\example\\Timer.xxxxx'</br>
                    * 勾选单次执行则定时任务执行一次后将被销毁不再执行</br>
                    <a target="_blank" href="http://doc2.workerman.net/">* 在线开发手册</a>
                </p>

                <div class="row">
                    <div class="col-xs-12">
                        <form role="form">
                            <input type="hidden" name="commandtype" value="timer" />

                            <!-- 计时器服务配置 START -->
                            <div class="form-group">
                                <legend>Timer服务配置</legend>
                                <div class="row">

                                    <div class="col-xs-3">
                                        <label>填写调用方法</label>
                                        <input type="text" class="form-control" name="execution"  data-toggle="tooltip" title="例如：addons\\fastworker\\example\\Timer.fire" placeholder="调用类">
                                    </div>

                                    <div class="col-xs-3">
                                        <label>间隔时长(秒,精确到0.001)</label>
                                        <input type="number" class="form-control" name="interval"  data-toggle="tooltip" title="间隔时长,单位秒" placeholder="10" value="10">
                                    </div>

                                </div>
                            </div>

                            <div class="form-group">
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label class="control-label" data-toggle="tooltip" title='在守护进程模式下运行'>
                                            <input name="daemon" type="checkbox" value=" ">
                                            守护进程模式
                                        </label>
                                    </div>
                                    <div class="col-xs-3">
                                        <label class="control-label" data-toggle="tooltip" title='勾选后定时任务执行一次后将销毁'>
                                            <input name="single" type="checkbox" value=" ">
                                            单次执行
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <legend>执行指令<span style="font-size:12px;font-weight: normal;">(windows下仅有启动指令)</span></legend>
                                <div class="row">
                                    <div class="col-xs-3">
                                        <label>选择指令</label>
                                        <?php echo build_select('action',$actionList,['start'],['class'=>'form-control selectpicker']);; ?>
                                    </div>
                                </div>
                            </div>

                            <hr>
                            <div class="form-group">
                                <legend>在项目目录中执行命令行</legend>
                                <textarea class="form-control" data-toggle="tooltip" title="将命令复制到命令行进行执行" rel="command" rows="1" placeholder="请点击生成命令行"></textarea>
                            </div>
                            <div class="form-group">
                                <button type="button" class="btn btn-info btn-embossed btn-command"><?php echo __('生成命令行'); ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>



<script id="relationtpl" type="text/html">
    <div class="form-group">
        <div class="row">
            <div class="col-xs-3">
                <label>填写服务类</label>
                <input type="text" class="form-control" name="class[]"  data-toggle="tooltip" title="例如：addons\\fastworker\\example\\Server" placeholder="服务类">
            </div>

            <div class="col-xs-2">
                <label>&nbsp;</label>
                <a href="javascript:;" class="btn btn-danger btn-block btn-removerelation">移除</a>
            </div>
        </div>
    </div>
</script>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
