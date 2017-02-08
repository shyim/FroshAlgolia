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
                name: 'rankingIndexAttributes',
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
                name: 'facetFilterWidget',
                fieldLabel: 'Configuration for the facet filter widgets on the instant search page'
            },
            {
                xtype: 'algolia-element-grid',
                title: 'Facets',
                name: 'facetAttributes',
                height: 200,
                addText: 'Add new facet',
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
                title: 'Blocked article attributes',
                name: 'blockedAttributes',
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
            }
        ];

        //{/literal}
    }
});
