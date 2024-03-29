<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"C:\eclipse-workspace\iot\addons\address\view\index\index.html";i:1567072488;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8"/>

    <title>地图位置(经纬度)选择插件 - FastAdmin</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@3.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="https://cdn.demo.fastadmin.net/assets/css/frontend.css" rel="stylesheet">

    <!-- Plugin CSS -->
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://cdn.jsdelivr.net/npm/html5shiv@3.7.3/dist/html5shiv.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/respond.js@1.4.2/dest/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<div class="container">

    <div class="bs-docs-section clearfix">
        <div class="row">
            <div class="col-lg-12">
                <div class="page-header">
                    <h2 id="navbar">地图位置(经纬度)选择示例</h2>
                </div>

                <div class="bs-component">
                    <form action="" method="post" role="form">
                        <div class="form-group">
                            <label for=""></label>
                            <input type="text" class="form-control" name="" id="address" placeholder="地址">
                        </div>
                        <div class="form-group">
                            <label for=""></label>
                            <input type="text" class="form-control" name="" id="lng" placeholder="经度">
                        </div>
                        <div class="form-group">
                            <label for=""></label>
                            <input type="text" class="form-control" name="" id="lat" placeholder="纬度">
                        </div>

                        <button type="button" class="btn btn-primary" data-toggle='addresspicker' data-input-id="address" data-lng-id="lng" data-lat-id="lat">点击选择地址获取经纬度</button>
                    </form>
                </div>

                <div class="page-header">
                    <h2 id="code">调用代码</h2>
                </div>
                <div class="bs-component">
                        <textarea class="form-control" rows="17">
<form action="" method="post" role="form">
    <div class="form-group">
        <label for=""></label>
        <input type="text" class="form-control" name="" id="address" placeholder="地址">
    </div>
    <div class="form-group">
        <label for=""></label>
        <input type="text" class="form-control" name="" id="lng" placeholder="经度">
    </div>
    <div class="form-group">
        <label for=""></label>
        <input type="text" class="form-control" name="" id="lat" placeholder="纬度">
    </div>

    <button type="button" class="btn btn-primary" data-toggle='addresspicker' data-input-id="address" data-lng-id="lng" data-lat-id="lat">点击选择地址获取经纬度</button>
</form>
                        </textarea>
                </div>
                <div class="page-header">
                    <h2 id="navbar">参数说明</h2>
                </div>

                <div class="bs-component">
                    <table class="table table-condensed table-hover">
                        <thead>
                        <tr>
                            <th>参数</th>
                            <th>释义</th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr>
                            <td>data-input-id</td>
                            <td>填充地址的文本框ID</td>
                        </tr>
                        <tr>
                            <td>data-lng-id</td>
                            <td>填充经度的文本框ID</td>
                        </tr>
                        <tr>
                            <td>data-lat-id</td>
                            <td>填充纬度的文本框ID</td>
                        </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>

</div>
<script type="text/javascript">
    var require = {
        config: <?php echo json_encode($config ); ?>
    }
    ;
</script>

<script>
    require.callback = function () {
        define('addons/address', ['jquery', 'bootstrap', 'frontend', 'template'], function ($, undefined, Frontend, Template) {
            var Controller = {
                index: function () {
                    ;
                }
            };
            return Controller;
        });
        define('lang', function () {
            return [];
        });
    }
</script>


<script src="/assets/js/require.min.js" data-main="/assets/js/require-frontend.min.js?v=<?php echo $site['version']; ?>"></script>
</body>
</html>