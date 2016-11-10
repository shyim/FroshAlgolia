{extends file="parent:frontend/index/search.tpl"}

{* Search field *}
{block name='frontend_index_search_field'}
    <div class="aa-input-container" id="aa-input-container">
        <input type="search" id="aa-search-input" class="aa-input-search" placeholder="{s name="IndexSearchFieldPlaceholder"}{/s}" name="sSearch" autocomplete="off" />
        <svg class="aa-input-icon" viewBox="654 -372 1664 1664">
            <path d="M1806,332c0-123.3-43.8-228.8-131.5-316.5C1586.8-72.2,1481.3-116,1358-116s-228.8,43.8-316.5,131.5  C953.8,103.2,910,208.7,910,332s43.8,228.8,131.5,316.5C1129.2,736.2,1234.7,780,1358,780s228.8-43.8,316.5-131.5  C1762.2,560.8,1806,455.3,1806,332z M2318,1164c0,34.7-12.7,64.7-38,90s-55.3,38-90,38c-36,0-66-12.7-90-38l-343-342  c-119.3,82.7-252.3,124-399,124c-95.3,0-186.5-18.5-273.5-55.5s-162-87-225-150s-113-138-150-225S654,427.3,654,332  s18.5-186.5,55.5-273.5s87-162,150-225s138-113,225-150S1262.7-372,1358-372s186.5,18.5,273.5,55.5s162,87,225,150s113,138,150,225  S2062,236.7,2062,332c0,146.7-41.3,279.7-124,399l343,343C2305.7,1098.7,2318,1128.7,2318,1164z" />
        </svg>
        <svg class="aa-input-close" id="icon-close" viewBox="0 0 26 25">
            <polygon points="26.2,23 15.4,12.5 26.2,2 23.9,-0.4 13,10.2 2.1,-0.4 -0.2,2 10.6,12.5 -0.2,23 2.1,25.4 13,14.8     23.9,25.4" />
        </svg>
    </div>
{/block}

{* Search submit *}
{block name='frontend_index_search_field_submit'}{/block}

{* Search results *}
{block name='frontend_index_search_results'}

    {literal}
    <script language="JavaScript">

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

        function escapeHTML (string) {
            return String(string).replace(/[&<>"'`=\/]/g, function fromEntityMap (s) {
                return entityMap[s];
            });
        }

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
                {/literal}{if $showAlgoliaLogo}footer: '<div class="branding"><span>Powered by</span> <img src="https://www.algolia.com/assets/algolia128x40.png" /></div>',{/if}{literal}
                suggestion: function(suggestion) {

                    var image = stripTags(suggestion.image);
                    var name = suggestion._highlightResult.name.value;
                    var currencySymbol = suggestion.currencySymbol;
                    var price = suggestion.price;
                    var number = suggestion._highlightResult.number.value;
                    var link = suggestion.link;

                    // render the suggestion
                    //var res = '<div class="aa-suggestion">';
                    var res = '';

                    /// show the image
//                    if (image !== '') {
//                        res += '<span><img src="' + escapeHTML(suggestion.image) + '" alt="' + escapeHTML(stripTags(suggestion.name)) +'" /></span>';
//                    }

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

                    // show number
//                    if (number !== '') {
//                        if (number && number.trim() !== '') {
//                            res += '<span class="number">';
//                            res += stripTags(number);
//                            res += '</span>';
//                        }
//                    }

                    // show price
                    if (price !== '') {
                        res += '<span class="price">';
                        res += currencySymbol + ' ' + stripTags(price);
                        res += '</span>';
                    }

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

    </script>
    {/literal}
{/block}