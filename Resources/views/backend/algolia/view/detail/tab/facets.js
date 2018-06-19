Ext.define('Shopware.apps.Algolia.view.detail.tab.Facets', {
    extend: 'Ext.container.Container',
    layout:'column',
    defaults: {
        columnWidth: 1,
        labelWidth: 180,
        margin: '0 0 20 0'
    },
    title: 'Facets',
    padding: 5,
    autoScroll: true,

    initComponent: function () {
        var me = this;

        me.items = me.getItems();

        me.callParent(arguments);
    },

    getItems: function () {
        //{literal}
        return [
            {
                xtype: 'textfield',
                name: 'facetFilterWidget',
                fieldLabel: 'Configuration for the facet filter widgets on the instant search page'
            }
        ];

        //{/literal}
    }
});
