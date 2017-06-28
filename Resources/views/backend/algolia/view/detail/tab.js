Ext.define('Shopware.apps.Algolia.view.detail.Tab', {
    extend: 'Ext.form.Panel',
    alias: 'widget.algolia-form',
    layout: 'fit',

    initComponent: function () {
        var me = this;

        me.dockedItems = [{
            dock: 'bottom',
            xtype: 'toolbar',
            ui: 'shopware-ui',
            cls: 'shopware-toolbar',
            items: me.createFormButtons()
        }];

        me.items = [
            {
                xtype: 'tabpanel',
                items: [
                    Ext.create('Shopware.apps.Algolia.view.detail.tab.Index')
                ]
            }
        ];

        me.loadData();

        me.callParent(arguments);
    },

    loadData: function () {
        var me = this;

        Ext.Ajax.request({
            url: '{url action=getConfig}',
            params: {
                shopId: me.record.get('id')
            },
            success: function(response){
                var text = response.responseText,
                    object = Ext.JSON.decode(text);

                Object.keys(object.data).forEach(function (key) {
                    me.down('[name="' + key + '"]').setValue(object.data[key]);
                });
            }
        });
    },

    createFormButtons: function(){
        var me = this;

        return ['->',
            {
                text:'Save',
                cls:'primary',
                handler: function () {
                    me.fireEvent('saveForm', me);
                }
            }
        ];
    }
});
