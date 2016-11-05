{extends file="parent:frontend/listing/index.tpl"}

{block name='frontend_index_content_left'}
<aside class="sidebar-main off-canvas">
    <div id="price" class="facet" style="width: 90%;padding-left: 10px;"></div>
    <div id="brand" class="facet"></div>
    <div id="category" class="facet"></div>

</aside>
{/block}

{block name='frontend_index_content'}
<div class="content listing--content">
    {literal}
        <img src="a"></img>
    <script type="text/html" id="hit-template">
        <div class="product--box box--basic" data-ordernumber="{{number}}">
            <div class="box--content is--rounded">
                <a href="{{link}}"
                   title="{{name}}"
                   class="product--image">
                    <span class="image--element">
                        <span class="image--media">
                            <img srcset="{{image}}">
                        </span>
                    </span>
                </a>
                <div class="product--info">
                    <a href="{{link}}"
                       class="product--title"
                       title="{{name}}">
                        {{name}}
                    </a>
                    <div class="product--description">
                        {{description}}
                    </div>
                    <div class="product--price-info">
                        <div class="product--price">
                            <span class="price--default is--nowrap">
                                {{price}} €*
                            </span>
                        </div>
                    </div>
                </div>
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
    <div class="listing--wrapper{if !$showListing} is--hidden{/if}">
        <div class="listing--container">
            <div id="search-input"></div>
            <div id="sort-by-wrapper"><span id="sort-by"></span></div>
            <div id="stats"></div>
            <div id="hits" class="block-group"></div>
            <div id="pagination"></div>
        </div>
    </div>

    <script>
        {literal}
        function getTemplate(templateName) {
            return document.getElementById(templateName + '-template').innerHTML;
        }
        var search = instantsearch({
            // Replace with your own values
            appId: '{/literal}{$algoliaApplicationId}{literal}',
            apiKey: '{/literal}{$algoliaSearchOnlyApiKey}{literal}', // search only API key, no ADMIN key
            indexName: '{/literal}{$indexName}{literal}'
            ,
            urlSync: {
                useHash: true
            }
        });
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
            instantsearch.widgets.rangeSlider({
                container: '#price',
                attributeName: 'price',
                templates: {
                    header: '<h5>Preis</h5>'
                }
            })
        );
        search.addWidget(
            instantsearch.widgets.pagination({
                container: '#pagination'
            })
        );
        search.addWidget(
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
        search.addWidget(
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
        search.addWidget(
            instantsearch.widgets.searchBox({
                container: '#search-input',
                placeholder: 'Suche nach Produkten'
            })
        );
        search.start();
        {/literal}
    </script>
    </div>
{/block}
