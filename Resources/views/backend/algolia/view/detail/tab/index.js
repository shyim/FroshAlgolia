Ext.define('Shopware.apps.Algolia.view.detail.tab.Index', {
    extend: 'Ext.container.Container',
    layout:'column',
    defaults: {
        columnWidth: 1,
        labelWidth: 180,
        margin: '0 0 20 0'
    },
    title: 'Index-Configuration',
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
                xtype: 'algolia-element-grid',
                title: 'Searchable attributes (ordered by importance)',
                name: 'sortableAttributes',
                height: 200,
                columns: [
                    {
                        header: 'Field',
                        dataIndex: 'name',
                        flex: 1,
                        editor: {
                            xtype: 'combo',
                            displayField: 'name',
                            valueField: 'name',
                            store: Ext.create('Shopware.apps.Algolia.store.Attributes'),
                            queryMode: 'local'
                        }
                    }
                ]
            },
            {
                xtype: 'algolia-element-grid',
                title: 'Custom ranking attributes for main index (ordered by importance)',
                name: 'rankingAttributes',
                height: 200,
                columns: [
                    {
                        header: 'Field',
                        dataIndex: 'name',
                        flex: 1,
                        editor: {
                            xtype: 'combo',
                            displayField: 'name',
                            valueField: 'name',
                            store: Ext.create('Shopware.apps.Algolia.store.Attributes'),
                            queryMode: 'local'
                        }
                    },
                    {
                        header: 'Sort',
                        dataIndex: 'sort',
                        flex: 1,
                        editor: {
                            xtype: 'combo',
                            displayField: 'name',
                            valueField: 'name',
                            store: Ext.create('Shopware.apps.Algolia.store.Sort'),
                            queryMode: 'local'
                        }
                    }
                ]
            },
            {
                xtype: 'textfield',
                name: 'rankingAttributes',
                fieldLabel: 'Configuration for the facet filter widgets on the instant search page',
                value: '{"categories":{"widgetType":"refinementList","match":"or"},"manufacturerName":{"widgetType":"refinementList","match":"or"},"price":{"widgetType":"rangeSlider"},"properties.Flaschengröße":{"widgetType":"rangeSlider"},"properties.Farbe":{"widgetType":"refinementList","match":"or"},"properties.Alkoholgehalt":{"widgetType":"numericRefinementList"},"properties.Geschmack":{"widgetType":"refinementList","match":"or"},"properties.Trinktemperatur":{"widgetType":"refinementList","match":"or"}}'
            },
            {
                xtype: 'algolia-element-grid',
                title: 'Attributes for faceting',
                name: 'facetAttributes',
                height: 200,
                columns: [
                    {
                        header: 'Field',
                        dataIndex: 'name',
                        flex: 1,
                        editor: {
                            xtype: 'combo',
                            displayField: 'name',
                            valueField: 'name',
                            store: Ext.create('Shopware.apps.Algolia.store.Attributes'),
                            queryMode: 'local'
                        }
                    }
                ]
            },
            {
                xtype: 'textfield',
                name: 'blockedAttributes',
                fieldLabel: 'Blocked article attributes',
                value: 'id,articleID,articledetailsID'
            }
        ];

        //{/literal}
    }
});
