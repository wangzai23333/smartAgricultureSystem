<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:61:"C:\eclipse-workspace\iot\addons\address\view\index\baidu.html";i:1567074175;}*/ ?>
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

        label.BMapLabel {
            max-width: inherit;
            padding: .75rem 1.25rem;
            margin-bottom: 1rem;
            background-color: white;
            width: auto;
            min-width: 22rem;
            border: none;
            box-shadow: 0 2px 6px 0 rgba(114, 124, 245, .5);
        }

    </style>
</head>
<body>
<div class="search">
    <div class="input-group">
        <input type="text" id="place" name="q" class="form-control" placeholder="输入地点"/>
        <div id="searchResultPanel" style="border:1px solid #C0C0C0;width:150px;height:auto; display:none;"></div>
        <span class="input-group-btn">
            <button type="button" name="search" id="address" class="btn btn-success">
                <i class="fa fa-search"></i>
            </button>
        </span>
    </div>
</div>
<div class="confirm">确定</div>
<div id="container"></div>
<script type="text/javascript" src="//api.map.baidu.com/api?v=2.0&ak=<?php echo (isset($config['baidukey']) && ($config['baidukey'] !== '')?$config['baidukey']:''); ?>"></script>
<script src="/assets/libs/jquery/dist/jquery.min.js"></script>
<script type="text/javascript">
    $(function () {
        // 百度地图API功能
        function G(id) {
            return document.getElementById(id);
        }

        var map, marker, searchService, address = null, lng, lat;

        var init = function () {
            map = new BMap.Map("container"); // 创建地图实例
            var point = new BMap.Point(<?php echo $lng; ?>, <?php echo $lat; ?>); // 创建点坐标
            map.enableScrollWheelZoom(true); //开启鼠标滚轮缩放
            map.centerAndZoom(point, parseInt("<?php echo $config['zoom']; ?>")); // 初始化地图，设置中心点坐标和地图级别

            var size = new BMap.Size(10, 20);
            map.addControl(new BMap.CityListControl({
                anchor: BMAP_ANCHOR_TOP_LEFT,
                offset: size,
            }));

            var geoc = new BMap.Geocoder();

            var addpoint = function (point) {
                //通过点击百度地图，可以获取到对应的point, 由point的lng、lat属性就可以获取对应的经度纬度
                var pt = point;
                geoc.getLocation(pt, function (rs) {
                    //对象可以获取到详细的地址信息
                    address = rs.address;
                    deletePoint();
                    var mk = new BMap.Marker(pt);
                    map.addOverlay(mk);
                    map.panTo(pt);
                    var label = new BMap.Label('<div class="info">地址:' + address + '<br>经度:' + pt.lng + '<br>纬度:' + pt.lat + '</div>', {offset: new BMap.Size(16, 20)});
                    label.setStyle({
                        border: 'none',
                        padding: '.75rem 1.25rem'
                    });
                    mk.setLabel(label);
                    //将对应的HTML元素设置值
                    lng = pt.lng;
                    lat = pt.lat;
                });
            };

            if ("<?php echo $lng; ?>" != '' && "<?php echo $lat; ?>" != '') {
                addpoint(point);
            }

            ac = new BMap.Autocomplete({"input": "place", "location": map}); //建立一个自动完成的对象
            ac.addEventListener("onhighlight", function (e) {  //鼠标放在下拉列表上的事件
                var str = "";
                var _value = e.fromitem.value;
                var value = "";
                if (e.fromitem.index > -1) {
                    value = _value.province + _value.city + _value.district + _value.street + _value.business;
                }
                str = "FromItem<br />index = " + e.fromitem.index + "<br />value = " + value;

                value = "";
                if (e.toitem.index > -1) {
                    _value = e.toitem.value;
                    value = _value.province + _value.city + _value.district + _value.street + _value.business;
                }
                str += "<br />ToItem<br />index = " + e.toitem.index + "<br />value = " + value;
                G("searchResultPanel").innerHTML = str;
            });
            ac.addEventListener("onconfirm", function (e) {    //鼠标点击下拉列表后的事件
                var _value = e.item.value;
                myValue = _value.province + _value.city + _value.district + _value.street + _value.business;
                G("searchResultPanel").innerHTML = "onconfirm<br />index = " + e.item.index + "<br />myValue = " + myValue;
                setPlace();
            });

            function setPlace() {
                map.clearOverlays();    //清除地图上所有覆盖物
                function myFun() {
                    var result = local.getResults().getPoi(0);
                    var pp = result.point;    //获取第一个智能搜索的结果
                    map.centerAndZoom(pp, 18);
                    map.addOverlay(new BMap.Marker(pp));    //添加标注
                    lng = pp.lng;
                    lat = pp.lat;
                    address = result.address;
                }

                var local = new BMap.LocalSearch(map, { //智能搜索
                    onSearchComplete: myFun
                });
                local.search(myValue);
            }

            map.addEventListener("click", function (e) {
                //通过点击百度地图，可以获取到对应的point, 由point的lng、lat属性就可以获取对应的经度纬度
                var pt = e.point;
                addpoint(e.point);
            });

            /**
             * 清除覆盖物
             */
            function deletePoint() {
                var allOverlay = map.getOverlays();
                for (var i = 0; i < allOverlay.length; i++) {
                    map.removeOverlay(allOverlay[i]);
                }
            }
        };

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

        //点击确定后执行回调赋值
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