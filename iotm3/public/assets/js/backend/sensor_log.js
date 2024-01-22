define(['jquery', 'bootstrap', 'backend', 'table', 'form', 'template', 'echarts', 'echarts-theme'], function ($, undefined, Backend, Table, Form, Template, Echarts) {

    var Controller = {
        index: function () {
        	$(document).on("click", "#tosearch", function () {
        		var did =  $('#did').val();
        		window.location.replace("../sensor_log/to_index");
            });
        	 //这句话在多选项卡统计表时必须存在，否则会导致影响的图表宽度不正确
            $(document).on("click", ".charts-custom a[data-toggle=\"tab\"]", function () {
                 var that = this;
                 set_Data();
                 setTimeout(function () {
                     var id = $(that).attr("href");
                     console.log(id);
                     var chart = Echarts.getInstanceByDom($(id)[0]);
                     chart.resize();
                 }, 0);
             });
             var sensorText = $('#sensorText').val();
             var sensors = sensorText.split(',');
             for(var i  in sensors){
            	 var areaChart = Echarts.init(document.getElementById('chart_today_'+sensors[i]), 'walden');
                var data = $('#data_'+sensors[i]).val();
                console.log(sensors[i]);
                var datas = data.split(',');
            	 // 指定图表的配置项和数据
                 var option = {
                     xAxis: {
                         type: 'category',
                         boundaryGap: false,
                         data: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24]
                     },
                     yAxis: {
                         type: 'value'
                     },
                     tooltip: {
                         trigger: 'axis'                    
                     },
                     series: [{
                         data: datas,
                         type: 'line',
                         areaStyle: {
                         	color:"#21709d",
                         	  opacity: 0.6 ,
                         	  smooth: true
                         }
                     }]
                 };

                 // 使用刚指定的配置项和数据显示图表。
                 areaChart.setOption(option);
             }
             
             
             
            function set_Data(){
            	var did = $('#did').val();
            	 $.ajax({
                     url: "ajax/getSensorDetail",
                     type: "post",
                     data:{did:did},
                     dataType: "json",
                     async: false,
                     success: function (result) {
                         if(result)
                         {
                         	if(result.todayData){
                         		 
                         		 var dateList = [];
                         		 var n = 0;
                         		for(var d=7;d>-1;d--){
                         			 var today = new Date();
                         			 var before = today.getTime() + 1000*60*60*24*(-d); 
                       		         today.setTime(before);     
                       		         dateList[n] = today.getDate();  
                       		         n++;
                         		}
                         		for(var k in result.todayData){
                         		// 基于准备好的dom，初始化echarts实例
                                    var areaChart = Echarts.init(document.getElementById('chart_today_'+k), 'walden');
                                    var data = [];	
                                    data[0] = 0;
                                    for(var i=0;i<25;i++){
                                    	
                                    	if(result.todayData[k][i]){
                                    		data[i] = result.todayData[k][i];
                                    	}else{
                                    		data[i] = 0;
                                    	}
                                    	
                                    	}
                                    console.log(data);
                                    // 指定图表的配置项和数据
                                    var option = {
                                        xAxis: {
                                            type: 'category',
                                            boundaryGap: false,
                                            data: [0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24]
                                        },
                                        yAxis: {
                                            type: 'value'
                                        },
                                        tooltip: {
                                            trigger: 'axis'                    
                                        },
                                        series: [{
                                            data: data,
                                            type: 'line',
                                            smooth: true,
                                            areaStyle: {
                                            	color:"#21709d",
                                            	  opacity: 0.6
                                            }
                                        }]
                                    };

                                    // 使用刚指定的配置项和数据显示图表。
                                    areaChart.setOption(option);
                                    
                                    var sevenData = [];
                                    for(var day in dateList){
                                    	sevenData[day] = 0;
                                    	if(result.sevenData[k][dateList[day]]){
                                    		sevenData[day] = result.sevenData[k][dateList[day]];
                                    	}
                                    	
                                    }
                                  
                                    // 基于准备好的dom，初始化echarts实例
                                    var lineChart = Echarts.init(document.getElementById('chart_day_'+k), 'walden');

                                    // 指定图表的配置项和数据
                                     option = {
                                        xAxis: {
                                            type: 'category',
                                            data: dateList
                                        },
                                        yAxis: {
                                            type: 'value'
                                        },
                                        tooltip: {
                                            trigger: 'axis'                    
                                        },
                                        series: [{
                                            data: sevenData,
                                            type: 'line',
                                            smooth: true
                                        }]
                                    };

                                    // 使用刚指定的配置项和数据显示图表。
                                    lineChart.setOption(option);
	                               
                                    
                         		}
                         		
                         	}
                         	
                         	
                         }
                         
                     },
                     error:function(data){
                         console.log(result);
                     }
                 });
            }
            
        },
        to_index: function () {
        	$(document).on("click", "#search", function () {
        		var did =  $('#did').val();
        		window.location.replace("../sensor_log/index?did="+did);
            });
        }
    };
    return Controller;
});
