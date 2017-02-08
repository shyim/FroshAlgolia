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
        Ext.Ajax.request({
            url: '{url action=saveConfig}',
            params: {
                shopId: me.record.get('id'),
                data: Ext.JSON.encode(me.getForm().getValues())
            },
            success: function(){
                Shopware.Notification.createGrowlMessage("Algolia", "Configuration has been saved")
            }
        });
    }
});
