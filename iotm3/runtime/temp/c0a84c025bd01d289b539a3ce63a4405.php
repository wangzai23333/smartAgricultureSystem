<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:60:"C:\eclipse-workspace\iot\addons\address\view\index\amap.html";i:1567076543;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
    <title>地址选择器</title>
    <link rel="stylesheet" href="/assets/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="/assets/css/fastadmin.min.css"/>
    <link rel="stylesheet" href="/assets/libs/font-awesome/css/font-awesome.min.css"/>
    <style type="text/css">
        body {
            margin: 0;
            padding: 0;
        }

        #container {
            position: absolute;
            left: 0;
            top: 0;
            right: 0;
            bottom: 0;
        }

        .confirm {
            position: absolute;
            bottom: 30px;
            right: 4%;
            z-index: 99;
            height: 50px;
            width: 50px;
            line-height: 50px;
            font-size: 15px;
            text-align: center;
            background-color: white;
            background: #1ABC9C;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 50%;
        }

        .search {
            position: absolute;
            width: 400px;
            top: 0;
            left: 50%;
            padding: 5px;
            margin-left: -200px;
        }

        .amap-marker-label {
            border: 0;
            background-color: transparent;
        }

        .info {
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            border-radius: .25rem;
            position: fixed;
            top: 2rem;
            background-color: white;
            width: auto;
            min-width: 22rem;
            border-width: 0;
            left: 1.8rem;
            box-shadow: 0 2px 6px 0 rgba(114, 124, 245, .5);
        }
    </style>
</head>
<body>
<div class="search">
    <div class="input-group">
        <input type="text" id="place" name="q" class="form-control" placeholder="输入地点"/>
        <span class="input-group-btn">
            <button type="submit" name="search" id="search-btn" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
        </span>
    </div>
</div>
<div class="confirm">确定</div>
<div id="container"></div>
<script type="text/javascript" src="//webapi.amap.com/maps?v=1.4.11&key=<?php echo (isset($config['amapkey']) && ($config['amapkey'] !== '')?$config['amapkey']:''); ?>&plugin=AMap.ToolBar,AMap.Autocomplete,AMap.PlaceSearch,AMap.Geocoder"></script>
<!-- UI组件库 1.0 -->
<script src="//webapi.amap.com/ui/1.0/main.js?v=1.0.11"></script>
<script src="/assets/libs/jquery/dist/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        var as, address, map, lat, lng, geocoder;
        var init = function () {
            AMapUI.loadUI(['misc/PositionPicker', 'misc/PoiPicker'], function (PositionPicker, PoiPicker) {
                //加载PositionPicker，loadUI的路径参数为模块名中 'ui/' 之后的部分
                map = new AMap.Map('container', {
                    zoom: parseInt('<?php echo $config['zoom']; ?>')
                });
                geocoder = new AMap.Geocoder({
                    radius: 1000 //范围，默认：500
                });
                var positionPicker = new PositionPicker({
                    mode: 'dragMarker',//设定为拖拽地图模式，可选'dragMap'、'dragMarker'，默认为'dragMap'
                    map: map//依赖地图对象
                });
                //输入提示
                var autoOptions = {
                    input: "place"
                };

                var relocation = function (lnglat) {
                    lng = lnglat.lng;
                    lat = lnglat.lat;
                    map.panTo([lng, lat]);
                    positionPicker.start(lnglat);
                    geocoder.getAddress(lng + ',' + lat, function (status, result) {
                        if (status === 'complete' && result.regeocode) {
                            var address = result.regeocode.formattedAddress;
                            var label = '<div class="info">地址:' + address + '<br>经度:' + lng + '<br>纬度:' + lat + '</div>';
                            positionPicker.marker.setLabel({
                                content: label //显示内容
                            });
                        } else {
                            console.log(JSON.stringify(result));
                        }
                    });
                };
                var auto = new AMap.Autocomplete(autoOptions);

                //构造地点查询类
                var placeSearch = new AMap.PlaceSearch({
                    map: map
                });
                //注册监听，当选中某条记录时会触发
                AMap.event.addListener(auto, "select", function (e) {
                    placeSearch.setCity(e.poi.adcode);
                    placeSearch.search(e.poi.name);  //关键字查询查询
                });
                AMap.event.addListener(map, 'click', function (e) {
                    relocation(e.lnglat);
                });

                //加载工具条
                var tool = new AMap.ToolBar();
                map.addControl(tool);

                var poiPicker = new PoiPicker({
                    input: 'place',
                    placeSearchOptions: {
                        map: map,
                        pageSize: 6 //关联搜索分页
                    }
                });
                poiPicker.on('poiPicked', function (poiResult) {
                    poiPicker.hideSearchResults();
                    $('.poi .nearpoi').text(poiResult.item.name);
                    $('.address .info').text(poiResult.item.address);
                    $('#address').val(poiResult.item.address);
                    $("#place").val(poiResult.item.name);

                    relocation(poiResult.item.location);
                });

                positionPicker.on('success', function (positionResult) {
                    as = positionResult.position;
                    address = positionResult.address;
                    lat = as.lat;
                    lng = as.lng;
                });
                positionPicker.on('fail', function (positionResult) {
                    address = '';
                });
                positionPicker.start();
            });
        };

        //点击确定后执行回调赋值
        var close = function (data) {
            var index = parent.Layer.getFrameIndex(window.name);
            var callback = parent.$("#layui-layer" + index).data("callback");
            //再执行关闭
            parent.Layer.close(index);
            //再调用回传函数
            if (typeof callback === 'function') {
                callback.call(undefined, data);
            }
        };

        //点击搜索按钮
        $(document).on('click', '.confirm', function () {
            var zoom = map.getZoom();
            var data = {lat: lat, lng: lng, zoom: zoom, address: address};
            close(data);
        });
        init();
    });
</script>
</body>
</html>