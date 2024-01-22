<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:75:"C:\eclipse-workspace\iot\public/../application/admin\view\linkage\edit.html";i:1621222143;s:67:"C:\eclipse-workspace\iot\application\admin\view\layout\default.html";i:1605513288;s:64:"C:\eclipse-workspace\iot\application\admin\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\admin\view\common\script.html";i:1605513288;}*/ ?>
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
  <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Weigh'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-weigh" data-rule="required" class="form-control" name="row[weigh]" type="number" value="<?php echo htmlentities($row['weigh']); ?>">
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">标题:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-title" data-rule="required" class="form-control" name="row[title]" type="text" value="<?php echo htmlentities($row['title']); ?>">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Referenceid'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
         <input type="hidden"  id="rcalculation"  name="row[rcalculation]" type="text" value="<?php echo $rdata['calculation']; ?>">
         <input type="hidden"  id="rdid"  name="row[rdid]" type="text" value="<?php echo $rdata['did']; ?>">
         <input type="hidden"  id="rfixedvalue"  name="row[rfixedvalue]" type="text" value="<?php echo $rdata['fixedvalue']; ?>">
         <input type="hidden"  id="rgroupid"  name="row[rgroupid]" type="text" value="<?php echo $rdata['groupid']; ?>">
         <input type="hidden"  id="rrtype"  name="row[rrtype]" type="text" value="<?php echo $rdata['rtype']; ?>">
         <input type="hidden"  id="rsensor"  name="row[rsensor]" type="text" value="<?php echo $rdata['sensorid']; ?>">
         <input type="hidden"  id="runitlabel"  name="row[runitlabel]" type="text" value="<?php echo $rdata['unitlabel']; ?>">
         <button  type="button" id="referenceidChange" class="btn btn-success btn-embossed">重新选择</button>
        </div>
        
       
    </div>
  
    
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">区间:</label>
        <div class="col-xs-12 col-sm-8">
           [
         <input type="hidden"  id="mcalculation"  name="row[mcalculation]" type="text" value="<?php echo $mindata['calculation']; ?>">
         <input type="hidden"  id="mdid"  name="row[mdid]" type="text" value="<?php echo $mindata['did']; ?>">
         <input type="hidden"  id="mfixedvalue"  name="row[mfixedvalue]" type="text" value="<?php echo $mindata['fixedvalue']; ?>">
         <input type="hidden"  id="mgroupid"  name="row[mgroupid]" type="text" value="<?php echo $mindata['groupid']; ?>">
         <input type="hidden"  id="mrtype"  name="row[mrtype]" type="text" value="<?php echo $mindata['rtype']; ?>">
         <input type="hidden"  id="msensor"  name="row[msensor]" type="text" value="<?php echo $mindata['sensorid']; ?>">
         <input type="hidden"  id="munitlabel"  name="row[munitlabel]" type="text" value="<?php echo $mindata['unitlabel']; ?>">
         <button  type="button" id="minidChange" class="btn btn-success btn-embossed">重新选择</button>
       ,
         <input type="hidden"  id="maxcalculation"  name="row[maxcalculation]" type="text" value="<?php echo $maxdata['calculation']; ?>">
         <input type="hidden"  id="maxdid"  name="row[maxdid]" type="text" value="<?php echo $maxdata['did']; ?>">
         <input type="hidden"  id="maxfixedvalue"  name="row[maxfixedvalue]" type="text" value="<?php echo $maxdata['fixedvalue']; ?>">
         <input type="hidden"  id="maxgroupid"  name="row[maxgroupid]" type="text" value="<?php echo $maxdata['groupid']; ?>">
         <input type="hidden"  id="maxrtype"  name="row[maxrtype]" type="text" value="<?php echo $maxdata['rtype']; ?>">
         <input type="hidden"  id="maxsensor"  name="row[maxsensor]" type="text" value="<?php echo $maxdata['sensorid']; ?>">
         <input type="hidden"  id="maxunitlabel"  name="row[maxunitlabel]" type="text" value="<?php echo $maxdata['unitlabel']; ?>">
         <button  type="button" id="maxidChange" class="btn btn-success btn-embossed">重新选择</button>
         )
       
        </div>
    </div>
  
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Did'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
         <select  id="c-did" data-rule="required" class="form-control selectpicker" name="row[did]">
                <?php if(is_array($unitlist) || $unitlist instanceof \think\Collection || $unitlist instanceof \think\Paginator): if( count($unitlist)==0 ) : echo "" ;else: foreach($unitlist as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['did'])?$row['did']:explode(',',$row['did']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Switchnum'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <select class="form-control selectpicker" id="c-switchnum" name="row[switchnum]">
       <option value='1' <?php if($row['switchnum'] == 1): ?>selected<?php endif; ?>>1</option>
       <option value='2' <?php if($row['switchnum'] == 2): ?>selected<?php endif; ?>>2</option>
       <option value='3'<?php if($row['switchnum'] == 3): ?>selected<?php endif; ?>>3</option>
       <option value='4'<?php if($row['switchnum'] == 4): ?>selected<?php endif; ?>>4</option>
       </select>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Onoff'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
                        
            <select  id="c-onoff" data-rule="required" class="form-control selectpicker" name="row[onoff]">
                <?php if(is_array($onoffList) || $onoffList instanceof \think\Collection || $onoffList instanceof \think\Paginator): if( count($onoffList)==0 ) : echo "" ;else: foreach($onoffList as $key=>$vo): ?>
                    <option value="<?php echo $key; ?>" <?php if(in_array(($key), is_array($row['onoff'])?$row['onoff']:explode(',',$row['onoff']))): ?>selected<?php endif; ?>><?php echo $vo; ?></option>
                <?php endforeach; endif; else: echo "" ;endif; ?>
            </select>

        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Keeptime'); ?>:</label>
        <div class="col-xs-12 col-sm-8" style="line-height: 30px;">
            <input id="c-keeptime" style="width: 36%;" data-rule="required" class="form-control"  name="row[keeptime]" type="number" value="<?php echo htmlentities($row['keeptime']); ?>">
        分钟
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Delaytime'); ?>:</label>
        <div class="col-xs-12 col-sm-8" style="line-height: 30px;">
            <input id="c-delaytime" style="width: 36%;" data-rule="required" class="form-control"  name="row[delaytime]" type="number" value="<?php echo htmlentities($row['delaytime']); ?>">
        分钟
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">运行方式:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label><input id="row[operationWay]-polling" name="row[operationWay]" type="radio" value="polling" <?php if($row['operationWay'] == 'polling'): ?>checked<?php endif; ?> />轮询</label> 
           <label><input id="row[operationWay]-task" name="row[operationWay]" type="radio" value="task" <?php if($row['operationWay'] == 'task'): ?>checked<?php endif; ?> />任务</label> 
            </div>
        </div>
    </div>
        <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">禁用:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label><input id="row[forbidden]-1" name="row[forbidden]" type="radio" value="polling" <?php if($row['forbidden'] == 1): ?>checked<?php endif; ?> />是</label> 
           <label><input id="row[forbidden]-0" name="row[forbidden]" type="radio" value="task" <?php if($row['forbidden'] == 0): ?>checked<?php endif; ?> />否</label> 
            </div>
        </div>
    </div>
    <div class="form-group layer-footer">
        <label class="control-label col-xs-12 col-sm-2"></label>
        <div class="col-xs-12 col-sm-8" >
            <button type="submit" class="btn btn-success btn-embossed disabled"><?php echo __('OK'); ?></button>
            <button type="reset" class="btn btn-default btn-embossed"><?php echo __('Reset'); ?></button>
        </div>
    </div>
</form>
<script src="/assets/js/jquery.3.4.1.min.js"></script>
<script src="/assets/js/layer/layer.js"></script>
<script>
    var index = parent.layer.getFrameIndex(window.name);

      
    
    $("#referenceidChange").on("click",function(){
        ids = $("#ids").val();
        layer.open({
            type: 2,
            title: '选择值',
            shadeClose: true,
            shade: 0.9,
            area: ['80%', '80%'],
            content: "/<?php echo $url; ?>/notice/addRange",
            btn: ['确定','关闭'],
            success: function(layero, index){
                var body=layer.getChildFrame('body',index);//少了这个是不能从父页面向子页面传值的
    　　　　　　　if($('#rrtype').val()!=''){
    			//获取子页面的元素，进行数据渲染
    			body.contents().find('#c-rtype').val($('#rrtype').val());
    			switch($('#rrtype').val()){
    			case '1':
					body.contents().find('#c-fixedvalue').val($('#rfixedvalue').val());
					body.contents().find('#c-unitlabel').val($('#runitlabel').val());
					body.contents().find('#type_2').hide();
					body.contents().find('#type_1').show();
					body.contents().find('#type_3').hide();
					body.contents().find('#type_4').hide();
					body.contents().find('#label').show();
					break;
				case '2':
					window["layui-layer-iframe" + index].getSensorList($('#rdid').val());
					body.contents().find('#c-did').val($('#rdid').val());
					body.contents().find('#c-did').change();
					body.contents().find('#c-sensor').val($('#rsensor').val());
					body.contents().find('#c-unitlabel').val($('#runitlabel').val());
					body.contents().find('#type_1').hide();
					body.contents().find('#type_2').show();
					body.contents().find('#type_3').hide();
					body.contents().find('#type_4').hide();
					body.contents().find('#label').show();
					break;
				case '3':
					body.contents().find('#c-groupid').val($('#rgroupid').val());
					body.contents().find('#c-calculation').val($('#rcalculation').val());
					body.contents().find('#c-unitlabel').val($('#runitlabel').val());
					body.contents().find('#type_2').hide();
					body.contents().find('#type_3').show();
					body.contents().find('#type_1').hide();
					body.contents().find('#type_4').hide();
					body.contents().find('#label').show();
					break;
				case '4':
					body.contents().find('#type_2').hide();
					body.contents().find('#type_3').hide();
					body.contents().find('#type_1').hide();
					body.contents().find('#label').hide();
					body.contents().find('#type_4').show();
					body.contents().find('#c-ldid').val($('#rdid').val());
					break;
    				case '0':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					break;
    			}
    　　　　　　　}
            },
            yes: function(index){
            	 var res = window["layui-layer-iframe" + index].callBack();
            	 if(res.rtype){
            		 if(res.rtype == 0){
            			     Layer.alert('参考值不能为空', {
                                title: __('Warning'),
                                btn: ['确认']
                            
                            });
            			 return;
            		 }
            		 if(res.rtype == 1){
        			     Layer.alert('参考值不能为值', {
                            title: __('Warning'),
                            btn: ['确认']
                        
                        });
        			 return;
        		 }
            		 if((res.rtype == 1 || res.rtype == 2 || res.rtype == 3) && res.unitlabel == ''){
            			     Layer.alert('标签不能为空', {
                                title: __('Warning'),
                                btn: ['确认']
                            
                            });
            			 return;
            		 }
            	 	$('#rrtype').val(res.rtype);
            		$('#rfixedvalue').val('');
            	 	$('#rcalculation').val('');
            	 	$('#rdid').val('');
            	 	$('#rgroupid').val('');
            	 	$('#rsensor').val('');
            		$('#runitlabel').val('');
            
            	 switch(res.rtype){
            	 	case '1':
            	 	$('#rfixedvalue').val(res.fixedvalue);
            	 	$('#runitlabel').val(res.unitlabel);
            	 	break;
            		case '2':
            	 	$('#rdid').val(res.did);
            		$('#rsensor').val(res.sensor);
            		$('#runitlabel').val(res.unitlabel);
            	 	break;
            		case '3':
            	 	$('#rgroupid').val(res.groupid);
            		$('#rcalculation').val(res.calculation);
            		$('#runitlabel').val(res.unitlabel);
            	 	break;
            		case '4':
                    	$('#rdid').val(res.ldid);
                        break;
            	 }
            	 $("#referenceidChange").text('重新选择');
            	 }
            	  layer.close(index);
            },
            cancel: function(){
                //右上角关闭回调
            }
        });
    });
	
    $("#minidChange").on("click",function(){
        ids = $("#ids").val();
        layer.open({
            type: 2,
            title: '选择值',
            shadeClose: true,
            shade: 0.9,
            area: ['80%', '80%'],
            content: "/<?php echo $url; ?>/notice/addRange",
            btn: ['确定','关闭'],
            success: function(layero, index){
                var body=layer.getChildFrame('body',index);//少了这个是不能从父页面向子页面传值的
    　　　　　　　if($('#mrtype').val()!=''){
    			//获取子页面的元素，进行数据渲染
    			body.contents().find('#c-rtype').val($('#mrtype').val());
    		
    			switch($('#mrtype').val()){
    				case '1':
    					body.contents().find('#c-fixedvalue').val($('#mfixedvalue').val());
    					body.contents().find('#c-unitlabel').val($('#munitlabel').val());
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_1').show();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#label').show();
    					break;
    				case '2':
    					window["layui-layer-iframe" + index].getSensorList($('#mdid').val());
    					body.contents().find('#c-did').val($('#mdid').val());
    					body.contents().find('#c-sensor').val($('#msensor').val());
    					body.contents().find('#c-unitlabel').val($('#munitlabel').val());
    					body.contents().find('#type_1').hide();
    					body.contents().find('#type_2').show();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#label').show();
    					break;
    				case '3':
    					body.contents().find('#c-groupid').val($('#mgroupid').val());
    					body.contents().find('#c-calculation').val($('#mcalculation').val());
    					body.contents().find('#c-unitlabel').val($('#munitlabel').val());
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').show();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').show();
    					break;
    				case '4':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					break;
    				case '0':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					break;
    			}
    　　　　　　　}
            },
            yes: function(index){
            	 var res = window["layui-layer-iframe" + index].callBack();
            	 if(res.rtype){
            		 if(res.rtype == 4){
        			     Layer.alert('最小值不能为离线', {
                            title: __('Warning'),
                            btn: ['确认']
                        
                        });
        			 return;
        		 }
            		 if((res.rtype == 1 || res.rtype == 2 || res.rtype == 3) && res.unitlabel == ''){
            			     Layer.alert('标签不能为空', {
                                title: __('Warning'),
                                btn: ['确认']
                            
                            });
            			 return;
            		 }
            	 	$('#mrtype').val(res.rtype);
            		$('#mfixedvalue').val('');
            	 	$('#mcalculation').val('');
            	 	$('#mdid').val('');
            	 	$('#mgroupid').val('');
            	 	$('#msensor').val('');
            		$('#munitlabel').val('');
            
            	 switch(res.rtype){
            	 	case '1':
            	 	$('#mfixedvalue').val(res.fixedvalue);
            	 	$('#munitlabel').val(res.unitlabel);
            	 	break;
            		case '2':
            	 	$('#mdid').val(res.did);
            		$('#msensor').val(res.sensor);
            		$('#munitlabel').val(res.unitlabel);
            	 	break;
            		case '3':
            	 	$('#mgroupid').val(res.groupid);
            		$('#mcalculation').val(res.calculation);
            		$('#munitlabel').val(res.unitlabel);
            	 	break;
            	 }
            	 $("#minidChange").text('重新选择');
            	 }
            	  layer.close(index);
            },
            cancel: function(){
                //右上角关闭回调
            }
        }); 
    });
    $("#maxidChange").on("click",function(){
        ids = $("#ids").val();
        layer.open({
            type: 2,
            title: '选择值',
            shadeClose: true,
            shade: 0.9,
            area: ['80%', '80%'],
            content: "/<?php echo $url; ?>/notice/addRange",
            btn: ['确定','关闭'],
            success: function(layero, index){
                var body=layer.getChildFrame('body',index);//少了这个是不能从父页面向子页面传值的
    　　　　　　　if($('#maxrtype').val()!=''){
    			//获取子页面的元素，进行数据渲染
    			body.contents().find('#c-rtype').val($('#maxrtype').val());
    		
    			switch($('#maxrtype').val()){
    				case '1':
    					body.contents().find('#c-fixedvalue').val($('#maxfixedvalue').val());
    					body.contents().find('#c-unitlabel').val($('#maxunitlabel').val());
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_1').show();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#label').show();
    					break;
    				case '2':
    					window["layui-layer-iframe" + index].getSensorList($('#maxdid').val());
    					body.contents().find('#c-did').val($('#maxdid').val());
    					body.contents().find('#c-sensor').val($('#maxsensor').val());
    					body.contents().find('#c-unitlabel').val($('#maxunitlabel').val());
    					body.contents().find('#type_1').hide();
    					body.contents().find('#type_2').show();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#label').show();
    					break;
    				case '3':
    					body.contents().find('#c-groupid').val($('#maxgroupid').val());
    					body.contents().find('#c-calculation').val($('#maxcalculation').val());
    					body.contents().find('#c-unitlabel').val($('#maxunitlabel').val());
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').show();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').show();
    					break;
    				case '4':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					break;
    				case '0':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					break;
    			}
    　　　　　　　}
            },
            yes: function(index){
            	 var res = window["layui-layer-iframe" + index].callBack();
            	 if(res.rtype){
            		 if(res.rtype == 4){
        			     Layer.alert('最大值不能为离线', {
                            title: __('Warning'),
                            btn: ['确认']
                        
                        });
        			 return;
        		 }
            		 if((res.rtype == 1 || res.rtype == 2 || res.rtype == 3) && res.unitlabel == ''){
            			     Layer.alert('标签不能为空', {
                                title: __('Warning'),
                                btn: ['确认']
                            
                            });
            			 return;
            		 }
            	 	$('#maxrtype').val(res.rtype);
            		$('#maxfixedvalue').val('');
            	 	$('#maxcalculation').val('');
            	 	$('#maxdid').val('');
            	 	$('#maxgroupid').val('');
            	 	$('#maxsensor').val('');
            		$('#maxunitlabel').val('');
            
            	 switch(res.rtype){
            	 	case '1':
            	 	$('#maxfixedvalue').val(res.fixedvalue);
            	 	$('#maxunitlabel').val(res.unitlabel);
            	 	break;
            		case '2':
            	 	$('#maxdid').val(res.did);
            		$('#maxsensor').val(res.sensor);
            		$('#maxunitlabel').val(res.unitlabel);
            	 	break;
            		case '3':
            	 	$('#maxgroupid').val(res.groupid);
            		$('#maxcalculation').val(res.calculation);
            		$('#maxunitlabel').val(res.unitlabel);
            	 	break;
            	 }
            	 $("#maxidChange").text('重新选择');
            	 }
            	  layer.close(index);
            },
            cancel: function(){
                //右上角关闭回调
            }
        });
    });
    
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
