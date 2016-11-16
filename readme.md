SwAlgolia - Algolia plugin for Shopware
=====
This plugin integrates the high-performance search cluster [Algolia](https://www.algolia.com/) in your [Shopware](https://www.shopware.de) system.

Algolia Search allows full-text searches over tons of records within microseconds. This ensures a great UX for your customers and high click-through rates on your search results.

See it in action
-----
Blazing fast as-you-type auto-suggestion with keyboard navigation:

![SwAlgolia auto suggestion](https://github.com/synonymous1984/SwAlgolia/blob/master/Documentation/images/screencast_autosuggest.gif "SwAlgolia auto-suggest as-you-type")

Requirements
-----
* Shopware >= 5.2.5

Pushing article data to Algolia index
====
To push your article data to the algolia index, run the following command:

* Full index update: `php bin/console swalgoliasync full`

Multiple shops / Multilanguage shops
=====
The SwAlgolia plugin creates one Algolia index for each of your active shops. So if you have two shops (e.g. German and English), 
those shops would generate two indices following this naming convention:

`<prefix>-<shopId>`. 

The prefix can be defined in the plugin config, the shopId is added automatically.

Article attributes
=====
By default *all article attributes* (default and plugin-added) are pushed to Algolia Index. You can block attributes from being
transmitted by adding their names in the plugin configuration under *Blocked article attributes*. Add all attribute names here and 
delimit them with a colon.

Article properties and instant search faceting
=====
All article properties are published to Algolia index by default. If an option is marked as *filterable* it will be automatically added to the filter-sidebar on the instant search page. If you don´t want that a filter is shown there, simply remove the *filterable flag* from the option and recompile your theme.

Glossary
=====
* **SERP** is the synonym for "search engine result page" and in this plugin context SERP means the full search page that a user can access by entering a search-term in the main search box and presses enter.
* **List** means a list of products.
* **Auto-Suggest** means the ability to suggest matching results below the search field as soon as the user starts typing.