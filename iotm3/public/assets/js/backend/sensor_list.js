define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'sensor_list/index' + location.search,
                    add_url: 'sensor_list/add',
                    edit_url: 'sensor_list/edit',
                    del_url: 'sensor_list/del',
                    multi_url: 'sensor_list/multi',
                    import_url: 'sensor_list/import',
                    table: 'sensor_list',
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
                        {field: 'label', title: '属性', operate: 'LIKE',operate: false},
                        {field: 'did', title: __('Did'), operate: 'LIKE',operate: false},
                        {field: 'createtime_text', title: __('Createtime'),operate: false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate ,buttons: [
        {
            name: 'detail',
            text: '',
            title: '数据校准',
            classname: 'btn btn-xs btn-primary btn-dialog',
            icon: 'fa fa-cogs',
            url: 'sensor_list/adjust?type={id}',
            visible: function (row) {
                //返回true时按钮显示,返回false隐藏
                return true;
            }
        }]}
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
          adjust: function () {
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