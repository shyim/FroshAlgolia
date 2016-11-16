/**
 * The swAlgolia jQuery plugin is used to generate the output on every instant search
 * page. These are for example the SERP or the category listing which allows a typ-as-you-go
 * refinement of the list.
 */
$.plugin('swAlgolia', {
    defaults: {
        appId: false,
        apiKey: false,
        indexName: false,
        showAlgoliaLogo: false,
        noImage: '',
        currentCategory: '',
        searchPlaceholder: 'Suchbegriff...',
        hitTemplate: 'hit-template',
        noResultTemplate: 'no-result-template',
        hitsContainerSelector: '#hits',
        statsContainerSelector: '#stats',
        paginationContainerSelector: '#pagination',
        searchInputContainerSelector: '#search-input',
        hitsPerPageContainerSelector: '#hits-per-page',
        sortByContainerSelector: '#sort-by'
    },

    // Init jQuery plugin for instant search
    init: function () {
        var me = this;

        me.applyDataAttributes();
        me.search = me.initSearch();

        // Price helper
        me.search.templatesConfig.helpers.price = function (text, render) {
            return me.numberFormat(parseFloat(render(text)), 2, 3, '.', ',') + ' €*';
        };

        // Image helper function
        me.search.templatesConfig.helpers.image = function (text, render) {
            var renderedText = render(text);

            if (renderedText == null) {
                return me.opts.noImage;
            }

            return renderedText;
        };

        // Add Widgets to InstantSearch
        me.addDefaultWidgets();
        me.addFacets();

        // Start instant search
        me.search.start();

    },

    // Format numbers
    numberFormat: function (number, n, x, s, c) {
        var re = '\\d(?=(\\d{' + (x || 3) + '})+' + (n > 0 ? '\\D' : '$') + ')',
            num = number.toFixed(Math.max(0, ~~n));

        return (c ? num.replace('.', c) : num).replace(new RegExp(re, 'g'), '$&' + (s || ','));
    },

    // Init instant search
    initSearch: function () {

        var me = this;
        console.log(me.opts);

        return instantsearch({
            appId: me.opts.appId,
            apiKey: me.opts.apiKey,
            indexName: me.opts.indexName,
            urlSync: {
                useHash: true
            },
            searchParameters: {
                facetsRefinements: {
                    categories: [me.opts.currentCategory]
                }
            }
        });
    },

    /**
     * Add the default instant search widgets like hits or pagination
     */
    addDefaultWidgets: function () {
        var me = this;

        // Hits widget
        me.search.addWidget(
            instantsearch.widgets.hits({
                container: me.opts.hitsContainerSelector,
                hitsPerPage: 10,
                templates: {
                    item: me.getTemplate(me.opts.hitTemplate),
                    empty: me.getTemplate(me.opts.noResultTemplate)
                }
            })
        );

        // Meta stats widget
        me.search.addWidget(
            instantsearch.widgets.stats({
                container: me.opts.statsContainerSelector
            })
        );

        // Pagination widget
        me.search.addWidget(
            instantsearch.widgets.pagination({
                container: me.opts.paginationContainerSelector
            })
        );

        // Search Input
        me.search.addWidget(
            instantsearch.widgets.searchBox({
                container: me.opts.searchInputContainerSelector,
                placeholder: me.opts.searchPlaceholder,
                poweredBy: me.opts.showAlgoliaLogo
            })
        );

        // Hits per page select field
        me.search.addWidget(
            instantsearch.widgets.hitsPerPageSelector({
                container: me.opts.hitsPerPageContainerSelector,
                options: [
                    {value: 6, label: '6 per page'},
                    {value: 12, label: '12 per page'},
                    {value: 24, label: '24 per page'}
                ]
            })
        )

        // Sort select field
        me.search.addWidget(
            instantsearch.widgets.sortBySelector({
                container: me.opts.sortByContainerSelector,
                autoHideContainer: true,
                indices: [{
                    name: me.search.indexName, label: '{/literal}{s name="orderMostRelevant" namespace="bundle/translation"}{/s}{literal}'
                }, {
                    name: me.search.indexName + '_price_asc', label: '{/literal}{s name="orderLowestPrice" namespace="bundle/translation"}{/s}{literal}'
                }, {
                    name: me.search.indexName + '_price_desc', label: '{/literal}{s name="orderHighestPrice" namespace="bundle/translation"}{/s}{literal}'
                }]
            })
        );
    },

    /**
     * Add the facet widgets (filter widgets)
     */
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
                container: '#manufacturerName',
                attributeName: 'manufacturerName',
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
                attributeName: 'categories',
                limit: 10,
                sortBy: ['isRefined', 'count:desc', 'name:asc'],
                operator: 'or',
                templates: {
                    header: '<h5>Kategorie</h5>'
                }
            })
        );
    },

    /**
     * Small helper method to grab the Hogan template
     * @param templateName
     * @returns {string}
     */
    getTemplate: function (templateName) {

        var templateContent = document.getElementById(templateName).innerHTML;

        /**
         * Replace all occurrences of a found character in a given string
         * @param target
         * @param replacement
         * @returns {string}
         */
        String.prototype.replaceAll = function(target, replacement) {
            return this.split(target).join(replacement);
        };

        /**
         * Due to the fact, that the SW PostFilter automatically adds the hostname to different DOM element attributes
         * (like href, src, srcset...) it´s necessary to work with fake attributes and replace them on client side with the correct
         * HTML attribute.
         */
        templateContent = templateContent.replaceAll('link="', 'href="').replaceAll('source="', 'src="').replaceAll('sourceset="', 'srcset="');
        return templateContent;
    }

});

/**
 * Add the plugin to the StateManager
 */
$(function () {
    window.StateManager.addPlugin('*[data-algolia="true"]', 'swAlgolia');
});
