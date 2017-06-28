Ext.define('Shopware.apps.Algolia.view.detail.Window', {
    extend: 'Enlight.app.Window',
    height: 600,
    width: 1000,
    title : 'Algolia',
    layout: 'fit',
    autoShow: false,

    initComponent: function () {
        var me = this;

        me.items = me.getItems();

        me.callParent(arguments);
    },

    getItems: function () {
        var me = this;

        me.tabPanel = Ext.create('Ext.tab.Panel');

        me.shopStore = Ext.create('Shopware.apps.Base.store.ShopLanguage');
        me.shopStore.on('load', function () {
            var first = true;
            me.shopStore.each(function (record) {
                var tab = Ext.create('Shopware.apps.Algolia.view.detail.Tab', {
                    title: record.get('name'),
                    record: record
                });

                me.tabPanel.add(tab);

                if (first) {
                    me.tabPanel.setActiveTab(tab);
                    first = false;
                }
            });

            me.show();
        });
        me.shopStore.load();

        return [me.tabPanel];
    }
});
