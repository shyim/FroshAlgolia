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

            <!-- Price facet -->
            <div id="price" class="facet"></div>

            <!-- Numeric refinement list facet -->
            <div id="numericRefinementList" class="facet"></div>

            <!-- Toggle -->
            <div id="toggle" class="facet"></div>

            <!-- Price ranges -->
            <div id="priceRanges" class="facet"></div>

            <!-- Numeric selector -->
            <div id="numericSelector" class="facet"></div>

            <!-- Start rating -->
            <div id="starRating" class="facet"></div>

            <!-- Manufacturer facet -->
            <div id="manufacturerName" class="facet"></div>

            <!-- Category facet -->
            <div id="category" class="facet"></div>

        </div>

    </div>


        {*{foreach from=$filterOptions item=filterOption}*}
            {*{if $filterOption->isFilterable()}*}
                {*<div id="filterOption-{$filterOption->getId()}" class="facet"></div>*}
            {*{/if}*}
        {*{/foreach}*}

{/block}

{* Main content *}
{block name='frontend_index_content'}

    {* Include hogan.js template files *}
    {include file='frontend/instant_search/serp/hit.tpl'}
    {include file='frontend/instant_search/serp/no-result.tpl'}
    {include file='frontend/instant_search/serp/stat.tpl'}

    {* Defining the structure of instant search container *}
    <div class="content listing--content" data-algolia="true"
         data-appId="{$algoliaApplicationId}"
         data-apiKey="{$algoliaSearchOnlyApiKey}"
         data-indexName="{$indexName}"
         data-noImage="{link file='frontend/_public/src/img/no-picture.jpg'}"
         data-currentCategory="{$sCategoryContent.name}"
         data-sortOrderIndex="{$sortOrderIndex}"
         data-compare-ajax="true"
         data-ajax-wishlist="true">

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
                <div class="listing">
                    <div id="hits" class="block-group"></div>
                </div>
            </div>
            <div class="listing--bottom-paging">
                <div class="listing--paging panel--paging">
                    <div id="pagination"></div>
                    <div id="hits-per-page"></div>
                </div>
            </div>
        </div>

    </div>

    {* Defining the structure of instant search container *}
    {*<div class="algolia--container"*}
         {*data-algolia="true"*}
         {*data-appId="{$algoliaApplicationId}"*}
         {*data-apiKey="{$algoliaSearchOnlyApiKey}"*}
         {*data-indexName="{$indexName}"*}
         {*data-noImage="{link file='frontend/_public/src/img/no-picture.jpg'}"*}
         {*data-currentCategory="{$sCategoryContent.name}"*}
         {*data-sortOrderIndex="{$sortOrderIndex}">*}
        {*<main>*}
            {*<div id="left-column">*}
                {*<div id="currentRefinedValues"></div>*}
                {*{foreach from=$filterOptions item=filterOption}*}
                    {*{if $filterOption->isFilterable()}*}
                        {*<div id="filterOption-{$filterOption->getId()}" class="facet"></div>*}
                    {*{/if}*}
                {*{/foreach}*}
                {*<div id="manufacturerName" class="facet"></div>*}
                {*<div id="price" class="facet"></div>*}
                {*<div id="category" class="facet"></div>*}
            {*</div>*}

            {*<div id="right-column">*}
                {*<div class="listing--wrapper">*}
                    {*<div class="listing--actions is--rounded">*}
                        {*<div class="action--filter-btn">*}
                            {*Show meta stats here*}
                        {*</div>*}
                        {*<form class="action--sort action--content block" method="get" data-action-form="true">*}
                            {*<input type="hidden" name="p" value="1">*}
                            {*<label class="sort--label action--label">Sortierung:</label>*}
                            {*<div class="js--fancy-select sort--select"><select name="o" class="sort--field action--field" data-auto-submit="true" data-class="sort--select">*}
                                    {*<option value="1" selected="selected">Erscheinungsdatum</option>*}
                                    {*<option value="2">Beliebtheit</option>*}
                                    {*<option value="3">Niedrigster Preis</option>*}
                                    {*<option value="4">Höchster Preis</option>*}
                                    {*<option value="5">Artikelbezeichnung</option>*}
                                {*</select><div class="js--fancy-select-text">Erscheinungsdatum</div><div class="js--fancy-select-trigger"><i class="icon--arrow-down"></i></div></div>*}
                        {*</form>*}

                    {*</div>*}
                    {*<div class="listing--container">*}
                        {*<div class="algolia--container">*}
                            {*<div id="sort-by-wrapper"><span id="sort-by"></span></div>*}
                            {*<div id="stats"></div>*}
                            {*<div id="hits" class="block-group"></div>*}
                            {*<div id="pagination"></div>*}
                            {*<div id="hits-per-page"></div>*}
                        {*</div>*}
                    {*</div>*}
                {*</div>*}
            {*</div>*}
        {*</main>*}
    {*</div>*}
{/block}
