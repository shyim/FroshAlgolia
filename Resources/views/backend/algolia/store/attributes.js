Ext.define('Shopware.apps.Algolia.store.Attributes', {
    extend: 'Ext.data.Store',
    fields: [
        {
            name: 'name',
            type: 'string'
        }
    ],
    data: [
        {
            name: 'number'
        },
        {
            name: 'name'
        },
        {
            name: 'manufacturerName'
        },
        {
            name: 'description'
        },
        {
            name: 'categories'
        },
        {
            name: 'price'
        },
        {
            name: 'properties'
        }
    ]
});
