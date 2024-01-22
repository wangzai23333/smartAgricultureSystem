define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'air_log/index' + location.search,
                    add_url: 'air_log/add',
                    edit_url: 'air_log/edit',
                    del_url: 'air_log/del',
                    multi_url: 'air_log/multi',
                    import_url: 'air_log/import',
                    table: 'air_log',
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
                        {field: 'hid', title: __('Hid')},
                        {field: 'aid', title: __('Aid')},
                        {field: 'onoff', title: __('Onoff'), searchList: {"on":__('Onoff on'),"off":__('Onoff off')}, formatter: Table.api.formatter.normal},
                        {field: 'env_temp', title: __('Env_temp'), operate:'BETWEEN'},
                        {field: 'env_humi', title: __('Env_humi'), operate:'BETWEEN'},
                        {field: 'settemp', title: __('Settemp'), operate:'BETWEEN'},
                        {field: 'sethumi', title: __('Sethumi'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'air.id', title: __('Air.id')},
                        {field: 'air.title', title: __('Air.title'), operate: 'LIKE'},
                        {field: 'air.dev_uid', title: __('Air.dev_uid'), operate: 'LIKE'},
                        {field: 'air.topic', title: __('Air.topic'), operate: 'LIKE'},
                        {field: 'air.hid', title: __('Air.hid')},
                        {field: 'air.createtime', title: __('Air.createtime')},
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