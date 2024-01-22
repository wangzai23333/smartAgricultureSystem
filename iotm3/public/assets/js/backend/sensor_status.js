define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sensor_status/index' + location.search,
                    add_url: 'sensor_status/add',
                    edit_url: 'sensor_status/edit',
                    del_url: 'sensor_status/del',
                    multi_url: 'sensor_status/multi',
                    import_url: 'sensor_status/import',
                    table: 'sensor_status',
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
                        {field: 'did', title: __('Did'), operate: 'LIKE'},
                        {field: 'onoff1', title: __('Onoff1'), searchList: {"1":__('Onoff1 1'),"0":__('Onoff1 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff2', title: __('Onoff2'), searchList: {"1":__('Onoff2 1'),"0":__('Onoff2 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff3', title: __('Onoff3'), searchList: {"1":__('Onoff3 1'),"0":__('Onoff3 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff4', title: __('Onoff4'), searchList: {"1":__('Onoff4 1'),"0":__('Onoff4 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff5', title: __('Onoff5'), searchList: {"1":__('Onoff5 1'),"0":__('Onoff5 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff6', title: __('Onoff6'), searchList: {"1":__('Onoff6 1'),"0":__('Onoff6 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff7', title: __('Onoff7'), searchList: {"1":__('Onoff7 1'),"0":__('Onoff7 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff8', title: __('Onoff8'), searchList: {"1":__('Onoff8 1'),"0":__('Onoff8 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff9', title: __('Onoff9'), searchList: {"1":__('Onoff9 1'),"0":__('Onoff9 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff10', title: __('Onoff10'), searchList: {"1":__('Onoff10 1'),"0":__('Onoff10 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff11', title: __('Onoff11'), searchList: {"1":__('Onoff11 1'),"0":__('Onoff11 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff12', title: __('Onoff12'), searchList: {"1":__('Onoff12 1'),"0":__('Onoff12 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff13', title: __('Onoff13'), searchList: {"1":__('Onoff13 1'),"0":__('Onoff13 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff14', title: __('Onoff14'), searchList: {"1":__('Onoff14 1'),"0":__('Onoff14 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff15', title: __('Onoff15'), searchList: {"1":__('Onoff15 1'),"0":__('Onoff15 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff16', title: __('Onoff16'), searchList: {"1":__('Onoff16 1'),"0":__('Onoff16 0')}, formatter: Table.api.formatter.normal},
                        {field: 'updatetime', title: __('Updatetime')},
                        {field: 'componentunit.title', title: __('Componentunit.title'), operate: 'LIKE'},
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