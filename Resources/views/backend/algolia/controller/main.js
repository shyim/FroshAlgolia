Ext.define('Shopware.apps.Algolia.controller.Main', {
    extend: 'Enlight.app.Controller',

    init: function() {
        var me = this;

        me.control({
            'algolia-form': {
                saveForm: me.saveForm
            }
        });

        me.mainWindow = me.getView('detail.Window').create({ });
    },

    saveForm: function (me) {
        var values = me.getForm().getValues();

        me.query('[xtype="algolia-element-grid"]').forEach(function (grid) {
            values[grid.name] = grid.getValue();
        });

        Ext.Ajax.request({
            url: '{url action=saveConfig}',
            params: {
                shopId: me.record.get('id'),
                data: Ext.JSON.encode(values)
            },
            success: function(){
                Shopware.Notification.createGrowlMessage("Algolia", "Configuration has been saved")
            }
        });
    }
});
