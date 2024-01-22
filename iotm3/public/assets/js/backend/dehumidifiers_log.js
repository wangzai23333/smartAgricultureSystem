define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dehumidifiers_log/index' + location.search,
                    add_url: 'dehumidifiers_log/add',
                    edit_url: 'dehumidifiers_log/edit',
                    del_url: 'dehumidifiers_log/del',
                    multi_url: 'dehumidifiers_log/multi',
                    import_url: 'dehumidifiers_log/import',
                    table: 'dehumidifiers_log',
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
                        {field: 'did', title: __('Did')},
                        {field: 'onoff', title: __('Onoff'), searchList: {"on":__('Onoff on'),"off":__('Onoff off')}, formatter: Table.api.formatter.normal},
                        {field: 'env_temp', title: __('Env_temp'), operate:'BETWEEN'},
                        {field: 'env_humi', title: __('Env_humi'), operate:'BETWEEN'},
                        {field: 'settemp', title: __('Settemp'), operate:'BETWEEN'},
                        {field: 'sethumi', title: __('Sethumi'), operate:'BETWEEN'},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
                        {field: 'dehumidifiers.id', title: __('Dehumidifiers.id')},
                        {field: 'dehumidifiers.title', title: __('Dehumidifiers.title'), operate: 'LIKE'},
                        {field: 'dehumidifiers.dev_uid', title: __('Dehumidifiers.dev_uid'), operate: 'LIKE'},
                        {field: 'dehumidifiers.topic', title: __('Dehumidifiers.topic'), operate: 'LIKE'},
                        {field: 'dehumidifiers.hid', title: __('Dehumidifiers.hid')},
                        {field: 'dehumidifiers.createtime', title: __('Dehumidifiers.createtime')},
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