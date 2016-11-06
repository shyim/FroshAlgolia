$.plugin('swAlgolia', {
    defaults: {
        appId: false,
        apiKey: false,
        indexName: false,
        showAlgoliaLogo: false,
        noImage: '',
        currentCategory: '',
        searchPlaceholder: 'Suchbegriff...',
        hitTemplate: '<div class="product--box box--basic" data-ordernumber="{{number}}">\n            <div class="box--content is--rounded">\n                <a href="{{link}}"\n                   title="{{name}}"\n                   class="product--image">\n                    <span class="image--element">\n                        <span class="image--media">\n                            <img srcset="{{#helpers.image}}{{image}}{{/helpers.image}}">\n                        </span>\n                    </span>\n                </a>\n                <div class="product--info">\n                    <a href="{{link}}"\n                       class="product--title"\n                       title="{{name}}">\n                        {{name}}\n                    </a>\n                    <div class="product--description">\n                        {{description}}\n                    </div>\n                    <div class="product--price-info">\n                        <div class="product--price">\n                            <span class="price--default is--nowrap">\n                                {{#helpers.price}}{{price}}{{/helpers.price}}\n                            </span>\n                        </div>\n                    </div>\n                </div>\n            </div>\n        </div>',
        noResultTemplate: '<div id="no-results-message">\n    <p>We didnt find any results for the search <em>"{{query}}"</em>.</p>\n    <a href="." class="clear-all">Clear search</a>\n</div'
    },
    init: function () {
        var me = this;

        me.applyDataAttributes();
        me.search = me.initSearch();

        me.search.templatesConfig.helpers.price = function(text, render) {
            return me.numberFormat(parseFloat(render(text)), 2, 3, '.', ',') + ' €*';
        };

        me.search.templatesConfig.helpers.image = function(text, render) {
            var renderedText = render(text);

            if (renderedText == null) {
                return me.opts.noImage;
            }

            return renderedText;
        };

        // Add Widgets to InstantSearch
        me.addDefaultWidgets();
        me.addFacets();
        me.search.start();

    },

    numberFormat: function (number, n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = number.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    },

    initSearch: function () {
        var me = this;
        return instantsearch({
            // Replace with your own values
            appId: me.opts.appId,
            apiKey: me.opts.apiKey,
            indexName: me.opts.indexName,
            urlSync: {
                useHash: true
            },
            searchParameters: {
                facetsRefinements: {
                    category: [me.opts.currentCategory]
                }
            }
        });
    },

    addDefaultWidgets: function () {
        var me = this;

        // Products
        me.search.addWidget(
            instantsearch.widgets.hits({
                container: '#hits',
                hitsPerPage: 10,
                templates: {
                    item: me.opts.hitTemplate,
                    empty: me.opts.noResultTemplate
                }
            })
        );

        // Search Stats
        me.search.addWidget(
            instantsearch.widgets.stats({
                container: '#stats'
            })
        );

        // Pagination
        me.search.addWidget(
            instantsearch.widgets.pagination({
                container: '#pagination'
            })
        );

        // Search Input
        me.search.addWidget(
            instantsearch.widgets.searchBox({
                container: '#search-input',
                placeholder: me.opts.searchPlaceholder,
                poweredBy: me.opts.showAlgoliaLogo
            })
        );

        me.search.addWidget(
            instantsearch.widgets.hitsPerPageSelector({
                container: '.algolia-pagination',
                options: [
                    {value: 6, label: '6 per page'},
                    {value: 12, label: '12 per page'},
                    {value: 24, label: '24 per page'}
                ]
            })
        )
    },

    addFacets: function () {
        var me = this;

        me.search.addWidget(
            instantsearch.widgets.rangeSlider({
                container: '#price',
                attributeName: 'price',
                templates: {
                    header: '<h5>Preis</h5>'
                }
            })
        );

        me.search.addWidget(
            instantsearch.widgets.refinementList({
                container: '#brand',
                attributeName: 'manufacturer_name',
                limit: 10,
                sortBy: ['isRefined', 'count:desc', 'name:asc'],
                operator: 'or',
                templates: {
                    header: '<h5>Hersteller</h5>'
                }
            })
        );

        me.search.addWidget(
            instantsearch.widgets.refinementList({
                container: '#category',
                attributeName: 'category',
                limit: 10,
                sortBy: ['isRefined', 'count:desc', 'name:asc'],
                operator: 'or',
                templates: {
                    header: '<h5>Kategorie</h5>'
                }
            })
        );
    }
});
$(function () {
    window.StateManager.addPlugin('*[data-algolia="true"]', 'swAlgolia');
});
