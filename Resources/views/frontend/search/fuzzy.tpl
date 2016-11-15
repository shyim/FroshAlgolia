{*extends file="parent:frontend/search/fuzzy.tpl"*}
{extends file="parent:frontend/index/index.tpl"}

{* Breadcrumb *}
{block name='frontend_index_start'}{/block}

{* Shop navigation *}
{block name='frontend_index_search'}
    <li class="navigation--entry entry--search" role="menuitem" data-search="true" aria-haspopup="true"{if $theme.focusSearch && {controllerName|lower} == 'index'} data-activeOnStart="true"{/if}>
        <a class="btn entry--link entry--trigger" href="#show-hide--search" title="{"{s namespace='frontend/index/search' name="IndexTitleSearchToggle"}{/s}"|escape}">
            <i class="icon--search"></i>

            {block name='frontend_index_search_display'}
                <span class="search--display">{s namespace='frontend/index/search' name="IndexSearchFieldSubmit"}{/s}</span>
            {/block}
        </a>

        {* Include of the search form *}
        {block name='frontend_index_search_include'}
            <div id="search-input"></div>
            <svg class="aa-input-icon" viewBox="654 -372 1664 1664">
                <path d="M1806,332c0-123.3-43.8-228.8-131.5-316.5C1586.8-72.2,1481.3-116,1358-116s-228.8,43.8-316.5,131.5  C953.8,103.2,910,208.7,910,332s43.8,228.8,131.5,316.5C1129.2,736.2,1234.7,780,1358,780s228.8-43.8,316.5-131.5  C1762.2,560.8,1806,455.3,1806,332z M2318,1164c0,34.7-12.7,64.7-38,90s-55.3,38-90,38c-36,0-66-12.7-90-38l-343-342  c-119.3,82.7-252.3,124-399,124c-95.3,0-186.5-18.5-273.5-55.5s-162-87-225-150s-113-138-150-225S654,427.3,654,332  s18.5-186.5,55.5-273.5s87-162,150-225s138-113,225-150S1262.7-372,1358-372s186.5,18.5,273.5,55.5s162,87,225,150s113,138,150,225  S2062,236.7,2062,332c0,146.7-41.3,279.7-124,399l343,343C2305.7,1098.7,2318,1128.7,2318,1164z" />
            </svg>
            <svg class="aa-input-close" id="icon-close" viewBox="0 0 26 25">
                <polygon points="26.2,23 15.4,12.5 26.2,2 23.9,-0.4 13,10.2 2.1,-0.4 -0.2,2 10.6,12.5 -0.2,23 2.1,25.4 13,14.8     23.9,25.4" />
            </svg>
        {/block}
    </li>

{/block}

