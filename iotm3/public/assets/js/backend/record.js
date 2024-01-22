define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'record/index' + location.search,
                    add_url: 'record/add',
                    edit_url: 'record/edit',
                    del_url: 'record/del',
                    multi_url: 'record/multi',
                    import_url: 'record/import',
                    table: 'record',
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
                        {field: 'ntype', title: __('Ntype'), searchList: {"1":__('Ntype 1'),"2":__('Ntype 2')}, formatter: Table.api.formatter.normal},
                        {field: 'tid', title: __('Tid')},
                        {field: 'nid', title: __('Nid')},
                        {field: 'sendtime', title: __('Sendtime')},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'task.did', title: __('Task.did'), operate: 'LIKE'},
                        {field: 'recordlog.isread', title: __('Recordlog.isread')},
                        {field: 'recordlog.issend', title: __('Recordlog.issend')},
                        {field: 'notice.id', title: __('Notice.id')},
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