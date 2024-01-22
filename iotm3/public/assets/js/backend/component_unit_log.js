define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'component_unit_log/index' + location.search,
                    add_url: 'component_unit_log/add',
                    edit_url: 'component_unit_log/edit',
                    del_url: 'component_unit_log/del',
                    multi_url: 'component_unit_log/multi',
                    import_url: 'component_unit_log/import',
                    table: 'component_unit_log',
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
                        {field: 'state1_set', title: __('State1_set')},
                        {field: 'state2_set', title: __('State2_set')},
                        {field: 'state3_set', title: __('State3_set')},
                        {field: 'state4_set', title: __('State4_set')},
                        {field: 'state5_set', title: __('State5_set')},
                        {field: 'timeon1', title: __('Timeon1')},
                        {field: 'timeoff1', title: __('Timeoff1')},
                        {field: 'timeon2', title: __('Timeon2')},
                        {field: 'timeoff2', title: __('Timeoff2')},
                        {field: 'timeon3', title: __('Timeon3')},
                        {field: 'timeoff3', title: __('Timeoff3')},
                        {field: 'timeon4', title: __('Timeon4')},
                        {field: 'timeoff4', title: __('Timeoff4')},
                        {field: 'timeon5', title: __('Timeon5')},
                        {field: 'timeoff5', title: __('Timeoff5')},
                        {field: 'count1', title: __('Count1')},
                        {field: 'count2', title: __('Count2')},
                        {field: 'count3', title: __('Count3')},
                        {field: 'onoff1', title: __('Onoff1'), searchList: {"1":__('Onoff1 1'),"0":__('Onoff1 0')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff2', title: __('Onoff2'), searchList: {"0":__('Onoff2 0'),"1":__('Onoff2 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff3', title: __('Onoff3'), searchList: {"0":__('Onoff3 0'),"1":__('Onoff3 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff4', title: __('Onoff4'), searchList: {"0":__('Onoff4 0'),"1":__('Onoff4 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff5', title: __('Onoff5'), searchList: {"0":__('Onoff5 0'),"1":__('Onoff5 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff6', title: __('Onoff6'), searchList: {"0":__('Onoff6 0'),"1":__('Onoff6 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff7', title: __('Onoff7'), searchList: {"0":__('Onoff7 0'),"1":__('Onoff7 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff8', title: __('Onoff8'), searchList: {"0":__('Onoff8 0'),"1":__('Onoff8 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff9', title: __('Onoff9'), searchList: {"0":__('Onoff9 0'),"1":__('Onoff9 1')}, formatter: Table.api.formatter.normal},
                        {field: 'onoff10', title: __('Onoff10'), searchList: {"0":__('Onoff10 0'),"1":__('Onoff10 1')}, formatter: Table.api.formatter.normal},
                        {field: 'createtime', title: __('Createtime')},
                        {field: 'update_at', title: __('Update_at')},
                        {field: 'componentunit.id', title: __('Componentunit.id')},
                        {field: 'componentunit.title', title: __('Componentunit.title'), operate: 'LIKE'},
                        {field: 'componentunit.did', title: __('Componentunit.did'), operate: 'LIKE'},
                        {field: 'componentunit.lat', title: __('Componentunit.lat'), operate:'BETWEEN'},
                        {field: 'componentunit.lng', title: __('Componentunit.lng'), operate:'BETWEEN'},
                        {field: 'componentunit.remark', title: __('Componentunit.remark')},
                        {field: 'componentunit.createtime', title: __('Componentunit.createtime')},
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