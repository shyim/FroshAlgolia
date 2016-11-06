{extends file="parent:frontend/listing/index.tpl"}

{block name="frontend_listing_actions_filter_submit_button"}{/block}

{block name="frontend_listing_actions_filter_form_facets"}
    <div id="price" class="facet"></div>
    <div id="brand" class="facet"></div>
    <div id="category" class="facet"></div>
{/block}

{block name='frontend_listing_actions_items_per_page'}
    <div class="algolia-pagination"></div>
{/block}

{block name="frontend_listing_listing_content"}
    <div class="algolia--container"
         data-algolia="true"
         data-appId="{$algoliaApplicationId}"
         data-apiKey="{$algoliaSearchOnlyApiKey}"
         data-indexName="{$indexName}"
         data-noImage="{link file='frontend/_public/src/img/no-picture.jpg'}"
         data-currentCategory="{$sCategoryContent.name}"
    >
        <div id="search-input"></div>
        <div id="sort-by-wrapper"><span id="sort-by"></span></div>
        <div id="stats"></div>
        <div id="hits" class="block-group"></div>
        <div id="pagination"></div>
    </div>
{/block}
