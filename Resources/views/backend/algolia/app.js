Ext.define('Shopware.apps.Algolia', {
    extend: 'Enlight.app.SubApplication',

    name: 'Shopware.apps.Algolia',

    loadPath: '{url action=load}',
    bulkLoad: true,

    controllers: [ 'Main' ],

    views: [
        'detail.Window',
        'detail.Tab',
        'detail.tab.Index',
        'element.Grid'
    ],

    models: [],
    stores: ['Attributes', 'Sort', 'Facets'],

    launch: function() {
        return this.getController('Main').mainWindow;
    }
});
