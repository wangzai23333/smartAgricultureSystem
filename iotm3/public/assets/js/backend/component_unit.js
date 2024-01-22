define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'component_unit/index' + location.search,
                    add_url: 'component_unit/add',
                    edit_url: 'component_unit/edit',
                    del_url: 'component_unit/del',
                    multi_url: 'component_unit/multi',
                    import_url: 'component_unit/import',
                    table: 'component_unit',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'title', title: __('Title'), operate: 'LIKE'},
                        {field: 'did', title: __('Did'), operate: 'LIKE'},
                        {field: 'lat', title: __('Lat'), operate:'BETWEEN'},
                        {field: 'lng', title: __('Lng'), operate:'BETWEEN'},
                        {field: 'remark', title: __('Remark')},
                        {field: 'createtime_text', title: __('Createtime'),operate: false},
                        {field: 'cstatus', title: '状态', searchList: {"success":'正常', "deleted": '离线'}, formatter:Table.api.formatter.status},
                        {field: 'operate', title: __('Operate'),  
                        	buttons: [
                      	 {
                            name: 'addtabs3',
                            title: '开关管理',
                            classname: 'btn btn-xs btn-primary btn-dialog',
                            icon: 'fa fa-sign-language',
                            url: 'component_unit/switch'
                        }
                    ], table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });
        	setInterval(function(){ 
                $('.btn-refresh').click();
            	},480000);
            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        switch: function () {
        	var id = $('#sid').val();
       
        	setInterval(function(){ 
            $.ajax({
                url: "component_unit/switchList",
                type: "post",
                data:{id:id},
                dataType: "json",
                async: false,
                success: function (result) {
                    if(result)
                    {
                    	var j = 1;
                       for(var i in result){
                    	   if(result[i] ==0){
                    		   $('#status'+j+'_0').attr("checked",true);
                    		  
                    	   }else{
                    		   $('#status'+j+'_1').attr("checked",true);
                    		   
                    	   }
                    	   j++;
                       }
                    }
                    
                },
                error:function(data){
                    console.log(result);
                }
            });
        	},18000);
        	  Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});