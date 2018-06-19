<?php

return [
    'searchAttributes' => [
        [
            'name' => 'number',
        ],
        [
            'name' => 'name',
        ],
        [
            'name' => 'manufacturerName',
        ],
        [
            'name' => 'ean',
        ],
        [
            'name' => 'properties',
        ],
        [
            'name' => 'description',
        ],
        [
            'name' => 'categories',
        ],
    ],
    'rankingIndexAttributes' => [
        [
            'name' => 'sales',
            'sort' => 'desc',
        ],
        [
            'name' => 'price',
            'sort' => 'asc',
        ],
    ],
    'facetFilterWidget' => [
        'categories' => ['widgetType' => 'hierarchicalMenu', 'attributes' => ['categories.lvl0', 'categories.lvl1', 'categories.lvl2'], 'header' => 'Kategorien'],
        'properties.Farbe' => ['widgetType' => 'refinementList', 'match' => 'or', 'header' => 'Farbe'],
        'properties.Flaschengröße' => ['widgetType' => 'rangeSlider', 'header' => 'Flaschengröße'],
        'properties.Geschmack' => ['widgetType' => 'refinementList', 'match' => 'or', 'header' => 'Geschmack'],
        'properties.Trinktemperatur' => ['widgetType' => 'refinementList', 'match' => 'or', 'header' => 'Trinktemperatur'],
        'properties.Alkoholgehalt' => ['widgetType' => 'numericRefinementList', 'options' => [0 => ['name' => '< 10%', 'start' => '0', 'end' => '10',], 1 => ['name' => '10% - 20%', 'start' => '10', 'end' => '20',], 2 => ['name' => '> 20%', 'start' => '20',],], 'header' => 'Alkoholgehalt',],
        'manufacturerName' => ['widgetType' => 'refinementList', 'match' => 'or', 'header' => 'Hersteller'],
        'price' => ['widgetType' => 'rangeSlider', 'header' => 'Preis']
    ],
    'blockedAttributes' => [
        [
            'name' => 'id',
        ],
        [
            'name' => 'articleID',
        ],
        [
            'name' => 'articledetailsID',
        ],
    ],
    'facetAttributes' => [
        [
            'name' => 'categories',
        ],
        [
            'name' => 'manufacturerName',
        ],
        [
            'name' => 'price',
        ],
        [
            'name' => 'properties',
        ],
    ],
];
