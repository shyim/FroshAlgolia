{literal}
<script type="text/html" id="hit-template">

    <div class="product--box box--basic" data-ordernumber="{{number}}">
        <div class="box--content is--rounded">
            <a link="{{link}}" title="{{name}}" class="product--image">
                <span class="image--element">
                    <span class="image--media">
                        <img sourceset="{{#helpers.image}}{{image}}{{/helpers.image}}">
                    </span>
                </span>
            </a>
            <div class="product--info">
                <a link="{{link}}" class="product--title"
                   title="{{{_highlightResult.name.value}}}">{{{_highlightResult.name.value}}}</a>
                <div class="product--description">
                    {{{_highlightResult.description.value}}}
                </div>
                <div class="product--price-info">
                    <div class="product--price">
                        <span class="price--default is--nowrap">{{#helpers.price}}{{price}}{{/helpers.price}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</script>
{/literal}