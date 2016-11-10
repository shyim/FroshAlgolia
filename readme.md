Algolia for Shopware
=====
This plugin integrates the high-performance search cluster [Algolia](https://www.algolia.com/) in your Shopware system.

Algolia Search allows full-text searches over tons of records within microseconds. This ensures a great UX for your customers and high click-through rates on your search results.

Algolia credentials for development
-----
* **Application ID**: 47WGJ9ERN3
* **Search-Only API Key**: ea33799ba0afd8de30e5c73163a72c70

Requirements
-----
* Shopware >= 5.2.5

Pushing article data to Algolia index
====
To push your article data to the algolia index, run the following command:

* Full index update: `php bin/console algoliasync full`

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

Article properties
=====
All article properties are published to Algolia index.