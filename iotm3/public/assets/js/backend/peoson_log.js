define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'peoson_log/index' + location.search,
                    add_url: 'peoson_log/add',
                    edit_url: 'peoson_log/edit',
                    del_url: 'peoson_log/del',
                    multi_url: 'peoson_log/multi',
                    import_url: 'peoson_log/import',
                    table: 'peoson_log',
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
                        {field: 'sensorid', title: __('Sensorid')},
                        {field: 'starttime', title: __('Starttime')},
                        {field: 'endtime', title: __('Endtime')},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'isexpire', title: __('Isexpire'), searchList: {"0":__('Isexpire 0'),"1":__('Isexpire 1')}, formatter: Table.api.formatter.normal},
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