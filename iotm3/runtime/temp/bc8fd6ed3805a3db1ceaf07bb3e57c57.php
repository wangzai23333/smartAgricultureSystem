<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:73:"C:\eclipse-workspace\iot\public/../application/admin\view\notice\add.html";i:1621323595;s:67:"C:\eclipse-workspace\iot\application\admin\view\layout\default.html";i:1605513288;s:64:"C:\eclipse-workspace\iot\application\admin\view\common\meta.html";i:1605513288;s:66:"C:\eclipse-workspace\iot\application\admin\view\common\script.html";i:1605513288;}*/ ?>
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
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Weigh'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-weigh" data-rule="required" class="form-control" name="row[weigh]" type="number" value="0">
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">标题:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-title" data-rule="required" class="form-control" name="row[title]" type="text">
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Referenceid'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
         <input type="hidden"  id="rcalculation"  name="row[rcalculation]" type="text" value="">
         <input type="hidden"  id="rdid"  name="row[rdid]" type="text" value="">
         <input type="hidden"  id="rfixedvalue"  name="row[rfixedvalue]" type="text" value="">
         <input type="hidden"  id="rgroupid"  name="row[rgroupid]" type="text" value="">
         <input type="hidden"  id="rrtype"  name="row[rrtype]" type="text" value="">
         <input type="hidden"  id="rsensor"  name="row[rsensor]" type="text" value="">
         <input type="hidden"  id="runitlabel"  name="row[runitlabel]" type="text" value="">
         <button  type="button" id="referenceidChange" class="btn btn-success btn-embossed">选择</button>
        </div>
        
       
    </div>
  
    
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">区间:</label>
        <div class="col-xs-12 col-sm-8">
           [
         <input type="hidden"  id="mcalculation"  name="row[mcalculation]" type="text" value="">
         <input type="hidden"  id="mdid"  name="row[mdid]" type="text" value="">
         <input type="hidden"  id="mfixedvalue"  name="row[mfixedvalue]" type="text" value="">
         <input type="hidden"  id="mgroupid"  name="row[mgroupid]" type="text" value="">
         <input type="hidden"  id="mrtype"  name="row[mrtype]" type="text" value="">
         <input type="hidden"  id="msensor"  name="row[msensor]" type="text" value="">
         <input type="hidden"  id="munitlabel"  name="row[munitlabel]" type="text" value="">
         <button  type="button" id="minidChange" class="btn btn-success btn-embossed">选择</button>
       ,
         <input type="hidden"  id="maxcalculation"  name="row[maxcalculation]" type="text" value="">
         <input type="hidden"  id="maxdid"  name="row[maxdid]" type="text" value="">
         <input type="hidden"  id="maxfixedvalue"  name="row[maxfixedvalue]" type="text" value="">
         <input type="hidden"  id="maxgroupid"  name="row[maxgroupid]" type="text" value="">
         <input type="hidden"  id="maxrtype"  name="row[maxrtype]" type="text" value="">
         <input type="hidden"  id="maxsensor"  name="row[maxsensor]" type="text" value="">
         <input type="hidden"  id="maxunitlabel"  name="row[maxunitlabel]" type="text" value="">
         <button  type="button" id="maxidChange" class="btn btn-success btn-embossed">选择</button>
         )
       
        </div>
    </div>

    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Phone'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-phone" placeholder="手机号之间请以英文的 ,  隔开" class="form-control " rows="5" name="row[phone]" cols="50"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Content'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-content" " class="form-control editor" rows="5" name="row[content]" cols="50"></textarea>
          <div class="tips" style="color: #999;">参考值标签：{reference}、最大值标签：{max}、最小值标签：{min}</div>
        </div>
      
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('Noticesign'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
            <textarea id="c-noticesign" class="form-control " rows="5" name="row[noticesign]" cols="50"></textarea>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">延迟时间:</label>
        <div class="col-xs-12 col-sm-8" style="line-height: 30px;">
            <input style="width: 36%;" id="c-keeptime" class="form-control" name="row[keeptime]" type="text" value="">
       分钟
        </div>
    </div>
  
      <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2"><?php echo __('People'); ?>:</label>
        <div class="col-xs-12 col-sm-8">
           <?php echo Form::selectpickers('row[people][]', $memberList, $ids); ?>
        </div>
    </div>
     <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">app端授权:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label><input id="row[isApp]-open" name="row[isApp]" type="radio" value="1" checked />开启</label> 
           <label><input id="row[isApp]-close" name="row[isApp]" type="radio" value="0"  />关闭</label> 
            </div>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">客户端授权:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label><input id="row[isClient]-open" name="row[isClient]" type="radio" value="1" checked />开启</label> 
           <label><input id="row[isClient]-close" name="row[isClient]" type="radio" value="0"  />关闭</label> 
            </div>
        </div>
    </div>
       <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">运行方式:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label><input id="row[operationWay]-polling" name="row[operationWay]" type="radio" value="polling" checked />轮询</label> 
           <label><input id="row[operationWay]-task" name="row[operationWay]" type="radio" value="task"  />任务</label> 
            </div>
        </div>
    </div>
        <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">呼叫号码通知:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio" id="isCall">
            <label><input id="row[isCall]-open" name="row[isCall]" type="radio" value="1" checked />开启</label> 
           <label><input id="row[isCall]-close" name="row[isCall]" type="radio" value="0"  />关闭</label> 
            </div>
        </div>
    </div>
     <div class="form-group" id="callNumber">
        <label class="control-label col-xs-12 col-sm-2">呼叫号码:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-callNumber"  class="form-control" name="row[callNumber]" type="number">
        </div>
    </div>
    <div class="form-group" id="voiceCode">
        <label class="control-label col-xs-12 col-sm-2">呼叫语音模板ID:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-voiceCode"  class="form-control" name="row[voiceCode]" type="text">
        </div>
    </div>
     <div class="form-group" id="alarmName">
        <label class="control-label col-xs-12 col-sm-2">呼叫警告内容:</label>
        <div class="col-xs-12 col-sm-8">
            <input id="c-alarmName"  class="form-control" name="row[alarmName]" type="text">
        </div>
    </div>
      <div class="form-group">
        <label class="control-label col-xs-12 col-sm-2">禁用:</label>
        <div class="col-xs-12 col-sm-8">
            
            <div class="radio">
            <label><input id="row[forbidden]-1" name="row[forbidden]" type="radio" value="1"  />是</label> 
           <label><input id="row[forbidden]-0" name="row[forbidden]" type="radio" value="0" checked />否</label> 
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

<script src="/assets/js/jquery.3.4.1.min.js"></script>
<script src="/assets/js/layer/layer.js"></script>
<script>
$('input:radio[name="row[isCall]"]').click(function(){
	var iscall = $(this).val(); 
	if(iscall==1){
		$('#callNumber').show();
		$('#alarmName').show();
		$('#voiceCode').show();
	}else{
		$('#callNumber').hide();
		$('#alarmName').hide();
		$('#voiceCode').hide();
	}
});
    var index = parent.layer.getFrameIndex(window.name);

      
    
    $("#referenceidChange").on("click",function(){
        ids = $("#ids").val();
        layer.open({
            type: 2,
            title: '选择值',
            shadeClose: true,
            shade: 0.9,
            area: ['80%', '80%'],
            content: "addRange",
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
    					body.contents().find('#c-ldid').val($('#rdid').val());
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					body.contents().find('#type_4').show();
    				
    					break;
    				case '0':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#type_4').hide();
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
            content: "addRange",
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
    					body.contents().find('#type_4').hide();
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
    					body.contents().find('#type_4').hide();
    					break;
    				case '3':
    					body.contents().find('#c-groupid').val($('#mgroupid').val());
    					body.contents().find('#c-calculation').val($('#mcalculation').val());
    					body.contents().find('#c-unitlabel').val($('#munitlabel').val());
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').show();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').show();
    					body.contents().find('#type_4').hide();
    					break;
    				case '4':
    					body.contents().find('#type_2').hide();
    					body.contents().find('#type_3').hide();
    					body.contents().find('#type_1').hide();
    					body.contents().find('#label').hide();
    					body.contents().find('#type_4').show();
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
            content: "addRange",
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
