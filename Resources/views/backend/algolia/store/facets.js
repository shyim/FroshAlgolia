Ext.define('Shopware.apps.Algolia.store.Facets', {
    extend: 'Ext.data.Store',
    fields: [
        {
            name: 'name',
            type: 'string'
        }
    ],
    data: [
        {
            name: 'categories'
        },
        {
            name: 'manufacturerName'
        },
        {
            name: 'price'
        },
        {
            name: 'properties'
        }
    ]
});
