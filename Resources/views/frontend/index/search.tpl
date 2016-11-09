{extends file="parent:frontend/index/search.tpl"}

{* Search results *}
{block name='frontend_index_search_results'}

    {literal}
    <script language="JavaScript">
        var client = algoliasearch('{/literal}{$algoliaApplicationId}{literal}', '{/literal}{$algoliaSearchOnlyApiKey}{literal}');
        var index = client.initIndex('{/literal}{$indexName}{literal}');

        autocomplete('input[name=sSearch]', {
            hint: false,
            templates: {
                dropdownMenu: '#my-custom-menu-template',
                {/literal}{if $showAlgoliaLogo}footer: '<div class="branding">Powered by <img src="https://www.algolia.com/assets/algolia128x40.png" /></div>'{/if}{literal}
            }
        }, [
            {
                source: autocomplete.sources.hits(index, { hitsPerPage: 5 }),
                displayKey: 'name',
                templates: {
                    suggestion: function(suggestion) {
                        return suggestion._highlightResult.name.value;
                    }
                }
            }
        ]).on('autocomplete:selected', function(event, suggestion, dataset) {
            window.location.href = suggestion.link;
        });

    </script>
    {/literal}
{/block}