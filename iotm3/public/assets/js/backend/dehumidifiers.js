define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'dehumidifiers/index' + location.search,
                    add_url: 'dehumidifiers/add',
                    edit_url: 'dehumidifiers/edit',
                    del_url: 'dehumidifiers/del',
                    multi_url: 'dehumidifiers/multi',
                    import_url: 'dehumidifiers/import',
                    table: 'dehumidifiers',
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
                        {field: 'dev_uid', title: __('Dev_uid'), operate: 'LIKE'},
                        {field: 'topic', title: __('Topic'), operate: 'LIKE'},
                        {field: 'hid', title: __('Hid')},
                        {field: 'createtime', title: __('Createtime')},
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