<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:83:"/www/wwwroot/iotm3.yafrm.com/public/../application/admin/view/notice/add_range.html";i:1651916928;s:71:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/layout/default.html";i:1649987434;s:68:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/meta.html";i:1649987434;s:70:"/www/wwwroot/iotm3.yafrm.com/application/admin/view/common/script.html";i:1649987434;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2">类型:</label>
        <div class="col-xs-12 col-sm-8">
       <select class="form-control selectpicker" id="c-rtype">
       <option value='0'>为空</option>
       <option value='1'>数值</option>
       <option value='2'>组建单元下某值</option>
       <option value='3'>组别</option>
       <option value='4'>离线</option>
       </select>
        </div>
        
        
    </div>
    <div id="type_1" style="display:none;">
    
        <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">数值:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-fixedvalue" data-rule="required"  class="form-control" name="row[fixedvalue]" type="number" value="0">
        </div>
    </div>
     
    </div>
       <div id="type_2" style="display:none;">
        <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">组建单元:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-did" data-rule="required" class="form-control selectpicker" name="row[did]">
                <?php if(is_array($unitlist) || $unitlist instanceof \think\Collection || $unitlist instanceof \think\Paginator): if( count($unitlist)==0 ) : echo "" ;else: foreach($unitlist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">传感器:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-sensor" data-rule="required" class="form-control selectpicker" name="row[sensor]">
              
            </select>
        </div>
    </div>
   
    </div>
    <div id="type_3" style="display:none;">
        <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">组别:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-groupid" data-rule="required" class="form-control selectpicker" name="row[groupid]">
                <?php if(is_array($grouplist) || $grouplist instanceof \think\Collection || $grouplist instanceof \think\Paginator): if( count($grouplist)==0 ) : echo "" ;else: foreach($grouplist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">计算方式:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-calculation" data-rule="required" class="form-control selectpicker" name="row[calculation]">
                  <option value='avg'>平均</option>
      			 <option value='stddev'>方差</option>
      			 <option value='min'>最小值</option>
       			<option value='max'>最大值</option>
            </select>
        </div>
    </div>
    
    </div>
           <div id="type_4" style="display:none;">
        <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">组建单元:</label>
        <div class="col-xs-12 col-sm-8">
            <select  id="c-ldid" data-rule="required" class="form-control selectpicker" name="row[did]">
                <?php if(is_array($unitlist) || $unitlist instanceof \think\Collection || $unitlist instanceof \think\Paginator): if( count($unitlist)==0 ) : echo "" ;else: foreach($unitlist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), explode(',',""))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
   
   
    </div>
    
    
 
    <div class="form-group" id="label" style="display:none;">
        <label class="control-label col-xs-12 col-sm-2">标签:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-unitlabel" data-rule="required"  class="form-control" name="row[unitlabel]" type="text" value="">
        </div>
    </div>
    
    
  
 
</form>
<script src="/assets/js/jquery.3.4.1.min.js"></script>
<script>
var sensorList = new Array();
function getSensorList(did){
	 sensorList =  new Array();
	 $.ajax({
         url: "/<?php echo $url; ?>/notice/getSensorList",
         type: "get",
         data:{
        	 'did':did
         },
         dataType: "json",
         async: false,
         success: function (result) {
             if(result.code == '1')
             {
            	 var option = '';
            	if(result.data.length>0){
            		 $('#c-sensor').html('');
            		 $('#c-sensor').val('');
                for(var i in result.data){
                	option +='<option  value="'+result.data[i].id+'">'+result.data[i].title+'('+result.data[i].label+')</option>'; 
                	sensorList[result.data[i].id] = result.data[i].label;
                }
                $('#c-sensor').append(option);
            	}else{
            		alert('暂无传感器');
            	}
                
             }
             
         }
     });
}
getSensorList($('#c-did').val());
$('#c-did').change(function(){
	if($('#c-rtype').val() !=4){
	getSensorList($('#c-did').val());
	}
	
});
$('#c-sensor').change(function(){
	var val = $('#c-sensor').val();
	$('#c-unitlabel').val(sensorList[val]);
});

$('#c-rtype').change(function(){
	var val = $('#c-rtype').val();
	switch(val){
	case '0':
		$('#type_1').hide();
		$('#type_2').hide();
		$('#type_3').hide();
		$('#type_4').hide();
		$('#label').hide();
		break;
	case '1':
		$('#type_1').show();
		$('#type_2').hide();
		$('#type_3').hide();
		$('#type_4').hide();
		$('#label').hide();
		break;
	case '2':
		$('#type_1').hide();
		$('#type_2').show();
		$('#type_3').hide();
		$('#type_4').hide();
		$('#label').show();
		break;
	case '3':
		$('#type_1').hide();
		$('#type_2').hide();
		$('#type_3').show();
		$('#type_4').hide();
		$('#label').show();
		break;
	case '4':
		$('#type_1').hide();
		$('#type_2').hide();
		$('#type_3').hide();
		$('#type_4').show();
		$('#label').hide();
		break;
	}
	
	
	
});

function callBack(){
	
	var data = {
			rtype : $('#c-rtype').val(),
			fixedvalue : $('#c-fixedvalue').val(),
			did:$('#c-did').val(),
			sensor:$('#c-sensor').val(),
			groupid:$('#c-groupid').val(),
			calculation:$('#c-calculation').val(),
			unitlabel:$('#c-unitlabel').val(),
			ldid:$('#c-ldid').val()
	    };
	    return data;
}

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
