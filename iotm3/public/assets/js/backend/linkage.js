define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'linkage/index' + location.search,
                    add_url: 'linkage/add',
                    edit_url: 'linkage/edit',
                    del_url: 'linkage/del',
                    multi_url: 'linkage/multi',
                    import_url: 'linkage/import',
                    table: 'linkage',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
                        {field: 'title', title:'标题'},
                        {field: 'reference_text', title: __('Referenceid'),operate: false},
                        {field: 'min_text', title: __('Minid'),operate: false},
                        {field: 'max_text', title: __('Maxid'),operate: false},
                        {field: 'did', title: __('Did'), operate: 'LIKE'},
                        {field: 'switchnum', title: __('Switchnum')},
                        {field: 'onoff', title: __('Onoff'), searchList: {"on":__('Onoff on'),"off":__('Onoff off')}, formatter: Table.api.formatter.normal},
                        {field: 'forbidden', title: '禁用', searchList: {"1":'禁用', "0": '否'}, formatter: Table.api.formatter.toggle},
                        {field: 'createtime_text', title: __('Createtime'),operate: false},
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