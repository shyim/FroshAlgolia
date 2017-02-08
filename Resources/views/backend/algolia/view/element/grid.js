Ext.define('Shopware.apps.Algolia.view.element.Grid', {
    extend: 'Ext.grid.Panel',
    alias: 'widget.algolia-element-grid',
    viewConfig: {
        plugins: {
            pluginId: 'my-gridviewdragdrop',
            ptype: 'gridviewdragdrop'
        }
    },

    addText: 'Add new field',

    initComponent: function () {
        var me = this;

        me.dockedItems = [me.getToolbar()];
        me.store = me.createBasicStore();
        me.plugins = [
            Ext.create('Ext.grid.plugin.RowEditing')
        ];

        me.columns.push(Ext.create('Ext.grid.column.Action', {
            width:90,
            items:[
                {
                    iconCls:'sprite-minus-circle-frame',
                    handler:function (view, rowIndex, colIndex, item) {
                        var store = view.getStore(),
                            record = store.getAt(rowIndex);

                        me.store.remove(record);
                    }
                }
            ]
        }));

        me.callParent(arguments);
    },

    getToolbar: function () {
        var me = this;

        me.toolbar = Ext.create('Ext.toolbar.Toolbar', {
            dock: 'top',
            ui: 'shopware-ui',
            items: [
                {
                    xtype: 'button',
                    iconCls: 'sprite-plus-circle-frame',
                    text: me.addText,
                    handler: function () {
                        me.store.add({});
                    }
                }
            ]
        });

        return me.toolbar;
    },

    createBasicStore: function () {
        var me = this,
            models = [];

        me.columns.forEach(function (item) {
            models.push({
                name: item.dataIndex,
                type: 'string'
            });
        });

        return Ext.create('Ext.data.Store', {
            fields: models
        });
    },

    setValue: function (value) {
        var me = this;

        if (typeof value.forEach != 'undefined') {
            value.forEach(function (item) {
                me.store.add(item);
            });
        }
    },

    getValue: function () {
        var me = this,
            values = [];

        me.store.each(function (record) {
            values.push(record.data);
        });

        return values;
    }
});
