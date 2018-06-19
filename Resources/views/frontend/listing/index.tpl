{extends file="parent:frontend/listing/index.tpl"}

{block name="frontend_listing_top_actions"}{/block}
{block name="frontend_listing_bottom_paging"}{/block}

{block name="frontend_index_left_inner"}
    {$smarty.block.parent}

    {foreach $algoliaConfig.facetFilterWidget as $facetName => $facetConfig}
        <div id="{$facetName|replace:'.':'_'|lower}" class="facet"></div>
    {/foreach}
{/block}

{block name="frontend_listing_listing_content"}
    {* Include hogan.js template files *}
    {include file='frontend/instant_search/serp/hit.tpl'}
    {include file='frontend/instant_search/serp/no-result.tpl'}
    {include file='frontend/instant_search/serp/stat.tpl'}

    <div class="algolia--container"
         data-algolia="true"
         data-hitTemplateFile="hitTemplateFile"
         data-appId="{$algoliaApplicationId}"
         data-apiKey="{$algoliaSearchOnlyApiKey}"
         data-indexName="{$indexName}"
         data-noImage="{link file='frontend/_public/src/img/no-picture.jpg'}"
         data-currentCategory="{$sCategoryContent.name}"
         data-sortOrderIndex="{$sortOrderIndex}"
         data-facetWidgetsConfig='{$algoliaConfig.facetFilterWidget|@json_encode}'>
        <div class="listing--wrapper">

            <div data-listing-actions="true" class="listing--actions is--rounded without-pagination">
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
                    <div class="action--per-page action--content block">
                        <label for="n" class="per-page--label action--label">Artikel pro Seite:</label>
                        <div id="hits-per-page"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
{/block}
