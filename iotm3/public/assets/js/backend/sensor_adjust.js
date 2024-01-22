define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sensor_adjust/index' + location.search,
                    add_url: 'sensor_adjust/add',
                    edit_url: 'sensor_adjust/edit',
                    del_url: 'sensor_adjust/del',
                    multi_url: 'sensor_adjust/multi',
                    import_url: 'sensor_adjust/import',
                    table: 'sensor_adjust',
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
                        {field: 'sensorid', title: __('Sensorid')},
                        {field: 'unitid', title: __('Unitid')},
                        {field: 'label', title: __('Label'), operate: 'LIKE'},
                        {field: 'change_type', title: __('Change_type'), searchList: {"value":__('Value'),"ratio":__('Ratio')}, formatter: Table.api.formatter.normal},
                        {field: 'change_value', title: __('Change_value'), operate:'BETWEEN'},
                        {field: 'updatetime', title: __('Updatetime')},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'sensorlist.id', title: __('Sensorlist.id')},
                        {field: 'sensorlist.title', title: __('Sensorlist.title'), operate: 'LIKE'},
                        {field: 'sensorlist.did', title: __('Sensorlist.did'), operate: 'LIKE'},
                        {field: 'sensorlist.remark', title: __('Sensorlist.remark'), operate: 'LIKE'},
                        {field: 'sensorlist.createtime', title: __('Sensorlist.createtime')},
                        {field: 'sensorlist.isAdjust', title: __('Sensorlist.isadjust')},
                        {field: 'unit.id', title: __('Unit.id')},
                        {field: 'unit.label', title: __('Unit.label'), operate: 'LIKE'},
                        {field: 'unit.title', title: __('Unit.title'), operate: 'LIKE'},
                        {field: 'unit.text', title: __('Unit.text'), operate: 'LIKE'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
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