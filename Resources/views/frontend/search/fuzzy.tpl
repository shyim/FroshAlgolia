{*extends file="parent:frontend/search/fuzzy.tpl"*}
{extends file="parent:frontend/index/index.tpl"}

{* Breadcrumb *}
{block name='frontend_index_start'}{/block}

{* Shop navigation *}
{*block name='frontend_index_shop_navigation'}yyy{/block*}
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
                <div id="category" class="facet"></div>
                <div id="manufacturerName" class="facet"></div>
                <div id="price" class="facet"></div>
            </div>

            <div id="right-column">
                <div id="search-input2"></div>
                <div id="sort-by-wrapper"><span id="sort-by"></span></div>
                <div id="stats"></div>
                <div id="hits"></div>
                <div id="pagination"></div>
            </div>
        </main>
    </div>

    {literal}
        <script type="text/html" id="hit-template">
            <div class="hit">
                <div class="hit-image">
                    <img src="{{image}}" alt="{{name}}">
                </div>
                <div class="hit-content">
                    <h3 class="hit-price">{{currencySymbol}} {{price}}</h3>
                    <h2 class="hit-name"><a href="{{link}}">{{{_highlightResult.name.value}}}</a></h2>
                    <p class="hit-description">{{{_highlightResult.description.value}}}</p>
                </div>
            </div>
        </script>
        <script type="text/html" id="no-results-template">
            <div id="no-results-message">
                <p>We didn't find any results for the search <em>"{{query}}"</em>.</p>
                <a href="." class="clear-all">Clear search</a>
            </div>
        </script>
    {/literal}
    {literal}
        <script language="JavaScript">

            function getTemplate(templateName) {
                return document.getElementById(templateName + '-template').innerHTML;
            }

            var search = instantsearch({
                // Replace with your own values
                appId: '{/literal}{$algoliaApplicationId}{literal}',
                apiKey: '{/literal}{$algoliaSearchOnlyApiKey}{literal}', // search only API key, no ADMIN key
                indexName: '{/literal}{$indexName}{literal}',
                urlSync: true
            });

            // Add searchbox widget
            search.addWidget(
                    instantsearch.widgets.searchBox({
                        container: '#search-input',
                        placeholder: 'Search for products'
                    })
            );

            search.addWidget(
                    instantsearch.widgets.hits({
                        container: '#hits',
                        hitsPerPage: 10,
                        templates: {
                            item: getTemplate('hit'),
                            empty: getTemplate('no-results')
                        }
                    })
            );

            search.addWidget(
                    instantsearch.widgets.stats({
                        container: '#stats'
                    })
            );

            search.addWidget(
                    instantsearch.widgets.sortBySelector({
                        container: '#sort-by',
                        autoHideContainer: true,
                        indices: [{
                            name: search.indexName, label: 'Most relevant'
                        }, {
                            name: search.indexName + '_price_asc', label: 'Lowest price'
                        }, {
                            name: search.indexName + '_price_desc', label: 'Highest price'
                        }]
                    })
            );

            search.addWidget(
                    instantsearch.widgets.pagination({
                        container: '#pagination'
                    })
            );

            search.addWidget(
                    instantsearch.widgets.refinementList({
                        container: '#category',
                        attributeName: 'categories',
                        limit: 10,
                        sortBy: ['isRefined', 'count:desc', 'name:asc'],
                        operator: 'or',
                        templates: {
                            header: '<h5>Category</h5>'
                        }
                    })
            );
            search.addWidget(
                    instantsearch.widgets.menu({
                        container: '#manufacturerName',
                        attributeName: 'manufacturerName',
                        limit: 10,
                        sortBy: ['isRefined', 'count:desc', 'name:asc'],
                        operator: 'or',
                        templates: {
                            header: '<h5>Manufacturer</h5>'
                        }
                    })
            );
            search.addWidget(
                    instantsearch.widgets.rangeSlider({
                        container: '#price',
                        attributeName: 'price',
                        templates: {
                            header: '<h5>Price</h5>'
                        }
                    })
            );
//            search.addWidget(
//                    instantsearch.widgets.menu({
//                        container: '#type',
//                        attributeName: 'type',
//                        limit: 10,
//                        sortBy: ['isRefined', 'count:desc', 'name:asc'],
//                        templates: {
//                            header: '<h5>Type</h5>'
//                        }
//                    })
//            );

            search.start();

        </script>
    {/literal}

{/block}
