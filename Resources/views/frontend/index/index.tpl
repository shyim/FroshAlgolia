{extends file="parent:frontend/index/index.tpl"}

{block name="frontend_index_header_javascript_jquery_lib"}
    {$smarty.block.parent}

{literal}
    <script language="JavaScript">
        document.asyncReady(function() {

        // strip HTML tags + keep <em>, <p>, <b>, <i>, <u>, <strong>
        function stripTags(v) {
            return $('<textarea />').text(v).html()
                .replace(/&lt;(\/)?(em|p|b|i|u|strong)&gt;/g, '<$1$2>');
        }

        var entityMap = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#39;',
            '/': '&#x2F;',
            '`': '&#x60;',
            '=': '&#x3D;'
        };

        //        function escapeHTML (string) {
        //            return String(string).replace(/[&<>"'`=\/]/g, function fromEntityMap (s) {
        //                return entityMap[s];
        //            });
        //        }

        var client = algoliasearch('{/literal}{$algoliaApplicationId}{literal}', '{/literal}{$algoliaSearchOnlyApiKey}{literal}');
        var index = client.initIndex('{/literal}{$indexName}{literal}');

        //DOM Binding
        var searchInput = document.getElementById("aa-search-input");
        var inputContainer = document.getElementById("aa-input-container");

        autocomplete('input[id=aa-search-input]', {
                hint: false
            },{
                source: autocomplete.sources.hits(index, {hitsPerPage: 10}),
                displayKey: 'name',
                templates: {
                    {/literal}{if $showAlgoliaLogo}footer: '<div class="branding"><span><a href="https://github.com/shyim/FroshAlgolia" target="_blank">FroshAlgolia Plugin by FriendsOfShopware</a></span><span>Powered by <img src="https://www.algolia.com/assets/algolia128x40.png" /></span> </div>',{/if}{literal}
                    suggestion: function(suggestion) {

                        var name = suggestion._highlightResult.name.value;
                        var currencySymbol = suggestion.currencySymbol;
                        var price = suggestion.price;
                        // var link = suggestion.link;
                        // Remove Hostname from link to avoid issues with shopware inputFilter
                        var link = suggestion.link.replace(/^.*\/\/[^\/]+\//, '');

                        // render the suggestion
                        var res = '';

                        // show name
                        if (name !== '') {
                            res += '<span class="name">';
                            if (link !== '') {
                                res += '<a href="' + stripTags(link) + '">';
                                res += stripTags(name);
                                res += '</a>';
                            } else {
                                res += stripTags(name);
                            }
                            res += '</span>';
                        }

                        // show price
                        {/literal}
                        {if $showAutocompletePrice}
                        if (price !== '') {
                            res += '<span class="price">';
                            res += stripTags(suggestion.priceFormatted) + ' ' +  currencySymbol;
                            res += '</span>';
                        }
                        {/if}
                        {literal}

                        return res;
                    }
                }
            }
        ).on('autocomplete:updated', function() {
            if (searchInput.value.length > 0) {
                inputContainer.classList.add("input-has-value");
            }
            else {
                inputContainer.classList.remove("input-has-value");
            }
        }).on('autocomplete:selected', function(event, suggestion, dataset) {
            window.location.href = suggestion.link;
        });

        // Handle clearing the search input on close icon click
        document.getElementById("icon-close").addEventListener("click", function() {
            searchInput.value = "";
            inputContainer.classList.remove("input-has-value");
        });

        });
    </script>
{/literal}
{/block}