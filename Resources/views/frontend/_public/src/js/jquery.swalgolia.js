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
        sortOrderIndex: false,
        searchPlaceholder: 'Suchbegriff...',
        hitTemplate: 'hit-template',
        noResultTemplate: 'no-result-template',
        statTemplate: 'stat-template',
        hitsContainerSelector: '#hits',
        statsContainerSelector: '#stats',
        paginationContainerSelector: '#pagination',
        searchInputContainerSelector: '#search-input',
        hitsPerPageContainerSelector: '#hits-per-page',
        sortByContainerSelector: '#sort-by',
        facetWidgetsConfig: '',
        pages: '12|24',
        pagesSnippet: '',
        defaultPages: 12
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

        me.search.on('render', function () {
            window.StateManager.updatePlugin('select:not([data-no-fancy-select="true"])', 'swSelectboxReplacement');
            window.StateManager.destroyPlugin('*[data-compare-ajax="true"]', 'swProductCompareAdd');
            window.StateManager.destroyPlugin('*[data-ajax-wishlist="true"]', 'swAjaxWishlist');
            window.StateManager.removePlugin('*[data-compare-ajax="true"]', 'swProductCompareAdd');
            window.StateManager.removePlugin('*[data-ajax-wishlist="true"]', 'swAjaxWishlist');
            window.StateManager.addPlugin('*[data-compare-ajax="true"]', 'swProductCompareAdd');
            window.StateManager.addPlugin('*[data-ajax-wishlist="true"]', 'swAjaxWishlist');
        });

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

        return instantsearch({
            appId: me.opts.appId,
            apiKey: me.opts.apiKey,
            indexName: me.opts.indexName,
            urlSync: true
        });
    },

    /**
     * Add the default instant search widgets like hits, pagination or active filters
     */
    addDefaultWidgets: function () {

        var me = this;

        // Hits widget
        me.search.addWidget(
            instantsearch.widgets.hits({
                container: me.opts.hitsContainerSelector,
                hitsPerPage: parseInt(me.opts.defaultPages),
                templates: {
                    item: me.getTemplate(me.opts.hitTemplate),
                    empty: me.getTemplate(me.opts.noResultTemplate)
                },
                transformData: {
                    item: function (hit) {
                        hit.stars = [];
                        if (hit.voteAvgPoints > 0) {
                            for (var i = 0; i < hit.voteAvgPoints; i++) {
                                hit.stars[i] = hit.voteAvgPoints;
                            }
                        }
                        return hit;
                    }
                }
            })
        );

        // Meta stats widget
        me.search.addWidget(
            instantsearch.widgets.stats({
                container: me.opts.statsContainerSelector,
                templates: {
                    body: me.getTemplate(me.opts.statTemplate)
                }
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
                autofocus: true,
                placeholder: me.opts.searchPlaceholder,
                poweredBy: me.opts.showAlgoliaLogo
            })
        );


        var pages = me.opts.pages.split('|');
        var options = [];

        pages.forEach(function(page) {
            options.push({value: parseInt(page), label: me.opts.pagesSnippet + ' ' + page});
        });

        // Hits per page select field
        me.search.addWidget(
            instantsearch.widgets.hitsPerPageSelector({
                container: me.opts.hitsPerPageContainerSelector,
                options: options
            })
        );

        // Sort select field
        me.search.addWidget(
            instantsearch.widgets.sortBySelector({
                container: me.opts.sortByContainerSelector,
                autoHideContainer: true,
                indices: me.opts.sortOrderIndex,
                cssClasses: [{
                    root: 'sort--field action--field',
                    item: ''
                }]
            })
        );

        // List of currently refined (active) filter
        me.search.addWidget(
            instantsearch.widgets.currentRefinedValues({
                container: '#currentRefinedValues',
                clearAll: 'after',
                cssClasses: {
                    item: 'filter--active'
                },
                templates: {
                    header: '',
                    item: '<span class="filter--active-icon"></span>{{name}}<span class="ais-current-refined-values--count ">{{count}}</span>'
                }
            })
        );

    },

    /**
     * Add the facet widgets (filter widgets)
     */
    addFacets: function () {
        var me = this;

        // Iterate over all configured facets and add them to the SERP
        $.each(me.opts.facetWidgetsConfig, function (widgetName, widgetConfig) {
            me.search.addWidget(me.buildFacet(widgetName,widgetConfig));
        });

    },

    /**
     * Builds the facet widget according to the passed widget config
     * @param widgetName
     * @param widgetConfig
     * @returns {*}
     */
    buildFacet: function(widgetName, widgetConfig) {


        // Build basic widget data
        var data =
            {
                container: '#' + widgetName.replace('.', '_').toLowerCase(),
                attributeName: widgetName,
                templates: {
                    header: '<div class="shop-sites--headline navigation--headline">' + (typeof widgetConfig.header === 'undefined' ? widgetName : widgetConfig.header) + '</div>'
                }
            }
        ;

        // Add specific widget settings to the facet data array
        $.each(widgetConfig, function (key, value) {
            if(!data.hasOwnProperty(key)) {
                data[key] = value;
            }
        });

        return instantsearch.widgets[widgetConfig.widgetType](data);

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
        String.prototype.replaceAll = function (target, replacement) {
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
