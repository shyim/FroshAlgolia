<?php

return [
    'sortableAttributes' => [
        [
            'name' => 'number'
        ],
        [
            'name' => 'name'
        ],
        [
            'name' => 'manufacturerName'
        ],
        [
            'name' => 'ean'
        ],
        [
            'name' => 'properties'
        ],
        [
            'name' => 'description'
        ],
        [
            'name' => 'categories'
        ]
    ],
    'rankingIndexAttributes' => [
        [
            'name' => 'sales',
            'sort' => 'desc'
        ],
        [
            'name' => 'price',
            'sort' => 'asc'
        ]
    ],
    'facetFilterWidget' => '{"properties.Farbe":{"widgetType":"refinementList","match":"or"},"properties.Flaschengröße":{"widgetType":"rangeSlider"},"properties.Geschmack":{"widgetType":"refinementList","match":"or"},"properties.Trinktemperatur":{"widgetType":"refinementList","match":"or"},"properties.Alkoholgehalt":{"widgetType":"numericRefinementList","options":[{"name":"< 10%","start":"0","end":"10"},{"name":"10% - 20%","start":"10","end":"20"},{"name":"> 20%","start":"20"}]},"manufacturerName":{"widgetType":"refinementList","match":"or"},"categories":{"widgetType":"refinementList","match":"or"},"price":{"widgetType":"rangeSlider"}}',
    'blockedAttributes' => [
        [
            'name' => 'id'
        ],
        [
            'name' => 'articleID'
        ],
        [
            'name' => 'articledetailsID'
        ]
    ]
];