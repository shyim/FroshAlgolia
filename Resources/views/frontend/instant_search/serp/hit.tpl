{literal}
<script type="text/html" id="hit-template">

    <div class="product--box box--minimal" data-ordernumber="{{number}}">
        <div class="box--content is--rounded">

            <!-- Product image -->
            <a link="{{link}}" title="{{name}}" class="product--image">
                <span class="image--element">
                    <span class="image--media">
                        <img sourceset="{{#helpers.image}}{{image}}{{/helpers.image}}">
                    </span>
                </span>
            </a>
            <!-- /Product image -->


            <!-- Customer rating for the product -->
            <div class="product--rating-container">
                <span class="product--rating">
                    {{# stars}}
                        <i class="icon--star"></i>
                    {{/stars}}
                </span>
            </div>

            <!-- Product text -->
            <div class="product--info">
                <a link="{{link}}" class="product--title"
                   title="{{{_highlightResult.name.value}}}">{{{_highlightResult.name.value}}}</a>
                <!--<div class="product--description">
                    {{{_highlightResult.description.value}}}
                </div>-->
                <div class="product--price-info">
                    <div class="product--price">
                        <span class="price--default is--nowrap">{{#helpers.price}}{{price}}{{/helpers.price}}</span>
                    </div>
                </div>
                <div class="product--actions" data-compare-products="true">{/literal}
                    {if {config name="compareShow"}}
                        <form action="{url controller='compare' action='add_article'}?articleID={literal}{{articleId}}{/literal}" method="post">
                            <button type="submit"
                                    title="{s namespace='frontend/listing/box_article' name='ListingBoxLinkCompare'}{/s}"
                                    class="product--action action--compare"
                                    data-product-compare-add="true">
                                <i class="icon--compare"></i> {s namespace='frontend/listing/box_article' name='ListingBoxLinkCompare'}{/s}
                            </button>
                        </form>
                    {/if}

                    <form action="{url controller='note' action='add'}?ordernumber={literal}{{number}}{/literal}" method="post">
                        <button type="submit"
                                title="{"{s name='DetailLinkNotepad' namespace='frontend/detail/actions'}{/s}"|escape}"
                        class="product--action action--note"
                        data-ajaxUrl="{url controller='note' action='ajaxAdd'}?ordernumber={literal}{{number}}{/literal}"
                        data-text="{s name="DetailNotepadMarked"}{/s}">
                        <i class="icon--heart"></i> <span class="action--text">{s name="DetailLinkNotepadShort" namespace="frontend/detail/actions"}{/s}</span>
                        </button>
                    </form>
                    {literal}
                </div>
            </div>
        </div>
    </div>

</script>
{/literal}
