{literal}
<div class="product--box box--minimal" data-ordernumber="{{number}}">
    <div class="box--content is--rounded">
        <div class="product--badges"></div>

        <div class="product--info">
            <a link="{{link}}" title="{{name}}" class="product--image">
                    <span class="image--element">
                        <span class="image--media">
                            {{#image}}
                            <img sourceset="{{#helpers.image}}{{image}}{{/helpers.image}}" alt="{{{_highlightResult.name.value}}}" title="{{{_highlightResult.name.value}}}" />
                            {{/image}}
                            {{^image}}
                            <img src="{/literal}{link file='frontend/_public/src/img/no-picture.jpg'}{literal}" alt="{{{_highlightResult.name.value}}}" title="{{{_highlightResult.name.value}}}" />
                            {{/image}}
                        </span>
                    </span>
            </a>

            <!-- Customer rating for the product -->
            <div class="product--rating-container">
                {{# stars}}
                <span class="product--rating">
                                <i class="icon--star"></i>
                        </span>
                {{/stars}}
            </div>

            <a link="{{link}}" class="product--title"
               title="{{{_highlightResult.name.value}}}">{{{_highlightResult.name.value}}}</a>

            <div class="product--price-info">
                <div class="price--unit"></div>
                <div class="product--price-outer">
                    <div class="product--price">
                        <span class="price--default is--nowrap">{{#helpers.price}}{{price}}{{/helpers.price}}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
{/literal}