{* Main content *}
{block name='frontend_index_content'}
    <div class="ai-container">
        <main>
            <div id="left-column">
                <div id="currentRefinedValues"></div>
                {foreach from=$filterOptions item=filterOption}
                    {if $filterOption->isFilterable()}
                        <div id="filterOption-{$filterOption->getId()}" class="facet"></div>
                    {/if}
                {/foreach}
                <div id="manufacturerName" class="facet"></div>
                <div id="price" class="facet"></div>
                <div id="category" class="facet"></div>
            </div>

            <div id="right-column">
                <div id="sort-by-wrapper"><span id="sort-by"></span></div>
                <div id="stats"></div>
                <div id="hits"></div>
                <div id="pagination"></div>
            </div>
        </main>
    </div>

    {*
    The Hogan.js themes for displaying the hits
    --------------------
    To avoid conflicts with the SW postfilter please replace the default HTML attributes as follows:
    - href -> link
    - src -> source
    *}
    {literal}
        <script type="text/html" id="hit-template">
            <a link="{{{link}}}">
                <div class="hit">
                    <div class="hit-image">
                        <img source="{{image}}" alt="{{name}}">
                    </div>
                    <div class="hit-content">
                        <h3 class="hit-price">{{{currencySymbol}}} {{price}}</h3>
                        <h2 class="hit-name">{{{_highlightResult.name.value}}}</h2>
                        <p class="hit-description">{{{_highlightResult.description.value}}}</p>
                    </div>
                </div>
            </a>
        </script>

        <script type="text/html" id="no-results-template">
            <div id="no-results-message">
                <p>{/literal}{s name="noResultFound" namespace="bundle/translation"}{/s}{literal}</p>
                <a href="." class="clear-all">{/literal}{s name="clearSearch" namespace="bundle/translation"}{/s}{literal}</a>
            </div>
        </script>

        <script type="text/html" id="meta-stats-template">
            <div id="meta-stats">
                {/literal}{s name="metaStatsResults" namespace="bundle/translation"}{/s}{literal}
            </div>
        </script>

    {/literal}
    {literal}
        <script language="JavaScript">

            /**
             * Small helper method to grab the Hogan template
             * @param templateName
             * @returns {string}
             */
            function getTemplate(templateName) {

                var templateContent = document.getElementById(templateName + '-template').innerHTML;

                /**
                * Due to the fact, that the SW PostFilter automatically adds the hostname to different DOM element attributes
                 * (like href, src) it´s necessary to work with fake attributes and replace them on client side with the correct
                 * HTML attribute.
                */
                templateContent  = templateContent.replace('link','href');
                templateContent  = templateContent.replace('source','src');

                console.log(templateContent);

                return templateContent;
            }

            /**
             * Initialize instantsearch. urlsync is used to adopt the browser url bar to the actual search conditions
             * which allows the user to copy-paste the url for an exact search definition.
             */
            var search = instantsearch({
                appId: '{/literal}{$algoliaApplicationId}{literal}',
                apiKey: '{/literal}{$algoliaSearchOnlyApiKey}{literal}', // search only API key, no ADMIN key
                indexName: '{/literal}{$indexName}{literal}',
                urlSync: true
            });

            // Add searchbox widget
            search.addWidget(
                    instantsearch.widgets.searchBox({
                        container: '#search-input',
                        placeholder: '{/literal}{s name="indexSearchFieldPlaceholder" namespace="frontend/index/search"}{/s}{literal}'
                    })
            );

            search.addWidget(
                    instantsearch.widgets.hits({
                        container: '#hits',
                        hitsPerPage: 50,
                        templates: {
                            item: getTemplate('hit'),
                            empty: getTemplate('no-results')
                        }
                    })
            );

            search.addWidget(
                    instantsearch.widgets.stats({
                        container: '#stats',
                        templates: {
                            body: getTemplate('meta-stats'),
                        }
                    })
            );

            search.addWidget(
                    instantsearch.widgets.sortBySelector({
                        container: '#sort-by',
                        autoHideContainer: true,
                        indices: [{
                            name: search.indexName, label: '{/literal}{s name="orderMostRelevant" namespace="bundle/translation"}{/s}{literal}'
                        }, {
                            name: search.indexName + '_price_asc', label: '{/literal}{s name="orderLowestPrice" namespace="bundle/translation"}{/s}{literal}'
                        }, {
                            name: search.indexName + '_price_desc', label: '{/literal}{s name="orderHighestPrice" namespace="bundle/translation"}{/s}{literal}'
                        }]
                    })
            );

            search.addWidget(
                    instantsearch.widgets.pagination({
                        container: '#pagination'
                    })
            );


            // Current refined values
            search.addWidget(
                    instantsearch.widgets.currentRefinedValues({
                        container: '#currentRefinedValues',
                        clearAll: 'after',
                        templates: {
                            header: '<h5>{/literal}{s name="activeFilters" namespace="bundle/translation"}{/s}{literal}</h5>'
                        }
                    })
            );

            // Show refinement widgets by properties
            {/literal}
            {foreach from=$filterOptions item=filterOption}
                {if $filterOption->isFilterable()}
                    {literal}
                        search.addWidget(
                                instantsearch.widgets.refinementList({
                                    container: '#filterOption-{/literal}{$filterOption->getId()}{literal}',
                                    attributeName: 'properties.{/literal}{$filterOption->getName()}{literal}',
                                    limit: 10,
                                    sortBy: ['isRefined', 'count:desc', 'name:asc'],
                                    operator: 'or',
                                    templates: {
                                        header: '<h5>{/literal}{$filterOption->getName()}{literal}</h5>'
                                    }
                                })
                        );
                    {/literal}
                {/if}
            {/foreach}
            {literal}

            // Categories refinement widget
            search.addWidget(

                    instantsearch.widgets.{/literal}{$facetFilterWidgetConfig->categories->widgetType}{literal} ({
                        container: '#category',
                        attributeName: 'categories',
                        limit: 10,
                        sortBy: ['isRefined', 'count:desc', 'name:asc'],
                        {/literal}{if ($facetFilterWidgetConfig->categories->widgetType == 'refinementList' && $facetFilterWidgetConfig->categories->match)}{literal}
                        operator: '{/literal}{$facetFilterWidgetConfig->categories->match}{literal}',
                        {/literal}{/if}{literal}
                        templates: {
                            header: '<h5>{/literal}{s name="filterCategory" namespace="bundle/translation"}{/s}{literal}</h5>'
                        }
                    })
            );

            // Manufacturer refinement widget
            search.addWidget(
                    instantsearch.widgets.menu({
                        container: '#manufacturerName',
                        attributeName: 'manufacturerName',
                        limit: 10,
                        sortBy: ['isRefined', 'count:desc', 'name:asc'],
                        operator: 'or',
                        templates: {
                            header: '<h5>{/literal}{s name="filterManufacturer" namespace="bundle/translation"}{/s}{literal}</h5>'
                        }
                    })
            );

            // Price range slider
            search.addWidget(
                    instantsearch.widgets.rangeSlider({
                        container: '#price',
                        attributeName: 'price',
                        templates: {
                            header: '<h5>{/literal}{s name="filterPrice" namespace="bundle/translation"}{/s}{literal}</h5>'
                        }
                    })
            );

            // Start instantsearch
            search.start();

        </script>
    {/literal}

{/block}
