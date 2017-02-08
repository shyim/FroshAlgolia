Ext.define('Shopware.apps.Algolia.store.Sort', {
    extend: 'Ext.data.Store',
    fields: [
        {
            name: 'name',
            type: 'string'
        }
    ],
    data: [
        {
            name: 'asc'
        },
        {
            name: 'desc'
        }
    ]
});
