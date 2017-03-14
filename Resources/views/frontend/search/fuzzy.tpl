{*extends file="parent:frontend/search/fuzzy.tpl"*}
{extends file="parent:frontend/index/index.tpl"}

{* Breadcrumb *}
{block name='frontend_index_start'}{/block}

{* Shop navigation *}
{block name='frontend_index_search'}
    <li class="navigation--entry entry--search" role="menuitem" data-search="true"
        aria-haspopup="true"{if $theme.focusSearch && {controllerName|lower} == 'index'} data-activeOnStart="true"{/if}>
        <a class="btn entry--link entry--trigger" href="#show-hide--search"
           title="{"{s namespace='frontend/index/search' name="IndexTitleSearchToggle"}{/s}"|escape}">
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

{* Left column with facets *}
{block name='frontend_index_left_inner'}

    <div class="sidebar--categories-wrapper">
        <div class="shop-sites--container is--rounded">

            <!-- Dynamically add the container for the facet widgets -->
            {foreach $facetFilterWidgetConfig as $facetName => $facetConfig}
                <div id="{$facetName|replace:'.':'_'|lower}" class="facet"></div>
            {/foreach}

        </div>

    </div>

{/block}

{* Main content *}
{block name='frontend_index_content'}
    {* Include hogan.js template files *}
    {include file='frontend/instant_search/serp/hit.tpl'}
    {include file='frontend/instant_search/serp/no-result.tpl'}
    {include file='frontend/instant_search/serp/stat.tpl'}

    {* Defining the structure of instant search container *}
    <div class="content listing--content ais--listing-content" data-algolia="true"
         data-appId="{$algoliaApplicationId}"
         data-apiKey="{$algoliaSearchOnlyApiKey}"
         data-indexName="{$indexName}"
         data-noImage="{link file='frontend/_public/src/img/no-picture.jpg'}"
         data-currentCategory="{$sCategoryContent.name}"
         data-sortOrderIndex="{$sortOrderIndex}"
         data-compare-ajax="true"
         data-ajax-wishlist="true"
         data-pages="{config name="numberarticlestoshow"}"
         data-pagesSnippet="{s name="ListingLabelItemsPerPage" namespace="frontend/search/paging"}{/s}"
         data-defaultPages="{config name="articlesperpage"}"
         data-facetWidgetsConfig='{$algoliaConfig.facetFilterWidget}'>

        <div class="listing--wrapper">

            <!-- Listing action -->
            <div class="listing--actions is--rounded">
                <div class="action--filter-btn">
                    <div id="stats"></div>
                </div>
                <div class="action--sort action--content block">
                    <label class="sort--label action--label">Sortierung:</label>
                    <span id="sort-by"></span>
                </div>
            </div>

            <!-- Current refined values -->
            <div id="currentRefinedValues" class="facet"></div>

            <!-- Hit listing -->
            <div class="listing--container">
                <div id="hits" class="listing"></div>
            </div>
            <div class="listing--bottom-paging">
                <div class="listing--paging panel--paging">
                    <div id="pagination"></div>
                    <div id="hits-per-page"></div>
                </div>
            </div>
        </div>

    </div>

{/block}
